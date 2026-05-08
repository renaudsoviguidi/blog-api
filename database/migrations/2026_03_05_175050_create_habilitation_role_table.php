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
        Schema::create('habilitation_role', function (Blueprint $table) {
            $table->foreignId('role_id')->cascadeOnDelete();
            $table->foreignId('habilitation_id')->cascadeOnDelete();

            $table->primary(['role_id', 'habilitation_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habilitation_role');
    }
};
