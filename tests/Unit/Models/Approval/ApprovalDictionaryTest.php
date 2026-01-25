<?php

namespace Tests\Unit\Models\Approval;

use App\Models\Approval\ApprovalDictionary;
use App\Models\Approval\ApprovalFlowComponent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApprovalDictionaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_fillable_attributes(): void
    {
        $fillable = ['key', 'name', 'created_by', 'updated_by', 'deleted_by'];
        $model = new ApprovalDictionary;

        $this->assertEquals($fillable, $model->getFillable());
    }

    public function test_it_has_many_components(): void
    {
        $dictionary = ApprovalDictionary::factory()->create();
        $component = ApprovalFlowComponent::factory()->create(['approval_dictionary_id' => $dictionary->id]);

        $this->assertTrue($dictionary->components->contains($component));
        $this->assertInstanceOf(ApprovalFlowComponent::class, $dictionary->components->first());
    }
}
