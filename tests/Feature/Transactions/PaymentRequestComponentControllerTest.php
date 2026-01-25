<?php

namespace Tests\Feature\Transactions;

use App\Models\Purchases\PurchaseInvoiceComponent;
use App\Models\Purchases\PurchaseOrderComponent;
use App\Models\Transactions\PaymentRequest;
use App\Models\Transactions\PaymentRequestComponent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PaymentRequestComponentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_payment_request_components(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'payment.request.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'payment.request.component.index')->where('guard_name', 'sanctum')->first());

        $paymentRequest = PaymentRequest::factory()->create();
        PaymentRequestComponent::factory()->count(3)->create(['payment_request_id' => $paymentRequest->id]);

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.payment.request.component.index', ['payment_request_id' => $paymentRequest->id]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'payment_request_id', 'total'],
                ],
            ]);
    }

    public function test_show_returns_payment_request_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'payment.request.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'payment.request.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'payment.request.component.show')->where('guard_name', 'sanctum')->first());

        $component = PaymentRequestComponent::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('api.v1.payment.request.component.show', ['payment_request_id' => $component->payment_request_id, 'id' => $component->id]));

        $response->assertStatus(200)
            ->assertJson([
                'id' => $component->id,
            ]);
    }

    public function test_store_creates_payment_request_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::where('name', 'payment.request.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'payment.request.component.index')->where('guard_name', 'sanctum')->first());
        $user->givePermissionTo(Permission::where('name', 'payment.request.component.store')->where('guard_name', 'sanctum')->first());

        $paymentRequest = PaymentRequest::factory()->create();
        $poComponent = PurchaseOrderComponent::factory()->create();
        $piComponent = PurchaseInvoiceComponent::factory()->create();

        $array = [
            'payment_request_id' => $paymentRequest->id,
            'purchase_order_component_id' => $poComponent->id,
            'purchase_invoice_component_id' => $piComponent->id,
            'quantity' => 10,
            'price' => 100,
            'total' => 1000,
            'note' => 'New component',
        ];

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('api.v1.payment.request.component.store', ['payment_request_id' => $paymentRequest->id]), $array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('payment_request_components', ['payment_request_id' => $paymentRequest->id, 'total' => 1000]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'payment.request.index',
            'payment.request.component.index',
            'payment.request.component.show',
            'payment.request.component.store',
            'payment.request.component.update',
            'payment.request.component.delete',
            'payment.request.component.restore',
            'payment.request.component.destroy',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum'], ['label' => $permission, 'group' => 'payment_request_component']);
        }
    }
}
