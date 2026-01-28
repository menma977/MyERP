<?php

namespace App\Services;

use App\Models\FileBucket;
use App\Models\SecurityCheck;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Encoders\AutoEncoder;
use Intervention\Image\ImageManager;
use Throwable;
use wapmorgan\FileTypeDetector\Detector;

use function escapeshellarg;
use function escapeshellcmd;
use function file_exists;
use function file_get_contents;
use function filesize;
use function function_exists;
use function get_class;
use function implode;
use function is_resource;
use function preg_replace;
use function stripos;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

class FileBucketService
{
    private const string GS_CACHE_KEY = 'ghostscript:binary';

    private const int GS_CACHE_TTL = 86400;

    /**
     * Store a file in the storage and create a FileBucket record.
     *
     * @param  string|null  $name  Custom filename (default: null, generates ULID)
     * @param  Model  $model  The model to associate the file with
     * @param  array<string, mixed>  $data  Additional data to store with the file
     * @param  array<int, string>  $tags  Tags to associate with the file
     * @param  string  $path  Storage path
     * @return FileBucket|null File information including name, path, url, extension, size, type, and original name
     */
    public static function store(?string $name, Model $model, array $data = [], array $tags = [], string $path = 'tmp'): ?FileBucket
    {
        $request = request();

        if (! $request->has('file')) {
            return null;
        }

        $request->validate([
            'file' => ['required', 'file', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $file = $request->file('file');
        $index = Str::ulid();
        $extension = $file->getClientOriginalExtension();

        if (! $name) {
            $name = Str::ulid();
        }

        $modelId = $model->getKey();
        if (! $modelId) {
            throw ValidationException::withMessages([
                'message' => trans('messages.fail.action.cost', ['action' => 'Upload', 'attribute' => 'File', 'target' => 'Model'], App::getLocale()),
            ]);
        }

        $baseName = "$name-$modelId-$index.$extension";
        $mime = $file->getMimeType() ?? '0';
        $detected = Detector::detectByContent($file->getPathname()) ?? Detector::detectByFilename($file->getClientOriginalName());
        $isImage = $detected && $detected[0] === Detector::IMAGE || Str::startsWith($mime, 'image/');
        $isSvg = Str::lower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION)) === 'svg' || $mime === 'image/svg+xml';
        $isPdf = $detected && $detected[1] === Detector::PDF || $mime === 'application/pdf';

        $newName = $baseName;
        $newExtension = $extension;
        $newMime = $mime;

        if ($isImage || $isSvg) {
            [$newName, $newExtension, $newMime] = self::recycledImage($file, $path, $baseName, $isSvg);
        } elseif ($isPdf) {
            [$newName, $newExtension, $newMime] = self::recycledPDF($file, $path, $baseName);
        } else {
            Storage::disk('public')->putFileAs($path, $file, $baseName);
        }

        $fileBucket = new FileBucket;
        $fileBucket->name = $name;
        $fileBucket->path = "$path/$newName";
        $fileBucket->mime_type = $newMime;
        $fileBucket->mime = $newMime;
        $fileBucket->extension = $newExtension;
        $fileBucket->model_type = get_class($model);
        $fileBucket->model_id = (string) $modelId;
        $fileBucket->tags = collect($tags);
        $fileBucket->data = collect($data);
        $fileBucket->save();

        return $fileBucket;
    }

    /**
     * @return array{0: string, 1: string, 2: string|null}
     */
    private static function recycledImage(UploadedFile $file, string $path, string $name, bool $isSvg = false): array
    {
        $extension = $file->extension();
        $mime = $file->getMimeType();

        if ($isSvg) {
            $contents = @file_get_contents($file->getPathname());
            if ($contents !== false) {
                $contents = (string) preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $contents);
                $contents = (string) preg_replace('/on\w+\s*=\s*(?:"[^"]*"|\'[^\']*\')/i', '', $contents);
                $extension = 'svg';
                $mime = 'image/svg+xml';
                Storage::disk('public')->put("$path/$name", $contents);
            } else {
                Storage::disk('public')->putFileAs($path, $file, $name);
            }

            return [$name, $extension, $mime];
        }

        try {
            $manager = ImageManager::gd();
            $image = $manager->read($file->getPathname());

            $image->orient();

            $stored = false;

            try {
                $stream = $image->encode(new AutoEncoder);
                Storage::disk('public')->put("$path/$name", (string) $stream);
                $mime = $file->getMimeType();
                $stored = true;
            } catch (Throwable $encodeError) {
                Log::error($encodeError->getMessage(), ['trace' => $encodeError->getTraceAsString()]);
            }

            if (! $stored) {
                Storage::disk('public')->putFileAs($path, $file, $name);
                $extension = $file->extension();
                $mime = $file->getMimeType();
            }

            return [$name, $extension, $mime];
        } catch (Throwable $error) {
            Log::error($error->getMessage(), ['trace' => $error->getTraceAsString()]);
            Storage::disk('public')->putFileAs($path, $file, $name);

            return [$name, $file->extension(), $file->getMimeType()];
        }
    }

