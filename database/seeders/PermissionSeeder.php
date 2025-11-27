<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = $this->permissions();
        $permissionNames = [];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission['name'],
                'guard_name' => $permission['guard_name'],
            ], [
                'label' => $permission['label'],
                'group' => $permission['group'],
            ]);

            $permissionNames[] = $permission['name'];
        }

        $developer = Role::where('name', 'developer')->first();
        if (!$developer) {
            return;
        }

        $developer->givePermissionTo($permissionNames);
    }

    /**
     * @return Collection<int, array{name: string, label: string, group: string, guard_name: string}>
     */
    private function permissions(): Collection
    {
        $collector = collect();

        /**Approvals permissions*/
        $collector->push(['name' => 'approval.index', 'label' => 'Approval Index', 'group' => 'approval', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.show', 'label' => 'Approval Show', 'group' => 'approval', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.store', 'label' => 'Approval Store', 'group' => 'approval', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.update', 'label' => 'Approval Update', 'group' => 'approval', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.delete', 'label' => 'Approval Delete', 'group' => 'approval', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.dictionary.index', 'label' => 'Approval Dictionary Index', 'group' => 'approval.dictionary', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.dictionary.show', 'label' => 'Approval Dictionary Show', 'group' => 'approval.dictionary', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.dictionary.store', 'label' => 'Approval Dictionary Store', 'group' => 'approval.dictionary', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.dictionary.update', 'label' => 'Approval Dictionary Update', 'group' => 'approval.dictionary', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.dictionary.delete', 'label' => 'Approval Dictionary Delete', 'group' => 'approval.dictionary', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.component.index', 'label' => 'Approval Component Index', 'group' => 'approval.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.component.show', 'label' => 'Approval Component Show', 'group' => 'approval.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.component.store', 'label' => 'Approval Component Store', 'group' => 'approval.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.component.update', 'label' => 'Approval Component Update', 'group' => 'approval.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.component.delete', 'label' => 'Approval Component Delete', 'group' => 'approval.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.component.contributor.index', 'label' => 'Approval Component Contributor Index', 'group' => 'approval.component.contributor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.component.contributor.show', 'label' => 'Approval Component Contributor Show', 'group' => 'approval.component.contributor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.component.contributor.store', 'label' => 'Approval Component Contributor Store', 'group' => 'approval.component.contributor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.component.contributor.update', 'label' => 'Approval Component Contributor Update', 'group' => 'approval.component.contributor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.component.contributor.delete', 'label' => 'Approval Component Contributor Delete', 'group' => 'approval.component.contributor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.flow.index', 'label' => 'Approval Flow Index', 'group' => 'approval.flow', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.flow.show', 'label' => 'Approval Flow Show', 'group' => 'approval.flow', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.flow.store', 'label' => 'Approval Flow Store', 'group' => 'approval.flow', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.flow.update', 'label' => 'Approval Flow Update', 'group' => 'approval.flow', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.flow.delete', 'label' => 'Approval Flow Delete', 'group' => 'approval.flow', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.flow.component.index', 'label' => 'Approval Flow Component Index', 'group' => 'approval.flow.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.flow.component.show', 'label' => 'Approval Flow Component Show', 'group' => 'approval.flow.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.flow.component.store', 'label' => 'Approval Flow Component Store', 'group' => 'approval.flow.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.flow.component.update', 'label' => 'Approval Flow Component Update', 'group' => 'approval.flow.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.flow.component.delete', 'label' => 'Approval Flow Component Delete', 'group' => 'approval.flow.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.group.index', 'label' => 'Approval Group Index', 'group' => 'approval.group', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.group.show', 'label' => 'Approval Group Show', 'group' => 'approval.group', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.group.store', 'label' => 'Approval Group Store', 'group' => 'approval.group', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.group.update', 'label' => 'Approval Group Update', 'group' => 'approval.group', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.group.delete', 'label' => 'Approval Group Delete', 'group' => 'approval.group', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.group.contributor.index', 'label' => 'Approval Group Contributor Index', 'group' => 'approval.group.contributor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.group.contributor.show', 'label' => 'Approval Group Contributor Show', 'group' => 'approval.group.contributor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.group.contributor.store', 'label' => 'Approval Group Contributor Store', 'group' => 'approval.group.contributor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.group.contributor.update', 'label' => 'Approval Group Contributor Update', 'group' => 'approval.group.contributor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.group.contributor.delete', 'label' => 'Approval Group Contributor Delete', 'group' => 'approval.group.contributor', 'guard_name' => 'sanctum']);

        /**Items permissions*/
        $collector->push(['name' => 'item.index', 'label' => 'Item Index', 'group' => 'item', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.show', 'label' => 'Item Show', 'group' => 'item', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.store', 'label' => 'Item Store', 'group' => 'item', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.update', 'label' => 'Item Update', 'group' => 'item', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.delete', 'label' => 'Item Delete', 'group' => 'item', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.destroy', 'label' => 'Item Destroy', 'group' => 'item', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.restore', 'label' => 'Item Restore', 'group' => 'item', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.batch.index', 'label' => 'Item Batch Index', 'group' => 'item.batch', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.batch.show', 'label' => 'Item Batch Show', 'group' => 'item.batch', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.batch.store', 'label' => 'Item Batch Store', 'group' => 'item.batch', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.batch.update', 'label' => 'Item Batch Update', 'group' => 'item.batch', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.batch.delete', 'label' => 'Item Batch Delete', 'group' => 'item.batch', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.batch.stock.index', 'label' => 'Item Batch Stock Index', 'group' => 'item.batch.stock', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.batch.stock.show', 'label' => 'Item Batch Stock Show', 'group' => 'item.batch.stock', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.batch.stock.store', 'label' => 'Item Batch Stock Store', 'group' => 'item.batch.stock', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.batch.stock.update', 'label' => 'Item Batch Stock Update', 'group' => 'item.batch.stock', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.batch.stock.delete', 'label' => 'Item Batch Stock Delete', 'group' => 'item.batch.stock', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.batch.stock.history.index', 'label' => 'Item Batch Stock History Index', 'group' => 'item.batch.stock.history', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.batch.stock.history.show', 'label' => 'Item Batch Stock History Show', 'group' => 'item.batch.stock.history', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.batch.stock.history.store', 'label' => 'Item Batch Stock History Store', 'group' => 'item.batch.stock.history', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.batch.stock.history.update', 'label' => 'Item Batch Stock History Update', 'group' => 'item.batch.stock.history', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.batch.stock.history.delete', 'label' => 'Item Batch Stock History Delete', 'group' => 'item.batch.stock.history', 'guard_name' => 'sanctum']);

        return $collector;
    }
}
