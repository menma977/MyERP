<?php

namespace Tests\Feature\Transactions;

use App\Models\Permission;
use App\Models\Purchases\PurchaseInvoice;
use App\Models\Purchases\PurchaseOrder;
use App\Models\Transactions\PaymentRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaymentRequestControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_payment_requests(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'payment.request.index')->where('guard_name', 'sanctum')->first());

        PaymentRequest::factory()->count(3)->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.payment.request.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'code', 'total', 'tax', 'method'],
                ],
            ]);
    }

    public function test_show_returns_payment_request(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'payment.request.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'payment.request.show')->where('guard_name', 'sanctum')->first());

        $paymentRequest = PaymentRequest::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.payment.request.show', $paymentRequest->id));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $paymentRequest->id,
                    'code' => $paymentRequest->code,
                ],
            ]);
    }

    public function test_update_updates_payment_request(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'payment.request.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'payment.request.update')->where('guard_name', 'sanctum')->first());

        $paymentRequest = PaymentRequest::factory()->create();
        $newOrder = PurchaseOrder::factory()->create();
        $newInvoice = PurchaseInvoice::factory()->create();

        $array = [
            'purchase_order_id' => $newOrder->id,
            'purchase_invoice_id' => $newInvoice->id,
            'method' => 'BANK_TRANSFER',
            'total' => 5000,
            'tax' => 600,
            'note' => 'Updated note',
            'code' => $paymentRequest->code,
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('api.v1.payment.request.update', $paymentRequest->id), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('payment_requests', ['id' => $paymentRequest->id, 'total' => 5000]);
    }

    public function test_delete_soft_deletes_payment_request(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'payment.request.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'payment.request.delete')->where('guard_name', 'sanctum')->first());

        $paymentRequest = PaymentRequest::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('api.v1.payment.request.delete', $paymentRequest->id));

        $response->assertStatus(200);
        $this->assertSoftDeleted('payment_requests', ['id' => $paymentRequest->id]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'payment.request.index',
            'payment.request.show',
            'payment.request.update',
            'payment.request.delete',
            'payment.request.restore',
            'payment.request.destroy',
            'payment.request.approve',
            'payment.request.reject',
            'payment.request.cancel',
            'payment.request.rollback',
            'payment.request.force',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'payment_request']);
        }
    }
}
