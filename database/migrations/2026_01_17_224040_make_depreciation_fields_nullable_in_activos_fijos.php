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
            $table->integer('vida_util')->nullable()->change();
            $table->decimal('valor_residual', 15, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activos_fijos', function (Blueprint $table) {
            $table->integer('vida_util')->nullable(false)->change();
            $table->decimal('valor_residual', 15, 2)->nullable(false)->change();
        });
    }
};
