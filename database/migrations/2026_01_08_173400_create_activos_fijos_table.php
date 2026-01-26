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
        Schema::create('activos_fijos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('descripcion', 255);
            $table->date('fecha_adquisicion');
            $table->decimal('valor_adquisicion', 15, 2);
            $table->integer('vida_util');
            $table->decimal('valor_residual', 15, 2);

            $table->foreignId('marca_id')->constrained('marcas');
            $table->foreignId('modelo_id')->constrained('modelos');
            $table->foreignId('color_id')->constrained('colores');
            $table->foreignId('fuente_id')->constrained('fuentes');
            $table->foreignId('proveedor_id')->constrained('proveedores');
            $table->foreignId('responsable_id')->constrained('personal_responsables');
            $table->foreignId('estado_activo_id')->constrained('estados_activos');
            $table->foreignId('ubicacion_id')->constrained('ubicaciones');
            $table->foreignId('creado_por')->constrained('users');
            $table->foreignId('modificado_por')->nullable()->constrained('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activo_fijos');
    }
};