    /**
     * @return array{0: string, 1: string, 2: string|null}
     */
    private static function recycledPDF(UploadedFile $file, string $path, string $name): array
    {
        $newExtension = $file->extension();
        $newMime = $file->getMimeType();

        $inputPath = $file->getPathname();

        $tmpOut = tempnam(sys_get_temp_dir(), 'pdf');
        if ($tmpOut === false) {
            Storage::disk('public')->putFileAs($path, $file, $name);

            return [$name, $newExtension, $newMime];
        }
        $tmpOutPdf = $tmpOut.'.pdf';

        $binaries = ['gs', 'gswin64c', 'gswin32c'];

        $gs = Cache::get(self::GS_CACHE_KEY);

        if ($gs === null) {
            if (! self::canExecuteCommands()) {
                SecurityCheck::check('ghostscript', false);
                Log::warning('Shell execution disabled: cannot detect Ghostscript', ['disable_functions' => ini_get('disable_functions')]);
                Cache::put(self::GS_CACHE_KEY, null, self::GS_CACHE_TTL);
                Storage::disk('public')->putFileAs($path, $file, $name);

                return [$name, $newExtension, $newMime];
            }

            foreach ($binaries as $bin) {
                $cmd = escapeshellcmd($bin).' -v 2>&1';
                $res = self::runCommand($cmd);
                $output = $res['output'];
                $ret = $res['return'];
                $joined = implode("\n", $output);
                Log::debug('Detecting Ghostscript', ['bin' => $bin, 'used' => $res['used'], 'return' => $ret, 'output' => $output]);
                if ($ret === 0 || stripos($joined, 'ghostscript') !== false) {
                    $gs = $bin;
                    break;
                }
            }

            Cache::put(self::GS_CACHE_KEY, $gs, self::GS_CACHE_TTL);
        } else {
            Log::debug('Using cached Ghostscript binary', ['bin' => $gs]);
        }

        try {
            SecurityCheck::check('ghostscript', $gs !== null);

            if (! $gs) {
                Storage::disk('public')->putFileAs($path, $file, $name);

                return [$name, $newExtension, $newMime];
            }

            $cmd =
                escapeshellarg($gs)
                .' -dSAFER -dBATCH -dNOPAUSE -dCompatibilityLevel=1.4'
                .' -sDEVICE=pdfwrite -dPDFSETTINGS=/printer'
                .' -sOutputFile='.escapeshellarg($tmpOutPdf)
                .' -f '.escapeshellarg($inputPath)
                .' 2>&1';

            $res = self::runCommand($cmd);
            $out = $res['output'];
            $ret = $res['return'];
            Log::debug('Ghostscript executed', ['cmd' => $cmd, 'used' => $res['used'], 'output' => $out, 'return' => $ret]);

            if ($ret === 0 && file_exists($tmpOutPdf) && filesize($tmpOutPdf) > 0) {
                $pdfContents = file_get_contents($tmpOutPdf);
                if ($pdfContents !== false) {
                    Storage::disk('public')->put("$path/$name", $pdfContents);
                    $newExtension = 'pdf';
                    $newMime = 'application/pdf';
                } else {
                    Storage::disk('public')->putFileAs($path, $file, $name);
                }
            } else {
                Cache::forget(self::GS_CACHE_KEY);
                Log::warning('Ghostscript conversion failed; invalidated cached binary', ['cmd' => $cmd, 'used' => $res['used'], 'output' => $out, 'return' => $ret]);
                Storage::disk('public')->putFileAs($path, $file, $name);
            }
        } catch (Throwable) {
            Storage::disk('public')->putFileAs($path, $file, $name);
        } finally {
            @unlink($tmpOutPdf);
            @unlink($tmpOut);
        }

        return [$name, $newExtension, $newMime];
    }

    private static function canExecuteCommands(): bool
    {
        return function_exists('exec') || function_exists('shell_exec') || function_exists('proc_open') || function_exists('popen');
    }

    /**
     * Execute a shell command using the best available PHP function.
     * Tries exec -> shell_exec -> proc_open -> popen and returns the output,
     * return code (if available), and which function was used.
     *
     * @return array{output:array<int, string>,return:int|null,used:string}
     */
    private static function runCommand(string $cmd): array
    {
        if (function_exists('exec')) {
            /** @var array<int, string> $out */
            $out = [];
            $ret = null;
            exec($cmd, $out, $ret);

            return ['output' => $out, 'return' => (int) $ret, 'used' => 'exec'];
        }

        if (function_exists('shell_exec')) {
            $res = shell_exec($cmd);
            /** @var array<int, string> $out */
            $out = $res === null || $res === false ? [] : (preg_split("/\r\n|\n|\r/", trim($res)) ?: []);

            return ['output' => $out, 'return' => null, 'used' => 'shell_exec'];
        }

        if (function_exists('proc_open')) {
            $descriptors = [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ];
            $process = @proc_open($cmd, $descriptors, $pipes);
            if (is_resource($process)) {
                @fclose($pipes[0]);
                $stdout = (string) @stream_get_contents($pipes[1]);
                @fclose($pipes[1]);
                $stderr = (string) @stream_get_contents($pipes[2]);
                @fclose($pipes[2]);
                $ret = proc_close($process);
                $out = [];
                if ($stdout !== '') {
                    $splitStdout = preg_split("/\r\n|\n|\r/", trim($stdout));
                    if ($splitStdout !== false) {
                        $out = array_merge($out, $splitStdout);
                    }
                }
                if ($stderr !== '') {
                    $splitStderr = preg_split("/\r\n|\n|\r/", trim($stderr));
                    if ($splitStderr !== false) {
                        $out = array_merge($out, $splitStderr);
                    }
                }

                return ['output' => $out, 'return' => $ret, 'used' => 'proc_open'];
            }
        }

        if (function_exists('popen')) {
            $handle = @popen($cmd, 'r');
            if ($handle !== false) {
                $res = stream_get_contents($handle);
                pclose($handle);
                /** @var array<int, string> $out */
                $out = $res === false ? [] : (preg_split("/\r\n|\n|\r/", trim($res)) ?: []);

                return ['output' => $out, 'return' => null, 'used' => 'popen'];
            }
        }

        return ['output' => [], 'return' => null, 'used' => 'none'];
    }
}
