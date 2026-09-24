<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_incoming_messages', function (Blueprint $table) {
            $table->id();
            $table->string('message_id', 191)->unique();
            $table->unsignedBigInteger('customer_id');
            $table->date('date');
            $table->unsignedBigInteger('sent_at');
            $table->unsignedInteger('pages');
            $table->timestamps();

            $table->index(['customer_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_incoming_messages');
    }
};

