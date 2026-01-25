<?php

namespace Tests\Feature\Transactions;

use App\Models\Transactions\Ledger;
use App\Models\Transactions\LedgerComponent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class LedgerComponentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_ledger_components(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'ledger.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'ledger.component.index')->where('guard_name', 'sanctum')->first());

        $ledger = Ledger::factory()->create();
        LedgerComponent::factory()->count(3)->create(['ledger_id' => $ledger->id]);

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.ledger.component.index', ['ledger_id' => $ledger->id]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'ledger_id', 'in', 'out', 'total'],
                ],
            ]);
    }

    public function test_show_returns_ledger_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'ledger.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'ledger.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'ledger.component.show')->where('guard_name', 'sanctum')->first());

        $component = LedgerComponent::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.ledger.component.show', ['ledger_id' => $component->ledger_id, 'id' => $component->id]));

        $response->assertStatus(200)
            ->assertJson([
                'id' => $component->id,
            ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'ledger.index',
            'ledger.component.index',
            'ledger.component.show',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'ledger_component']);
        }
    }
}
