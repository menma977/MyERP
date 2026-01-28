<?php

namespace Tests\Feature\Companies;

use App\Models\Companies\Company;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CompanyControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_companies(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Permission $permission */
        $permission = Permission::where('name', 'company.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permission);

        Company::factory()->count(3)->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.company.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'code', 'phone', 'email'],
                ],
            ]);
    }

    public function test_show_returns_company(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Permission $permIndex */
        $permIndex = Permission::where('name', 'company.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permIndex);
        /** @var Permission $permShow */
        $permShow = Permission::where('name', 'company.show')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permShow);

        /** @var Company $company */
        $company = Company::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.company.show', $company->ulid));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $company->ulid,
                    'name' => $company->name,
                ],
            ]);
    }

    public function test_store_creates_company(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Permission $permIndex */
        $permIndex = Permission::where('name', 'company.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permIndex);
        /** @var Permission $permStore */
        $permStore = Permission::where('name', 'company.store')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permStore);

        $array = Company::factory()->make()->toArray();
        unset($array['id'], $array['created_at'], $array['updated_at'], $array['deleted_at'], $array['ulid'], $array['created_by'], $array['updated_by'], $array['deleted_by']);

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.company.store'), $array);

        $response->assertStatus(200);

        $this->assertDatabaseHas('companies', ['code' => $array['code'], 'name' => $array['name']]);
    }

    public function test_store_creates_company_with_users(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Permission $permIndex */
        $permIndex = Permission::where('name', 'company.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permIndex);
        /** @var Permission $permStore */
        $permStore = Permission::where('name', 'company.store')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permStore);

        $users = User::factory()->count(2)->create();
        $array = Company::factory()->make()->toArray();
        $array['users'] = $users->pluck('ulid')->toArray();
        unset($array['id'], $array['created_at'], $array['updated_at'], $array['deleted_at'], $array['ulid'], $array['created_by'], $array['updated_by'], $array['deleted_by']);

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.company.store'), $array);

        $response->assertStatus(200);

        $this->assertDatabaseHas('companies', ['code' => $array['code'], 'name' => $array['name']]);
        $company = Company::where('code', $array['code'])->firstOrFail();
        $this->assertCount(3, $company->users); // 2 added users + 1 current user (creator)
    }

    public function test_update_updates_company(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Permission $permIndex */
        $permIndex = Permission::where('name', 'company.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permIndex);
        /** @var Permission $permUpdate */
        $permUpdate = Permission::where('name', 'company.update')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permUpdate);

        /** @var Company $company */
        $company = Company::factory()->create();
        $newData = Company::factory()->make()->toArray();
        unset($newData['id'], $newData['created_at'], $newData['updated_at'], $newData['deleted_at'], $newData['ulid'], $newData['created_by'], $newData['updated_by'], $newData['deleted_by']);

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('api.v1.company.update', $company->ulid), $newData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('companies', ['id' => $company->id, 'name' => $newData['name']]);
    }

    public function test_update_updates_company_with_users(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Permission $permIndex */
        $permIndex = Permission::where('name', 'company.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permIndex);
        /** @var Permission $permUpdate */
        $permUpdate = Permission::where('name', 'company.update')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permUpdate);

        /** @var Company $company */
        $company = Company::factory()->create();
        $users = User::factory()->count(2)->create();

        $newData = Company::factory()->make()->toArray();
        $newData['users'] = $users->pluck('ulid')->toArray();
        unset($newData['id'], $newData['created_at'], $newData['updated_at'], $newData['deleted_at'], $newData['ulid'], $newData['created_by'], $newData['updated_by'], $newData['deleted_by']);

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('api.v1.company.update', $company->ulid), $newData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('companies', ['id' => $company->id, 'name' => $newData['name']]);
        $freshCompany = $company->fresh();
        $this->assertNotNull($freshCompany);
        $this->assertCount(2, $freshCompany->users);
    }

    public function test_delete_soft_deletes_company(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Permission $permIndex */
        $permIndex = Permission::where('name', 'company.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permIndex);
        /** @var Permission $permDelete */
        $permDelete = Permission::where('name', 'company.delete')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permDelete);

        /** @var Company $company */
        $company = Company::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.company.delete', $company->ulid));

        $response->assertStatus(200);
        $this->assertSoftDeleted('companies', ['id' => $company->id]);
    }

    public function test_restore_restores_company(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Permission $permIndex */
        $permIndex = Permission::where('name', 'company.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permIndex);
        /** @var Permission $permRestore */
        $permRestore = Permission::where('name', 'company.restore')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permRestore);

        /** @var Company $company */
        $company = Company::factory()->create();
        $company->delete();

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.company.restore', $company->ulid));

        $response->assertStatus(200);
        $this->assertNotSoftDeleted('companies', ['id' => $company->id]);
    }

    public function test_destroy_force_deletes_company(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Permission $permIndex */
        $permIndex = Permission::where('name', 'company.index')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permIndex);
        /** @var Permission $permDestroy */
        $permDestroy = Permission::where('name', 'company.destroy')->where('guard_name', 'sanctum')->firstOrFail();
        $user->givePermissionTo($permDestroy);

        /** @var Company $company */
        $company = Company::factory()->create();
        $company->delete();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.company.destroy', $company->ulid));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'company.index',
            'company.show',
            'company.store',
            'company.update',
            'company.delete',
            'company.restore',
            'company.destroy',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web'], ['label' => $permission, 'group' => 'company']);
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'company']);
        }
    }
}
