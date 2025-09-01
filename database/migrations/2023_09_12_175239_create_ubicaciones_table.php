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
        Schema::create('ubicaciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('deposito');
            $table->foreignId('id_racks')->nullable()->constrained('racks')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('id_niveles')->nullable()->constrained('niveles')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('id_posiciones')->nullable()->constrained('posiciones')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('id_lado')->nullable()->constrained('lados')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ubicaciones');
    }
};
