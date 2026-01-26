<?php

namespace App\Http\Controllers\Approvals;

use App\Http\Controllers\Controller;
use App\Http\Resources\Approval\ApprovalDictionaryResource;
use App\Models\Approval\ApprovalDictionary;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;

class DictionaryController extends Controller
{
    /**
     * ApprovalDictionary Index
     *
     * Display a listing of the resource.
     *
     * @return Collection<int, ApprovalDictionary>|LengthAwarePaginator<int, ApprovalDictionary>|JsonResource|int
     */
    public function index(Request $request): Collection|LengthAwarePaginator|JsonResource|int
    {
        $dictionary = ApprovalDictionary::when($request->input('search'), function ($build) use ($request) {
            return $build->where('name', 'like', '%'.$request->input('search').'%');
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type') === 'collection') {
            return ApprovalDictionaryResource::collection($dictionary->get());
        }

        if ($request->input('type') === 'count') {
            return $dictionary->count();
        }

        return ApprovalDictionaryResource::collection($dictionary->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*')));
    }

    /**
     * ApprovalDictionary Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string, dictionary: JsonResource}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'key' => ['required', 'string', 'max:255'],
        ]);

        $dictionary = new ApprovalDictionary;
        $dictionary->name = $request->input('name');
        $dictionary->key = $request->input('key');
        $dictionary->save();

        return [
            'message' => trans('messages.success.store', ['target' => $dictionary->name], App::getLocale()),
            'dictionary' => $dictionary->toResource(),
        ];
    }

    /**
     * ApprovalDictionary Show
     *
     * Show the specified resource.
     */
    public function show(Request $request): JsonResource
    {
        return ApprovalDictionary::with([
            'components',
        ])->withUsers()->where('id', $request->route('id'))->firstOrFail()->toResource();
    }

    /**
     * ApprovalDictionary Update
     *
     * Update the specified resource in storage.
     *
     * @return array{message: string, dictionary: JsonResource}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'key' => ['required', 'string', 'max:255'],
        ]);

        $dictionary = ApprovalDictionary::where('id', $request->route('id'))->firstOrFail();
        $dictionary->name = $request->input('name');
        $dictionary->key = $request->input('key');
        $dictionary->save();

        return [
            'message' => trans('messages.success.update', ['target' => $dictionary->name], App::getLocale()),
            'dictionary' => $dictionary->toResource(),
        ];
    }

    /**
     * ApprovalDictionary Delete
     *
     * Remove the specified resource from storage.
     *
     * @return array{message: string, dictionary: JsonResource}
     */
    public function delete(Request $request): array
    {
        $dictionary = ApprovalDictionary::where('id', $request->route('id'))->firstOrFail();

        if ($dictionary->components()->exists()) {
            throw ValidationException::withMessages([
                'message' => trans('messages.fail.delete.cost', ['attribute' => $dictionary->name, 'target' => 'Component'], App::getLocale()),
            ]);
        }
        $dictionary->delete();

        return [
            'message' => trans('messages.success.destroy', ['target' => $dictionary->name], App::getLocale()),
            'dictionary' => $dictionary->toResource(),
        ];
    }
}
