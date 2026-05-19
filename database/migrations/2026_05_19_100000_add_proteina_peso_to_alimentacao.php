<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Proteína estimada por refeição (template)
        Schema::table('meals', function (Blueprint $t) {
            $t->unsignedSmallInteger('proteina_g')->default(0)->after('descricao');
        });

        // Proteína registrada na execução do dia (copiada do template no toggle)
        Schema::table('meal_logs', function (Blueprint $t) {
            $t->unsignedSmallInteger('proteina_g')->nullable()->after('kcal');
        });

        // Peso corporal para calcular meta de proteína automaticamente
        Schema::table('profiles', function (Blueprint $t) {
            $t->decimal('peso_kg', 5, 1)->nullable()->after('meta_agua_litros');
        });
    }

    public function down(): void
    {
        Schema::table('meals', fn (Blueprint $t) => $t->dropColumn('proteina_g'));
        Schema::table('meal_logs', fn (Blueprint $t) => $t->dropColumn('proteina_g'));
        Schema::table('profiles', fn (Blueprint $t) => $t->dropColumn('peso_kg'));
    }
};
