<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Обычный индекс также нужен, если есть внешний ключ.
            $table->index('bolge_id', 'customers_bolge_id_index');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique('customers_bolge_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->unique('bolge_id', 'customers_bolge_id_unique');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('customers_bolge_id_index');
        });
    }
};
