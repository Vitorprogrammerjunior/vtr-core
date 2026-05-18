<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exercises', function (Blueprint $t) {
            $t->string('tipo', 20)->default('forca')->after('icone'); // forca | cardio | abdomen | aquecimento
            $t->unsignedSmallInteger('rep_min')->nullable()->after('series');
            $t->unsignedSmallInteger('rep_max')->nullable()->after('rep_min');
            $t->unsignedSmallInteger('segundos_min')->nullable()->after('rep_max');
            $t->unsignedSmallInteger('segundos_max')->nullable()->after('segundos_min');
            $t->boolean('por_lado')->default(false)->after('segundos_max');
            $t->string('observacao')->nullable()->after('por_lado');
        });

        Schema::create('exercise_sets', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('exercise_id')->constrained()->cascadeOnDelete();
            $t->date('data');
            $t->unsignedTinyInteger('serie_num');
            $t->boolean('feita')->default(false);
            $t->decimal('carga', 6, 2)->nullable();
            $t->unsignedSmallInteger('reps')->nullable();
            $t->unsignedSmallInteger('segundos')->nullable();
            $t->timestamps();

            $t->unique(['exercise_id', 'data', 'serie_num'], 'exercise_sets_unique');
            $t->index(['user_id', 'data']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercise_sets');
        Schema::table('exercises', function (Blueprint $t) {
            $t->dropColumn(['tipo', 'rep_min', 'rep_max', 'segundos_min', 'segundos_max', 'por_lado', 'observacao']);
        });
    }
};
