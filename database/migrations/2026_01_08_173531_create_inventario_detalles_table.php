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
        Schema::create('inventario_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventario_id')->constrained('inventarios');
            $table->foreignId('activo_fijo_id')->constrained('activos_fijos');
            $table->foreignId('ubicacion_origen_id')->constrained('ubicaciones');
            $table->foreignId('ubicacion_verificada_id')->constrained('ubicaciones');
            $table->foreignId('responsable_verificado_id')->constrained('personal_responsables');
            $table->foreignId('estado_fisico_id')->constrained('estados_activos');
            $table->boolean('verificado')->default(false);
            $table->foreignId('verificado_por')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventario_detalles');
    }
};
