<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = $this->tables();

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('company_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('companies')
                    ->cascadeOnDelete();
            });
        }

        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = $this->tables();

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }

        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }

    /**
     * @return string[]
     */
    protected function tables(): array
    {
        return [
            'good_issues',
            'payment_requests',
            'good_receipts',
            'sales_orders',
            'sales_invoices',
            'sales_returns',
            'purchase_returns',
            'purchase_invoices',
            'purchase_orders',
            'purchase_procurements',
            'vendor_components',
            'vendor_account_payables',
            'purchase_requests',
            'vendor_invoices',
            'vendor_payments',
            'approval_components',
            'approval_contributors',
            'approval_dictionaries',
            'approval_event_components',
            'approval_event_contributors',
            'approval_events',
            'approval_flow_components',
            'approval_flows',
            'approval_group_contributors',
            'approval_groups',
            'approvals',
            'companies',
            'file_buckets',
            'good_issue_components',
            'good_receipt_components',
            'item_batches',
            'item_bill_components',
            'item_bills',
            'item_stock_histories',
            'item_stocks',
            'items',
            'ledger_components',
            'ledgers',
            'payment_request_components',
            'purchase_invoice_components',
            'purchase_order_components',
            'purchase_procurement_components',
            'purchase_request_components',
            'purchase_return_components',
            'sales_invoice_components',
            'sales_order_components',
            'sales_return_components',
            'vendor_account_payable_components',
            'vendor_component_histories',
            'vendor_invoice_components',
            'vendor_payment_components',
            'vendors',
        ];
    }
};
