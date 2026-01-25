<?php

namespace Tests\Unit\Models\Transactions;

use App\Models\Transactions\Ledger;
use App\Models\Transactions\LedgerComponent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LedgerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_many_components(): void
    {
        $ledger = Ledger::factory()->create();
        $component = LedgerComponent::factory()->create(['ledger_id' => $ledger->id]);

        $this->assertTrue($ledger->component->contains($component));
    }
}
