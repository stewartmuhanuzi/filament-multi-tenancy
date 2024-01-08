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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')
                ->constrained('teams')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('brand_id')
                ->constrained('brands')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('name');
            $table->string('slug');
            $table->string('sku');
            $table->string('image')->nullable();
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('quantity');
            $table->decimal('price', 10, 2);
            $table->boolean('is_visible')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->string('type')->nullable();
            $table->date('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
