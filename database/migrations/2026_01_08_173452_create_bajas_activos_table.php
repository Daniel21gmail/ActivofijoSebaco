<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bajas_activos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activo_fijo_id')->unique()->constrained('activos_fijos');
            $table->date('fecha_baja');
            $table->foreignId('autorizado_por')->constrained('users');
            $table->foreignId('creado_por')->constrained('users');
            $table->string('motivo', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bajas_activos');
    }
};
