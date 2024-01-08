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
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')
                ->nullable()
                ->constrained('teams')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('title');
            $table->text('description');
            $table->string('status')->default('open');
            $table->string('priority')->default('medium');
            $table->date('due_date')->nullable();
            $table->string('attachments')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
