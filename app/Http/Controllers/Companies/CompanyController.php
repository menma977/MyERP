<?php

namespace App\Http\Controllers\Companies;

use App\Http\Controllers\Controller;
use App\Http\Resources\Companies\CompanyResource;
use App\Models\Companies\Company;
use App\Models\Companies\CompanyHasUser;
use App\Models\User;
use App\Rules\ValidationWithoutTrashed;
use App\Services\FakeIdTranslationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

/**
 * Company Controller
 *
 * Handles CRUD operations for Companies.
 */
class CompanyController extends Controller
{
    /**
     * Company Index
     *
     * Display a listing of the resource.
     *
     * @return LengthAwarePaginator<int, Company>|Collection<int, Company>|JsonResource|int
     */
    public function index(Request $request): LengthAwarePaginator|Collection|JsonResource|int
    {
        $companies = Company::with([
            'logo',
            'customerDefault',
        ])->withCount('customers')->withCount('users')->when($request->input('search'), function (Builder $query) use ($request) {
            return $query->where(function (Builder $query) use ($request) {
                return $query->where('name', 'like', '%'.$request->input('search').'%')
                    ->orWhere('code', 'like', '%'.$request->input('search').'%')
                    ->orWhere('email', 'like', '%'.$request->input('search').'%');
            });
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return CompanyResource::collection($companies->get());
        }

        if ($request->input('type', 'paginate') === 'count') {
            return $companies->count();
        }

        return CompanyResource::collection($companies->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*')));
    }

    /**
     * Company Show
     *
     * Show the specified resource.
     */
    public function show(Request $request): JsonResource
    {
        return Company::with([
            'logo',
            'users',
            'customerDefault',
            'customers',
        ])->withUsers()->where(
            'id',
            FakeIdTranslationService::model(new Company)->key($request->route('id'))->translateUlid()
        )->firstOrFail()->toResource();
    }

    /**
     * Company Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string, company: JsonResource}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', new ValidationWithoutTrashed(Company::class, 'code')],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'address' => ['nullable', 'string'],
            'users' => ['nullable', 'array'],
            'users.*' => ['string', 'exists:users,ulid'],
        ]);

        $company = new Company;
        $this->save($request, $company);

        $userFakeIds = $request->input('users', []);

        /** @var User|null $currentUser */
        $currentUser = Auth::user();
        if ($currentUser) {
            $userFakeIds[] = $currentUser->ulid;
        }

        $this->syncCompanyUsers($company, array_unique($userFakeIds));

        return [
            'message' => trans('messages.success.store', ['target' => 'Company'], App::getLocale()),
            'company' => $company->toResource(),
        ];
    }

    /**
     * Company Update
     *
     * Update the specified resource in storage.
     *
     * @return array{message: string, company: JsonResource}
     */
    public function update(Request $request): array
    {
        $companyId = FakeIdTranslationService::model(new Company)->key($request->route('id'))->translateUlid();
        $company = Company::where('id', $companyId)->firstOrFail();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', new ValidationWithoutTrashed(Company::class, 'code', $companyId)],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'address' => ['nullable', 'string'],
            'users' => ['nullable', 'array'],
            'users.*' => ['string', 'exists:users,ulid'],
        ]);

        $this->save($request, $company);

        if ($request->has('users')) {
            $this->syncCompanyUsers($company, $request->input('users'));
        }

        return [
            'message' => trans('messages.success.update', ['target' => 'Company'], App::getLocale()),
            'company' => $company->toResource(),
        ];
    }

    /**
     * Company Delete
     *
     * Remove the specified resource from storage.
     *
     * @return array{message: string, company: JsonResource}
     */
    public function delete(Request $request): array
    {
        $company = Company::where(
            'id',
            FakeIdTranslationService::model(new Company)->key($request->route('id'))->translateUlid()
        )->firstOrFail();

        $company->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Company'], App::getLocale()),
            'company' => $company->toResource(),
        ];
    }

    /**
     * Company Restore
     *
     * Restore the specified resource from storage.
     *
     * @return array{message: string, company: JsonResource}
     */
    public function restore(Request $request): array
    {
        /** @var Company $company */
        $company = Company::onlyTrashed()->where(
            'id',
            FakeIdTranslationService::model(new Company)->key($request->route('id'))->translateUlid()
        )->firstOrFail();

        $company->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Company'], App::getLocale()),
            'company' => $company->toResource(),
        ];
    }

    /**
     * Company Destroy
     *
     * Permanently remove the specified resource from storage.
     *
     * @return array{message: string, company: JsonResource}
     */
    public function destroy(Request $request): array
    {
        /** @var Company $company */
        $company = Company::onlyTrashed()->where(
            'id',
            FakeIdTranslationService::model(new Company)->key($request->route('id'))->translateUlid()
        )->firstOrFail();

        $company->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Company'], App::getLocale()),
            'company' => $company->toResource(),
        ];
    }

    protected function save(Request $request, Company $company): void
    {
        $company->name = $request->input('name');
        $company->code = $request->input('code');
        $company->phone = $request->input('phone');
        $company->email = $request->input('email');
        $company->website = $request->input('website');
        $company->address = $request->input('address');
        $company->save();

        if ($company->customerDefault()->doesntExist()) {
            $company->makeCustomerDefault();
        }
    }

    /**
     * Sync Users
     *
     * Sync, add, update, or remove users from the company.
     *
     * @param  array<string>  $userFakesId
     */
    private function syncCompanyUsers(Company $company, array $userFakesId): void
    {
        $userIds = User::whereIn('ulid', $userFakesId)->pluck('id')->toArray();

        $existingLinks = $company->hasUsers()->withTrashed()->get();
        $existingUserIds = $existingLinks->pluck('user_id')->toArray();

        $toDelete = array_diff($existingUserIds, $userIds);
        if (! empty($toDelete)) {
            $company->hasUsers()->whereIn('user_id', $toDelete)->delete();
        }

        foreach ($userIds as $userId) {
            $link = $existingLinks->where('user_id', $userId)->first();

            if ($link) {
                if ($link->trashed()) {
                    $link->restore();
                }
            } else {
                $newLink = new CompanyHasUser;
                $newLink->company_id = $company->id;
                $newLink->user_id = $userId;
                $newLink->save();
            }
        }
    }
}
