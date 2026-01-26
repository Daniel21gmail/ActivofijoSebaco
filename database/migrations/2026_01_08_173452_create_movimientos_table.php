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
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activo_fijo_id')->constrained('activos_fijos');
            $table->dateTime('fecha_movimiento');
            $table->foreignId('ubicacion_origen_id')->constrained('ubicaciones');
            $table->foreignId('ubicacion_destino_id')->constrained('ubicaciones');
            $table->foreignId('responsable_origen_id')->constrained('personal_responsables');
            $table->foreignId('responsable_destino_id')->constrained('personal_responsables');
            $table->foreignId('autorizado_por')->constrained('users');
            $table->foreignId('creado_por')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
