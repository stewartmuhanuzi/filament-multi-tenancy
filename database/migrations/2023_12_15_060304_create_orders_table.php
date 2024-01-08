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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')
                ->nullable()
                ->constrained('teams')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('customer_id')
                ->nullable()
                ->constrained('customers')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('number');
            $table->decimal('total_price', 10, 2);
            $table->string('status')->nullable();
            $table->decimal('shopping_price')->nullable();
            $table->longText('notes');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
