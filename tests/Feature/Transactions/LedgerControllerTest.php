<?php

namespace Tests\Feature\Transactions;

use App\Models\Transactions\Ledger;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class LedgerControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_ledgers(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'ledger.index')->where('guard_name', 'sanctum')->first());

        Ledger::factory()->count(3)->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.ledger.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'code', 'in', 'out', 'total'],
                ],
            ]);
    }

    public function test_show_returns_ledger(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'ledger.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'ledger.show')->where('guard_name', 'sanctum')->first());

        $ledger = Ledger::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.ledger.show', $ledger->id));

        $response->assertStatus(200)
            ->assertJson([
                'id' => $ledger->id,
                'code' => $ledger->code,
            ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'ledger.index',
            'ledger.show',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'ledger']);
        }
    }
}
