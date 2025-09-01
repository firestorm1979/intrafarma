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
        Schema::create('ocupaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_ubicaciones')->nullable()->constrained('ubicaciones')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('id_productos')->nullable()->constrained('productos')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('id_lotes')->nullable()->constrained('lotes')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('id_estados')->nullable()->constrained('estados')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ocupaciones');
    }
};
