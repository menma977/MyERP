<?php

namespace App\Http\Controllers\Approvals;

use App\Http\Controllers\Controller;
use App\Models\Approval\ApprovalDictionary;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;

class DictionaryController extends Controller
{
    /**
     * ApprovalDictionary Index
     *
     * Display a listing of the resource.
     */
    /**
     * @return ApprovalDictionary[]|Collection<int, ApprovalDictionary>|LengthAwarePaginator<int, ApprovalDictionary>
     */
    public function index(Request $request)
    {
        $dictionary = ApprovalDictionary::when($request->input('search'), function ($query) use ($request) {
            $query->where('name', 'like', '%'.$request->input('search').'%');
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type') === 'collection') {
            return $dictionary->get();
        }

        return $dictionary->paginate($request->input('per_page', 10));
    }

    /**
     * ApprovalDictionary Store
     *
     * Store a newly created resource in storage.
     */
    /**
     * @return array<string, string>
     */
    public function store(Request $request)
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
        ];
    }

    /**
     * ApprovalDictionary Show
     *
     * Show the specified resource.
     */
    /**
     * @return ApprovalDictionary|Collection<int, ApprovalDictionary>
     */
    public function show(Request $request)
    {
        return ApprovalDictionary::with([
            'components',
        ])->findOrFail($request->route('id'));
    }

    /**
     * ApprovalDictionary Update
     *
     * Update the specified resource in storage.
     */
    /**
     * @return array<string, string>
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'key' => ['required', 'string', 'max:255'],
        ]);

        $dictionary = ApprovalDictionary::findOrFail($request->route('id'));
        if ($dictionary instanceof ApprovalDictionary) {
            $dictionary->name = $request->input('name');
            $dictionary->key = $request->input('key');
            $dictionary->save();
        }

        $dictionaryName = ($dictionary instanceof ApprovalDictionary) ? $dictionary->name : 'Dictionary';

        return [
            'message' => trans('messages.success.update', ['target' => $dictionaryName], App::getLocale()),
        ];
    }

    /**
     * ApprovalDictionary Delete
     *
     * Remove the specified resource from storage.
     */
    /**
     * @return array<string, string>
     */
    public function delete(Request $request)
    {
        $dictionary = ApprovalDictionary::findOrFail($request->route('id'));

        if ($dictionary instanceof ApprovalDictionary && $dictionary->components()->exists()) {
            throw ValidationException::withMessages([
                'message' => trans('messages.fail.delete.cost', ['attribute' => $dictionary->name, 'target' => 'Component'], App::getLocale()),
            ]);
        }

        if ($dictionary instanceof ApprovalDictionary) {
            $dictionary->delete();
        }

        $dictionaryName = ($dictionary instanceof ApprovalDictionary) ? $dictionary->name : 'Dictionary';

        return [
            'message' => trans('messages.success.destroy', ['target' => $dictionaryName], App::getLocale()),
        ];
    }
}
