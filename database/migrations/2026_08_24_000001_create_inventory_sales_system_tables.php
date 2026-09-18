<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('short_name', 20)->unique();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->string('name', 150);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['parent_id', 'name']);
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->string('sku', 100)->unique();
            $table->string('barcode', 100)->nullable()->unique();
            $table->string('brand', 150)->nullable()->index();
            $table->string('name', 255);
            $table->string('color', 100)->nullable()->index();
            $table->decimal('volume', 12, 3)->nullable();
            $table->decimal('purchase_price', 18, 4)->default(0);
            $table->decimal('sale_price', 18, 4)->default(0);
            $table->decimal('min_stock', 18, 3)->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['category_id', 'name']);
        });

        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('code', 50)->unique();
            $table->string('address')->nullable();
            $table->foreignId('responsible_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('counterparties', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->default('both')->index(); // supplier, buyer, both
            $table->string('name', 255)->index();
            $table->string('inn', 30)->nullable()->index();
            $table->string('phone', 30)->nullable()->index();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->decimal('credit_limit', 18, 2)->default(0);
            $table->unsignedInteger('payment_days')->default(0);
            $table->text('note')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('cash_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->index(); // cash, bank
            $table->string('name', 150);
            $table->string('currency', 3)->default('KGS')->index();
            $table->decimal('opening_balance', 18, 2)->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('expense_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->unique();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('expense_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_type_id')->constrained('expense_types')->cascadeOnDelete();
            $table->string('name', 150);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['expense_type_id', 'name']);
        });

        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('number', 50)->unique();
            $table->foreignId('supplier_id')->constrained('counterparties')->restrictOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->date('document_date')->index();
            $table->date('due_date')->nullable()->index();
            $table->string('status', 20)->default('draft')->index(); // draft, posted, cancelled
            $table->string('currency', 3)->default('KGS');
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('discount', 18, 2)->default(0);
            $table->decimal('total', 18, 2)->default(0);
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['supplier_id', 'document_date']);
        });

        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('purchases')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->decimal('quantity', 18, 3);
            $table->decimal('unit_price', 18, 4);
            $table->decimal('discount', 18, 2)->default(0);
            $table->decimal('line_total', 18, 2);
            $table->timestamps();
            $table->unique(['purchase_id', 'product_id']);
        });

        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('number', 50)->unique();
            $table->foreignId('buyer_id')->nullable()->constrained('counterparties')->nullOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->date('document_date')->index();
            $table->date('due_date')->nullable()->index();
            $table->string('status', 20)->default('draft')->index(); // draft, posted, cancelled
            $table->string('currency', 3)->default('KGS');
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('discount', 18, 2)->default(0);
            $table->decimal('total', 18, 2)->default(0);
            $table->decimal('cost_total', 18, 2)->default(0);
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['buyer_id', 'document_date']);
        });

        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->decimal('quantity', 18, 3);
            $table->decimal('unit_price', 18, 4);
            $table->decimal('unit_cost', 18, 4)->default(0);
            $table->decimal('discount', 18, 2)->default(0);
            $table->decimal('line_total', 18, 2);
            $table->timestamps();
            $table->unique(['sale_id', 'product_id']);
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->string('movement_type', 30)->index(); // purchase, sale, transfer_in/out, write_off, inventory, return
            $table->nullableMorphs('document');
            $table->decimal('quantity', 18, 3); // positive = income, negative = outcome
            $table->decimal('unit_cost', 18, 4)->default(0);
            $table->dateTime('occurred_at')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['warehouse_id', 'product_id', 'occurred_at'], 'stock_balance_lookup');
        });

        Schema::create('money_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('number', 50)->unique();
            $table->string('direction', 10)->index(); // income, expense
            $table->foreignId('cash_account_id')->constrained('cash_accounts')->restrictOnDelete();
            $table->foreignId('counterparty_id')->nullable()->constrained('counterparties')->nullOnDelete();
            $table->nullableMorphs('document');
            $table->date('transaction_date')->index();
            $table->decimal('amount', 18, 2);
            $table->string('currency', 3)->default('KGS');
            $table->string('payment_method', 30)->default('cash')->index();
            $table->string('status', 20)->default('posted')->index();
            $table->text('comment')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['counterparty_id', 'transaction_date']);
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('money_transaction_id')->unique()->constrained('money_transactions')->cascadeOnDelete();
            $table->foreignId('expense_type_id')->constrained('expense_types')->restrictOnDelete();
            $table->foreignId('expense_item_id')->constrained('expense_items')->restrictOnDelete();
            $table->string('purpose')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_counts', function (Blueprint $table) {
            $table->id();
            $table->string('number', 50)->unique();
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->date('count_date')->index();
            $table->string('status', 20)->default('draft')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('posted_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_count_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_count_id')->constrained('inventory_counts')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->decimal('system_quantity', 18, 3);
            $table->decimal('actual_quantity', 18, 3);
            $table->decimal('difference', 18, 3);
            $table->timestamps();
            $table->unique(['inventory_count_id', 'product_id'], 'inventory_product_unique');
        });

        Schema::create('scheduled_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('counterparty_id')->nullable()->constrained('counterparties')->nullOnDelete();
            $table->nullableMorphs('document');
            $table->string('channel', 20)->default('system')->index(); // system, sms
            $table->string('recipient', 100);
            $table->text('message');
            $table->dateTime('scheduled_at')->index();
            $table->dateTime('sent_at')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 50)->index();
            $table->nullableMorphs('entity');
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->index(['created_at', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('scheduled_notifications');
        Schema::dropIfExists('inventory_count_items');
        Schema::dropIfExists('inventory_counts');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('money_transactions');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('expense_items');
        Schema::dropIfExists('expense_types');
        Schema::dropIfExists('cash_accounts');
        Schema::dropIfExists('counterparties');
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('units');
    }
};
