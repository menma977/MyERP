<?php

namespace Tests\Unit\Models\Transactions;

use App\Models\Transactions\Ledger;
use App\Models\Transactions\LedgerComponent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LedgerComponentTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_ledger(): void
    {
        $ledger = Ledger::factory()->create();
        $component = LedgerComponent::factory()->create(['ledger_id' => $ledger->id]);

        $this->assertInstanceOf(Ledger::class, $component->ledger);
        $this->assertEquals($ledger->id, $component->ledger->id);
    }
}
