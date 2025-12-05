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
     *
     * How to run: php artisan db:seed --class=PermissionSeeder
     */
    public function run(): void
    {
        $permissions = $this->permissions();
        $totalPermissions = $permissions->count();

        $this->command->info('Seeding permissions...');

        $this->command->withProgressBar($totalPermissions, function ($bar) use ($permissions) {
            $processedCount = 0;

            foreach ($permissions as $permission) {
                $processedCount++;

                Permission::firstOrCreate([
                    'name' => $permission['name'],
                    'guard_name' => $permission['guard_name'],
                ], [
                    'label' => $permission['label'],
                    'group' => $permission['group'],
                ]);

                $bar->advance();

                if ($processedCount % 50 === 0) {
                    $this->command->info(" Processed $processedCount/{$permissions->count()} permissions...");
                }
            }
        });

        $this->command->info('All permissions seeded successfully!');

        $this->command->info('Assigning permissions to a developer role...');

        $permissionNames = $permissions->pluck('name')->toArray();

        $developer = Role::where('name', 'developer')->first();
        if (!$developer) {
            $this->command->error('Developer role not found. Skipping permission assignment.');

            return;
        }

        $developer->givePermissionTo($permissionNames);
        $this->command->info('Permissions assigned to a developer role successfully!');
    }

    /**
     * @return Collection<int, array{name: string, label: string, group: string, guard_name: string}>
     */
    private function permissions(): Collection
    {
        $collector = collect();

        /**User permissions*/
        $collector->push(['name' => 'user.index', 'label' => 'User Index', 'group' => 'user', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'user.show', 'label' => 'User Show', 'group' => 'user', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'user.store', 'label' => 'User Store', 'group' => 'user', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'user.update', 'label' => 'User Update', 'group' => 'user', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'user.delete', 'label' => 'User Delete', 'group' => 'user', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'user.destroy', 'label' => 'User Destroy', 'group' => 'user', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'user.restore', 'label' => 'User Restore', 'group' => 'user', 'guard_name' => 'sanctum']);

        /**Role permissions*/
        $collector->push(['name' => 'role.index', 'label' => 'Role Index', 'group' => 'role', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'role.show', 'label' => 'Role Show', 'group' => 'role', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'role.store', 'label' => 'Role Store', 'group' => 'role', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'role.update', 'label' => 'Role Update', 'group' => 'role', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'role.destroy', 'label' => 'Role Destroy', 'group' => 'role', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'role.restore', 'label' => 'Role Restore', 'group' => 'role', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'role.delete', 'label' => 'Role Delete', 'group' => 'role', 'guard_name' => 'sanctum']);

        $collector->push(['name' => 'role.permission.index', 'label' => 'Role Permission Index', 'group' => 'role.permission', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'role.permission.show', 'label' => 'Role Permission Show', 'group' => 'role.permission', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'role.permission.store', 'label' => 'Role Permission Store', 'group' => 'role.permission', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'role.permission.update', 'label' => 'Role Permission Update', 'group' => 'role.permission', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'role.permission.destroy', 'label' => 'Role Permission Destroy', 'group' => 'role.permission', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'role.permission.restore', 'label' => 'Role Permission Restore', 'group' => 'role.permission', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'role.permission.delete', 'label' => 'Role Permission Delete', 'group' => 'role.permission', 'guard_name' => 'sanctum']);

        /**Approvals permissions*/
        $collector->push(['name' => 'approval.index', 'label' => 'Approval Index', 'group' => 'approval', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.show', 'label' => 'Approval Show', 'group' => 'approval', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.store', 'label' => 'Approval Store', 'group' => 'approval', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.update', 'label' => 'Approval Update', 'group' => 'approval', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.delete', 'label' => 'Approval Delete', 'group' => 'approval', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.destroy', 'label' => 'Approval Destroy', 'group' => 'approval', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.restore', 'label' => 'Approval Restore', 'group' => 'approval', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.dictionary.index', 'label' => 'Approval Dictionary Index', 'group' => 'approval.dictionary', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.dictionary.show', 'label' => 'Approval Dictionary Show', 'group' => 'approval.dictionary', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.dictionary.store', 'label' => 'Approval Dictionary Store', 'group' => 'approval.dictionary', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.dictionary.update', 'label' => 'Approval Dictionary Update', 'group' => 'approval.dictionary', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.dictionary.destroy', 'label' => 'Approval Dictionary Destroy', 'group' => 'approval.dictionary', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.dictionary.restore', 'label' => 'Approval Dictionary Restore', 'group' => 'approval.dictionary', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.dictionary.delete', 'label' => 'Approval Dictionary Delete', 'group' => 'approval.dictionary', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.component.index', 'label' => 'Approval Component Index', 'group' => 'approval.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.component.show', 'label' => 'Approval Component Show', 'group' => 'approval.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.component.store', 'label' => 'Approval Component Store', 'group' => 'approval.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.component.update', 'label' => 'Approval Component Update', 'group' => 'approval.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.component.destroy', 'label' => 'Approval Component Destroy', 'group' => 'approval.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.component.restore', 'label' => 'Approval Component Restore', 'group' => 'approval.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.component.delete', 'label' => 'Approval Component Delete', 'group' => 'approval.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.component.contributor.index', 'label' => 'Approval Component Contributor Index', 'group' => 'approval.component.contributor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.component.contributor.show', 'label' => 'Approval Component Contributor Show', 'group' => 'approval.component.contributor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.component.contributor.store', 'label' => 'Approval Component Contributor Store', 'group' => 'approval.component.contributor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.component.contributor.update', 'label' => 'Approval Component Contributor Update', 'group' => 'approval.component.contributor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.component.contributor.destroy', 'label' => 'Approval Component Contributor Destroy', 'group' => 'approval.component.contributor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.component.contributor.restore', 'label' => 'Approval Component Contributor Restore', 'group' => 'approval.component.contributor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.component.contributor.delete', 'label' => 'Approval Component Contributor Delete', 'group' => 'approval.component.contributor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.flow.index', 'label' => 'Approval Flow Index', 'group' => 'approval.flow', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.flow.show', 'label' => 'Approval Flow Show', 'group' => 'approval.flow', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.flow.store', 'label' => 'Approval Flow Store', 'group' => 'approval.flow', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.flow.update', 'label' => 'Approval Flow Update', 'group' => 'approval.flow', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.flow.destroy', 'label' => 'Approval Flow Destroy', 'group' => 'approval.flow', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.flow.restore', 'label' => 'Approval Flow Restore', 'group' => 'approval.flow', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.flow.delete', 'label' => 'Approval Flow Delete', 'group' => 'approval.flow', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.flow.component.index', 'label' => 'Approval Flow Component Index', 'group' => 'approval.flow.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.flow.component.show', 'label' => 'Approval Flow Component Show', 'group' => 'approval.flow.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.flow.component.store', 'label' => 'Approval Flow Component Store', 'group' => 'approval.flow.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.flow.component.update', 'label' => 'Approval Flow Component Update', 'group' => 'approval.flow.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.flow.component.destroy', 'label' => 'Approval Flow Component Destroy', 'group' => 'approval.flow.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.flow.component.restore', 'label' => 'Approval Flow Component Restore', 'group' => 'approval.flow.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.flow.component.delete', 'label' => 'Approval Flow Component Delete', 'group' => 'approval.flow.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.group.index', 'label' => 'Approval Group Index', 'group' => 'approval.group', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.group.show', 'label' => 'Approval Group Show', 'group' => 'approval.group', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.group.store', 'label' => 'Approval Group Store', 'group' => 'approval.group', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.group.update', 'label' => 'Approval Group Update', 'group' => 'approval.group', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.group.destroy', 'label' => 'Approval Group Destroy', 'group' => 'approval.group', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.group.restore', 'label' => 'Approval Group Restore', 'group' => 'approval.group', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.group.delete', 'label' => 'Approval Group Delete', 'group' => 'approval.group', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.group.contributor.index', 'label' => 'Approval Group Contributor Index', 'group' => 'approval.group.contributor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.group.contributor.show', 'label' => 'Approval Group Contributor Show', 'group' => 'approval.group.contributor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.group.contributor.store', 'label' => 'Approval Group Contributor Store', 'group' => 'approval.group.contributor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.group.contributor.update', 'label' => 'Approval Group Contributor Update', 'group' => 'approval.group.contributor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.group.contributor.destroy', 'label' => 'Approval Group Contributor Destroy', 'group' => 'approval.group.contributor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.group.contributor.restore', 'label' => 'Approval Group Contributor Restore', 'group' => 'approval.group.contributor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.group.contributor.delete', 'label' => 'Approval Group Contributor Delete', 'group' => 'approval.group.contributor', 'guard_name' => 'sanctum']);

        /**Approval Event permissions*/
        $collector->push(['name' => 'approval.event.index', 'label' => 'Approval Event Index', 'group' => 'approval.event', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.event.show', 'label' => 'Approval Event Show', 'group' => 'approval.event', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.event.store', 'label' => 'Approval Event Store', 'group' => 'approval.event', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.event.update', 'label' => 'Approval Event Update', 'group' => 'approval.event', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.event.delete', 'label' => 'Approval Event Delete', 'group' => 'approval.event', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.event.destroy', 'label' => 'Approval Event Destroy', 'group' => 'approval.event', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.event.restore', 'label' => 'Approval Event Restore', 'group' => 'approval.event', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.event.component.index', 'label' => 'Approval Event Component Index', 'group' => 'approval.event.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.event.component.show', 'label' => 'Approval Event Component Show', 'group' => 'approval.event.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.event.component.store', 'label' => 'Approval Event Component Store', 'group' => 'approval.event.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.event.component.update', 'label' => 'Approval Event Component Update', 'group' => 'approval.event.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.event.component.delete', 'label' => 'Approval Event Component Delete', 'group' => 'approval.event.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.event.contributor.index', 'label' => 'Approval Event Contributor Index', 'group' => 'approval.event.contributor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.event.contributor.show', 'label' => 'Approval Event Contributor Show', 'group' => 'approval.event.contributor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.event.contributor.store', 'label' => 'Approval Event Contributor Store', 'group' => 'approval.event.contributor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.event.contributor.update', 'label' => 'Approval Event Contributor Update', 'group' => 'approval.event.contributor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'approval.event.contributor.delete', 'label' => 'Approval Event Contributor Delete', 'group' => 'approval.event.contributor', 'guard_name' => 'sanctum']);

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
        $collector->push(['name' => 'item.batch.destroy', 'label' => 'Item Batch Destroy', 'group' => 'item.batch', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.batch.restore', 'label' => 'Item Batch Restores', 'group' => 'item.batch', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.batch.delete', 'label' => 'Item Batch Delete', 'group' => 'item.batch', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.batch.stock.index', 'label' => 'Item Batch Stock Index', 'group' => 'item.batch.stock', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.batch.stock.show', 'label' => 'Item Batch Stock Show', 'group' => 'item.batch.stock', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.batch.stock.store', 'label' => 'Item Batch Stock Store', 'group' => 'item.batch.stock', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.batch.stock.update', 'label' => 'Item Batch Stock Update', 'group' => 'item.batch.stock', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.batch.stock.destroy', 'label' => 'Item Batch Stock Destroy', 'group' => 'item.batch.stock', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.batch.stock.restore', 'label' => 'Item Batch Stock Restore', 'group' => 'item.batch.stock', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.batch.stock.delete', 'label' => 'Item Batch Stock Delete', 'group' => 'item.batch.stock', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.batch.stock.history.index', 'label' => 'Item Batch Stock History Index', 'group' => 'item.batch.stock.history', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.batch.stock.history.show', 'label' => 'Item Batch Stock History Show', 'group' => 'item.batch.stock.history', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.batch.stock.history.store', 'label' => 'Item Batch Stock History Store', 'group' => 'item.batch.stock.history', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.batch.stock.history.update', 'label' => 'Item Batch Stock History Update', 'group' => 'item.batch.stock.history', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.batch.stock.history.destroy', 'label' => 'Item Batch Stock History Destroy', 'group' => 'item.batch.stock.history', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.batch.stock.history.restore', 'label' => 'Item Batch Stock History Restores', 'group' => 'item.batch.stock.history', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.batch.stock.history.delete', 'label' => 'Item Batch Stock History Delete', 'group' => 'item.batch.stock.history', 'guard_name' => 'sanctum']);

        /**Item Bill permissions*/
        $collector->push(['name' => 'item.bill.index', 'label' => 'Item Bill Index', 'group' => 'item.bill', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.bill.show', 'label' => 'Item Bill Show', 'group' => 'item.bill', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.bill.store', 'label' => 'Item Bill Store', 'group' => 'item.bill', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.bill.update', 'label' => 'Item Bill Update', 'group' => 'item.bill', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.bill.delete', 'label' => 'Item Bill Delete', 'group' => 'item.bill', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.bill.destroy', 'label' => 'Item Bill Destroy', 'group' => 'item.bill', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.bill.restore', 'label' => 'Item Bill Restore', 'group' => 'item.bill', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.bill.component.index', 'label' => 'Item Bill Component Index', 'group' => 'item.bill.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.bill.component.show', 'label' => 'Item Bill Component Show', 'group' => 'item.bill.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.bill.component.store', 'label' => 'Item Bill Component Store', 'group' => 'item.bill.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.bill.component.update', 'label' => 'Item Bill Component Update', 'group' => 'item.bill.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.bill.component.destroy', 'label' => 'Item Bill Component Destroy', 'group' => 'item.bill.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.bill.component.restore', 'label' => 'Item Bill Component Restore', 'group' => 'item.bill.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'item.bill.component.delete', 'label' => 'Item Bill Component Delete', 'group' => 'item.bill.component', 'guard_name' => 'sanctum']);

        /**Good Receipt permissions*/
        $collector->push(['name' => 'good.receipt.index', 'label' => 'Good Receipt Index', 'group' => 'good.receipt', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'good.receipt.show', 'label' => 'Good Receipt Show', 'group' => 'good.receipt', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'good.receipt.store', 'label' => 'Good Receipt Store', 'group' => 'good.receipt', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'good.receipt.update', 'label' => 'Good Receipt Update', 'group' => 'good.receipt', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'good.receipt.delete', 'label' => 'Good Receipt Delete', 'group' => 'good.receipt', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'good.receipt.destroy', 'label' => 'Good Receipt Destroy', 'group' => 'good.receipt', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'good.receipt.restore', 'label' => 'Good Receipt Restore', 'group' => 'good.receipt', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'good.receipt.component.index', 'label' => 'Good Receipt Component Index', 'group' => 'good.receipt.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'good.receipt.component.show', 'label' => 'Good Receipt Component Show', 'group' => 'good.receipt.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'good.receipt.component.store', 'label' => 'Good Receipt Component Store', 'group' => 'good.receipt.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'good.receipt.component.update', 'label' => 'Good Receipt Component Update', 'group' => 'good.receipt.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'good.receipt.component.delete', 'label' => 'Good Receipt Component Delete', 'group' => 'good.receipt.component', 'guard_name' => 'sanctum']);

        /**Good Issue permissions*/
        $collector->push(['name' => 'good.issue.index', 'label' => 'Good Issue Index', 'group' => 'good.issue', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'good.issue.show', 'label' => 'Good Issue Show', 'group' => 'good.issue', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'good.issue.store', 'label' => 'Good Issue Store', 'group' => 'good.issue', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'good.issue.update', 'label' => 'Good Issue Update', 'group' => 'good.issue', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'good.issue.delete', 'label' => 'Good Issue Delete', 'group' => 'good.issue', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'good.issue.destroy', 'label' => 'Good Issue Destroy', 'group' => 'good.issue', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'good.issue.restore', 'label' => 'Good Issue Restore', 'group' => 'good.issue', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'good.issue.component.index', 'label' => 'Good Issue Component Index', 'group' => 'good.issue.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'good.issue.component.show', 'label' => 'Good Issue Component Show', 'group' => 'good.issue.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'good.issue.component.store', 'label' => 'Good Issue Component Store', 'group' => 'good.issue.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'good.issue.component.update', 'label' => 'Good Issue Component Update', 'group' => 'good.issue.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'good.issue.component.delete', 'label' => 'Good Issue Component Delete', 'group' => 'good.issue.component', 'guard_name' => 'sanctum']);

        /**Purchase Request permissions*/
        $collector->push(['name' => 'purchase.request.index', 'label' => 'Purchase Request Index', 'group' => 'purchase.request', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.request.show', 'label' => 'Purchase Request Show', 'group' => 'purchase.request', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.request.store', 'label' => 'Purchase Request Store', 'group' => 'purchase.request', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.request.update', 'label' => 'Purchase Request Update', 'group' => 'purchase.request', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.request.delete', 'label' => 'Purchase Request Delete', 'group' => 'purchase.request', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.request.destroy', 'label' => 'Purchase Request Destroy', 'group' => 'purchase.request', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.request.restore', 'label' => 'Purchase Request Restore', 'group' => 'purchase.request', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.request.component.index', 'label' => 'Purchase Request Component Index', 'group' => 'purchase.request.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.request.component.show', 'label' => 'Purchase Request Component Show', 'group' => 'purchase.request.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.request.component.store', 'label' => 'Purchase Request Component Store', 'group' => 'purchase.request.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.request.component.update', 'label' => 'Purchase Request Component Update', 'group' => 'purchase.request.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.request.component.delete', 'label' => 'Purchase Request Component Delete', 'group' => 'purchase.request.component', 'guard_name' => 'sanctum']);

        /**Purchase Procurement permissions*/
        $collector->push(['name' => 'purchase.procurement.index', 'label' => 'Purchase Procurement Index', 'group' => 'purchase.procurement', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.procurement.show', 'label' => 'Purchase Procurement Show', 'group' => 'purchase.procurement', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.procurement.store', 'label' => 'Purchase Procurement Store', 'group' => 'purchase.procurement', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.procurement.update', 'label' => 'Purchase Procurement Update', 'group' => 'purchase.procurement', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.procurement.delete', 'label' => 'Purchase Procurement Delete', 'group' => 'purchase.procurement', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.procurement.destroy', 'label' => 'Purchase Procurement Destroy', 'group' => 'purchase.procurement', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.procurement.restore', 'label' => 'Purchase Procurement Restore', 'group' => 'purchase.procurement', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.procurement.component.index', 'label' => 'Purchase Procurement Component Index', 'group' => 'purchase.procurement.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.procurement.component.show', 'label' => 'Purchase Procurement Component Show', 'group' => 'purchase.procurement.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.procurement.component.store', 'label' => 'Purchase Procurement Component Store', 'group' => 'purchase.procurement.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.procurement.component.update', 'label' => 'Purchase Procurement Component Update', 'group' => 'purchase.procurement.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.procurement.component.delete', 'label' => 'Purchase Procurement Component Delete', 'group' => 'purchase.procurement.component', 'guard_name' => 'sanctum']);

        /**Purchase Order permissions*/
        $collector->push(['name' => 'purchase.order.index', 'label' => 'Purchase Order Index', 'group' => 'purchase.order', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.order.show', 'label' => 'Purchase Order Show', 'group' => 'purchase.order', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.order.store', 'label' => 'Purchase Order Store', 'group' => 'purchase.order', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.order.update', 'label' => 'Purchase Order Update', 'group' => 'purchase.order', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.order.delete', 'label' => 'Purchase Order Delete', 'group' => 'purchase.order', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.order.destroy', 'label' => 'Purchase Order Destroy', 'group' => 'purchase.order', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.order.restore', 'label' => 'Purchase Order Restore', 'group' => 'purchase.order', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.order.component.index', 'label' => 'Purchase Order Component Index', 'group' => 'purchase.order.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.order.component.show', 'label' => 'Purchase Order Component Show', 'group' => 'purchase.order.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.order.component.store', 'label' => 'Purchase Order Component Store', 'group' => 'purchase.order.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.order.component.update', 'label' => 'Purchase Order Component Update', 'group' => 'purchase.order.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.order.component.delete', 'label' => 'Purchase Order Component Delete', 'group' => 'purchase.order.component', 'guard_name' => 'sanctum']);

        /**Purchase Return permissions*/
        $collector->push(['name' => 'purchase.return.index', 'label' => 'Purchase Return Index', 'group' => 'purchase.return', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.return.show', 'label' => 'Purchase Return Show', 'group' => 'purchase.return', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.return.store', 'label' => 'Purchase Return Store', 'group' => 'purchase.return', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.return.update', 'label' => 'Purchase Return Update', 'group' => 'purchase.return', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.return.delete', 'label' => 'Purchase Return Delete', 'group' => 'purchase.return', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.return.destroy', 'label' => 'Purchase Return Destroy', 'group' => 'purchase.return', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.return.restore', 'label' => 'Purchase Return Restore', 'group' => 'purchase.return', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.return.component.index', 'label' => 'Purchase Return Component Index', 'group' => 'purchase.return.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.return.component.show', 'label' => 'Purchase Return Component Show', 'group' => 'purchase.return.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.return.component.store', 'label' => 'Purchase Return Component Store', 'group' => 'purchase.return.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.return.component.update', 'label' => 'Purchase Return Component Update', 'group' => 'purchase.return.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.return.component.delete', 'label' => 'Purchase Return Component Delete', 'group' => 'purchase.return.component', 'guard_name' => 'sanctum']);

        /**Purchase Invoice permissions*/
        $collector->push(['name' => 'purchase.invoice.index', 'label' => 'Purchase Invoice Index', 'group' => 'purchase.invoice', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.invoice.show', 'label' => 'Purchase Invoice Show', 'group' => 'purchase.invoice', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.invoice.store', 'label' => 'Purchase Invoice Store', 'group' => 'purchase.invoice', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.invoice.update', 'label' => 'Purchase Invoice Update', 'group' => 'purchase.invoice', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.invoice.delete', 'label' => 'Purchase Invoice Delete', 'group' => 'purchase.invoice', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.invoice.destroy', 'label' => 'Purchase Invoice Destroy', 'group' => 'purchase.invoice', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.invoice.restore', 'label' => 'Purchase Invoice Restore', 'group' => 'purchase.invoice', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.invoice.component.index', 'label' => 'Purchase Invoice Component Index', 'group' => 'purchase.invoice.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.invoice.component.show', 'label' => 'Purchase Invoice Component Show', 'group' => 'purchase.invoice.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.invoice.component.store', 'label' => 'Purchase Invoice Component Store', 'group' => 'purchase.invoice.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.invoice.component.update', 'label' => 'Purchase Invoice Component Update', 'group' => 'purchase.invoice.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'purchase.invoice.component.delete', 'label' => 'Purchase Invoice Component Delete', 'group' => 'purchase.invoice.component', 'guard_name' => 'sanctum']);

        /**Sales Order permissions*/
        $collector->push(['name' => 'sales.order.index', 'label' => 'Sales Order Index', 'group' => 'sales.order', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.order.show', 'label' => 'Sales Order Show', 'group' => 'sales.order', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.order.store', 'label' => 'Sales Order Store', 'group' => 'sales.order', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.order.update', 'label' => 'Sales Order Update', 'group' => 'sales.order', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.order.delete', 'label' => 'Sales Order Delete', 'group' => 'sales.order', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.order.destroy', 'label' => 'Sales Order Destroy', 'group' => 'sales.order', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.order.restore', 'label' => 'Sales Order Restore', 'group' => 'sales.order', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.order.component.index', 'label' => 'Sales Order Component Index', 'group' => 'sales.order.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.order.component.show', 'label' => 'Sales Order Component Show', 'group' => 'sales.order.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.order.component.store', 'label' => 'Sales Order Component Store', 'group' => 'sales.order.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.order.component.update', 'label' => 'Sales Order Component Update', 'group' => 'sales.order.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.order.component.delete', 'label' => 'Sales Order Component Delete', 'group' => 'sales.order.component', 'guard_name' => 'sanctum']);

        /**Sales Invoice permissions*/
        $collector->push(['name' => 'sales.invoice.index', 'label' => 'Sales Invoice Index', 'group' => 'sales.invoice', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.invoice.show', 'label' => 'Sales Invoice Show', 'group' => 'sales.invoice', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.invoice.store', 'label' => 'Sales Invoice Store', 'group' => 'sales.invoice', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.invoice.update', 'label' => 'Sales Invoice Update', 'group' => 'sales.invoice', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.invoice.delete', 'label' => 'Sales Invoice Delete', 'group' => 'sales.invoice', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.invoice.destroy', 'label' => 'Sales Invoice Destroy', 'group' => 'sales.invoice', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.invoice.restore', 'label' => 'Sales Invoice Restore', 'group' => 'sales.invoice', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.invoice.component.index', 'label' => 'Sales Invoice Component Index', 'group' => 'sales.invoice.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.invoice.component.show', 'label' => 'Sales Invoice Component Show', 'group' => 'sales.invoice.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.invoice.component.store', 'label' => 'Sales Invoice Component Store', 'group' => 'sales.invoice.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.invoice.component.update', 'label' => 'Sales Invoice Component Update', 'group' => 'sales.invoice.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.invoice.component.delete', 'label' => 'Sales Invoice Component Delete', 'group' => 'sales.invoice.component', 'guard_name' => 'sanctum']);

        /**Sales Return permissions*/
        $collector->push(['name' => 'sales.return.index', 'label' => 'Sales Return Index', 'group' => 'sales.return', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.return.show', 'label' => 'Sales Return Show', 'group' => 'sales.return', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.return.store', 'label' => 'Sales Return Store', 'group' => 'sales.return', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.return.update', 'label' => 'Sales Return Update', 'group' => 'sales.return', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.return.delete', 'label' => 'Sales Return Delete', 'group' => 'sales.return', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.return.destroy', 'label' => 'Sales Return Destroy', 'group' => 'sales.return', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.return.restore', 'label' => 'Sales Return Restore', 'group' => 'sales.return', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.return.component.index', 'label' => 'Sales Return Component Index', 'group' => 'sales.return.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.return.component.show', 'label' => 'Sales Return Component Show', 'group' => 'sales.return.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.return.component.store', 'label' => 'Sales Return Component Store', 'group' => 'sales.return.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.return.component.update', 'label' => 'Sales Return Component Update', 'group' => 'sales.return.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'sales.return.component.delete', 'label' => 'Sales Return Component Delete', 'group' => 'sales.return.component', 'guard_name' => 'sanctum']);

        /**Vendor permissions*/
        $collector->push(['name' => 'vendor.index', 'label' => 'Vendor Index', 'group' => 'vendor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.show', 'label' => 'Vendor Show', 'group' => 'vendor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.store', 'label' => 'Vendor Store', 'group' => 'vendor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.update', 'label' => 'Vendor Update', 'group' => 'vendor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.delete', 'label' => 'Vendor Delete', 'group' => 'vendor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.destroy', 'label' => 'Vendor Destroy', 'group' => 'vendor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.restore', 'label' => 'Vendor Restore', 'group' => 'vendor', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.component.index', 'label' => 'Vendor Component Index', 'group' => 'vendor.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.component.show', 'label' => 'Vendor Component Show', 'group' => 'vendor.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.component.store', 'label' => 'Vendor Component Store', 'group' => 'vendor.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.component.update', 'label' => 'Vendor Component Update', 'group' => 'vendor.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.component.delete', 'label' => 'Vendor Component Delete', 'group' => 'vendor.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.component.history.index', 'label' => 'Vendor Component History Index', 'group' => 'vendor.component.history', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.component.history.show', 'label' => 'Vendor Component History Show', 'group' => 'vendor.component.history', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.component.history.store', 'label' => 'Vendor Component History Store', 'group' => 'vendor.component.history', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.component.history.update', 'label' => 'Vendor Component History Update', 'group' => 'vendor.component.history', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.component.history.delete', 'label' => 'Vendor Component History Delete', 'group' => 'vendor.component.history', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.account.payable.index', 'label' => 'Vendor Account Payable Index', 'group' => 'vendor.account.payable', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.account.payable.show', 'label' => 'Vendor Account Payable Show', 'group' => 'vendor.account.payable', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.account.payable.store', 'label' => 'Vendor Account Payable Store', 'group' => 'vendor.account.payable', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.account.payable.update', 'label' => 'Vendor Account Payable Update', 'group' => 'vendor.account.payable', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.account.payable.delete', 'label' => 'Vendor Account Payable Delete', 'group' => 'vendor.account.payable', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.account.payable.component.index', 'label' => 'Vendor Account Payable Component Index', 'group' => 'vendor.account.payable.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.account.payable.component.show', 'label' => 'Vendor Account Payable Component Show', 'group' => 'vendor.account.payable.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.account.payable.component.store', 'label' => 'Vendor Account Payable Component Store', 'group' => 'vendor.account.payable.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.account.payable.component.update', 'label' => 'Vendor Account Payable Component Update', 'group' => 'vendor.account.payable.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.account.payable.component.delete', 'label' => 'Vendor Account Payable Component Delete', 'group' => 'vendor.account.payable.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.invoice.index', 'label' => 'Vendor Invoice Index', 'group' => 'vendor.invoice', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.invoice.show', 'label' => 'Vendor Invoice Show', 'group' => 'vendor.invoice', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.invoice.store', 'label' => 'Vendor Invoice Store', 'group' => 'vendor.invoice', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.invoice.update', 'label' => 'Vendor Invoice Update', 'group' => 'vendor.invoice', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.invoice.delete', 'label' => 'Vendor Invoice Delete', 'group' => 'vendor.invoice', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.invoice.destroy', 'label' => 'Vendor Invoice Destroy', 'group' => 'vendor.invoice', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.invoice.restore', 'label' => 'Vendor Invoice Restore', 'group' => 'vendor.invoice', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.invoice.component.index', 'label' => 'Vendor Invoice Component Index', 'group' => 'vendor.invoice.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.invoice.component.show', 'label' => 'Vendor Invoice Component Show', 'group' => 'vendor.invoice.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.invoice.component.store', 'label' => 'Vendor Invoice Component Store', 'group' => 'vendor.invoice.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.invoice.component.update', 'label' => 'Vendor Invoice Component Update', 'group' => 'vendor.invoice.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.invoice.component.delete', 'label' => 'Vendor Invoice Component Delete', 'group' => 'vendor.invoice.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.payment.index', 'label' => 'Vendor Payment Index', 'group' => 'vendor.payment', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.payment.show', 'label' => 'Vendor Payment Show', 'group' => 'vendor.payment', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.payment.store', 'label' => 'Vendor Payment Store', 'group' => 'vendor.payment', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.payment.update', 'label' => 'Vendor Payment Update', 'group' => 'vendor.payment', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.payment.delete', 'label' => 'Vendor Payment Delete', 'group' => 'vendor.payment', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.payment.destroy', 'label' => 'Vendor Payment Destroy', 'group' => 'vendor.payment', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.payment.restore', 'label' => 'Vendor Payment Restore', 'group' => 'vendor.payment', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.payment.component.index', 'label' => 'Vendor Payment Component Index', 'group' => 'vendor.payment.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.payment.component.show', 'label' => 'Vendor Payment Component Show', 'group' => 'vendor.payment.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.payment.component.store', 'label' => 'Vendor Payment Component Store', 'group' => 'vendor.payment.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.payment.component.update', 'label' => 'Vendor Payment Component Update', 'group' => 'vendor.payment.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'vendor.payment.component.delete', 'label' => 'Vendor Payment Component Delete', 'group' => 'vendor.payment.component', 'guard_name' => 'sanctum']);

        /**Transaction Ledger permissions*/
        $collector->push(['name' => 'ledger.index', 'label' => 'Ledger Index', 'group' => 'ledger', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'ledger.show', 'label' => 'Ledger Show', 'group' => 'ledger', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'ledger.store', 'label' => 'Ledger Store', 'group' => 'ledger', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'ledger.update', 'label' => 'Ledger Update', 'group' => 'ledger', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'ledger.delete', 'label' => 'Ledger Delete', 'group' => 'ledger', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'ledger.destroy', 'label' => 'Ledger Destroy', 'group' => 'ledger', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'ledger.restore', 'label' => 'Ledger Restore', 'group' => 'ledger', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'ledger.component.index', 'label' => 'Ledger Component Index', 'group' => 'ledger.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'ledger.component.show', 'label' => 'Ledger Component Show', 'group' => 'ledger.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'ledger.component.store', 'label' => 'Ledger Component Store', 'group' => 'ledger.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'ledger.component.update', 'label' => 'Ledger Component Update', 'group' => 'ledger.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'ledger.component.delete', 'label' => 'Ledger Component Delete', 'group' => 'ledger.component', 'guard_name' => 'sanctum']);

        /**Transaction Payment Request permissions*/
        $collector->push(['name' => 'payment.request.index', 'label' => 'Payment Request Index', 'group' => 'payment.request', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'payment.request.show', 'label' => 'Payment Request Show', 'group' => 'payment.request', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'payment.request.store', 'label' => 'Payment Request Store', 'group' => 'payment.request', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'payment.request.update', 'label' => 'Payment Request Update', 'group' => 'payment.request', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'payment.request.delete', 'label' => 'Payment Request Delete', 'group' => 'payment.request', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'payment.request.destroy', 'label' => 'Payment Request Destroy', 'group' => 'payment.request', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'payment.request.restore', 'label' => 'Payment Request Restore', 'group' => 'payment.request', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'payment.request.component.index', 'label' => 'Payment Request Component Index', 'group' => 'payment.request.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'payment.request.component.show', 'label' => 'Payment Request Component Show', 'group' => 'payment.request.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'payment.request.component.store', 'label' => 'Payment Request Component Store', 'group' => 'payment.request.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'payment.request.component.update', 'label' => 'Payment Request Component Update', 'group' => 'payment.request.component', 'guard_name' => 'sanctum']);
        $collector->push(['name' => 'payment.request.component.delete', 'label' => 'Payment Request Component Delete', 'group' => 'payment.request.component', 'guard_name' => 'sanctum']);

        return $collector;
    }
}
