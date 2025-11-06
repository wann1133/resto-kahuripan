<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Store order headers and lifecycle status
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_session_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->decimal('subtotal', 12, 2)->unsigned();
            $table->decimal('tax', 12, 2)->default(0)->unsigned();
            $table->decimal('service_charge', 12, 2)->default(0)->unsigned();
            $table->decimal('grand_total', 12, 2)->unsigned();
            $table->string('status')->default('PLACED');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
