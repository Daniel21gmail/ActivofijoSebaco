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
        Schema::table('activos_fijos', function (Blueprint $table) {
            $table->string('serie', 100)->nullable()->after('descripcion');
            $table->foreignId('categoria_id')->nullable()->after('serie')->constrained('categorias');
            $table->foreignId('departamento_id')->nullable()->after('categoria_id')->constrained('departamentos');
            $table->string('imagen')->nullable()->after('estado_activo_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activos_fijos', function (Blueprint $table) {
            $table->dropForeign(['categoria_id']);
            $table->dropForeign(['departamento_id']);
            $table->dropColumn(['serie', 'categoria_id', 'departamento_id', 'imagen']);
        });
    }
};
