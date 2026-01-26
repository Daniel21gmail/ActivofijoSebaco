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
        Schema::create('mantenimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activo_fijo_id')->constrained('activos_fijos');
            $table->date('fecha_inicio');
            $table->decimal('costo', 15, 2);
            $table->foreignId('proveedor_id')->constrained('proveedores');
            $table->foreignId('responsable_id')->constrained('personal_responsables');
            $table->foreignId('creado_por')->constrained('users');
            $table->string('tipo', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimientos');
    }
};
