<?php

namespace Tests\Unit\Models\Companies;

use App\Models\Companies\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_casts_attributes(): void
    {
        $company = Company::factory()->create();

        $this->assertNotNull($company->created_at);
        $this->assertNotEmpty($company->ulid);
    }
}
