<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ============== PROFILE (1:1 com User) ==============
        Schema::create('profiles', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            // Identidade / hero
            $t->string('frase_principal')->default('Disciplina hoje, liberdade amanhã.');
            $t->string('foco')->default('Máximo');
            $t->string('status')->default('Ativo');
            $t->string('vtr_number', 10)->default('07');
            $t->boolean('modo_disciplina_on')->default(true);
            $t->string('avatar_path')->nullable();

            // Metas nutricionais
            $t->unsignedSmallInteger('meta_calorias')->default(2400);
            $t->unsignedSmallInteger('meta_proteina_g')->default(150);
            $t->unsignedSmallInteger('meta_carbo_g')->default(250);
            $t->unsignedSmallInteger('meta_gordura_g')->default(65);
            $t->decimal('meta_agua_litros', 3, 1)->default(3.0);

            $t->timestamps();
        });

        // ============== OBJETIVOS / HÁBITOS ==============
        Schema::create('goals', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('tipo')->default('main'); // main | foco | secundario
            $t->string('categoria')->nullable(); // fisico | financeiro | mental | etc
            $t->string('titulo');
            $t->string('subtitulo')->nullable();
            $t->date('prazo')->nullable();
            $t->unsignedTinyInteger('progresso')->default(0);
            $t->string('unidade', 20)->nullable(); // kg, R$, %, etc
            $t->decimal('meta_valor', 12, 2)->nullable();
            $t->decimal('valor_atual', 12, 2)->nullable();
            $t->text('frase')->nullable();
            $t->unsignedTinyInteger('total_marcadores')->default(0);
            $t->unsignedTinyInteger('marcadores_concluidos')->default(0);
            $t->boolean('ativo')->default(true);
            $t->unsignedSmallInteger('ordem')->default(0);
            $t->timestamps();
            $t->softDeletes();

            $t->index(['user_id', 'tipo', 'ativo']);
        });

        Schema::create('habits', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('titulo');
            $t->string('frequencia', 20)->default('daily'); // daily | weekly
            $t->boolean('ativo')->default(true);
            $t->unsignedSmallInteger('ordem')->default(0);
            $t->timestamps();
            $t->softDeletes();

            $t->index(['user_id', 'ativo']);
        });

        Schema::create('habit_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('habit_id')->constrained()->cascadeOnDelete();
            $t->date('data');
            $t->boolean('feito')->default(true);
            $t->timestamps();

            $t->unique(['habit_id', 'data']);
            $t->index(['user_id', 'data']);
        });

        Schema::create('streaks', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $t->unsignedInteger('dias')->default(0);
            $t->date('ultimo_dia')->nullable();
            $t->timestamps();
        });

        // ============== TREINOS ==============
        Schema::create('workouts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('nome'); // ex: "Peito + Tríceps"
            $t->string('grupo_muscular')->nullable();
            $t->unsignedTinyInteger('dia_semana')->nullable(); // 1=seg ... 7=dom
            $t->string('intensidade', 20)->nullable(); // leve | moderado | intenso
            $t->string('icone', 30)->nullable(); // dumbbell | biceps | run...
            $t->boolean('ativo')->default(true);
            $t->unsignedSmallInteger('ordem')->default(0);
            $t->timestamps();
            $t->softDeletes();

            $t->index(['user_id', 'ativo']);
        });

        Schema::create('exercises', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete(); // denormalizado p/ scope
            $t->foreignId('workout_id')->constrained()->cascadeOnDelete();
            $t->string('nome');
            $t->string('icone', 30)->nullable();
            $t->unsignedTinyInteger('series')->default(3);
            $t->string('reps', 30)->nullable(); // "4x8", "3x falha"
            $t->unsignedSmallInteger('ordem')->default(0);
            $t->timestamps();
            $t->softDeletes();

            $t->index('workout_id');
        });

        Schema::create('workout_sessions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('workout_id')->constrained()->cascadeOnDelete();
            $t->dateTime('agendado_em')->nullable();
            $t->dateTime('concluido_em')->nullable();
            $t->unsignedTinyInteger('percent')->default(0);
            $t->unsignedTinyInteger('exercicios_feitos')->default(0);
            $t->unsignedTinyInteger('exercicios_total')->default(0);
            $t->text('frase')->nullable();
            $t->timestamps();
            $t->softDeletes();

            $t->index(['user_id', 'agendado_em']);
            $t->index(['user_id', 'concluido_em']);
        });

        // ============== ALIMENTAÇÃO ==============
        // 'meals' = plano (template). 'meal_logs' = execução por dia. 'nutrition_logs' = totais por dia.
        Schema::create('meals', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('nome'); // Café da manhã, Almoço, Lanche...
            $t->time('horario')->nullable();
            $t->string('descricao')->nullable();
            $t->string('icone', 30)->default('cutlery');
            // null = aplica todos os dias; 0..6 = ISO (1=seg ... 7=dom usando ISO; aqui 0..6 = seg..dom)
            $t->unsignedTinyInteger('dia_semana')->nullable();
            $t->boolean('ativo')->default(true);
            $t->unsignedSmallInteger('ordem')->default(0);
            $t->timestamps();
            $t->softDeletes();

            $t->index(['user_id', 'ativo', 'ordem']);
            $t->index(['user_id', 'dia_semana']);
        });

        Schema::create('meal_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('meal_id')->constrained()->cascadeOnDelete();
            $t->date('data');
            $t->boolean('feita')->default(true);
            $t->unsignedSmallInteger('kcal')->nullable();
            $t->timestamps();

            $t->unique(['meal_id', 'data']);
            $t->index(['user_id', 'data']);
        });

        Schema::create('nutrition_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->date('data');
            $t->unsignedSmallInteger('kcal_consumido')->default(0);
            $t->unsignedSmallInteger('proteina_g')->default(0);
            $t->unsignedSmallInteger('carbo_g')->default(0);
            $t->unsignedSmallInteger('gordura_g')->default(0);
            $t->timestamps();

            $t->unique(['user_id', 'data']);
        });

        Schema::create('water_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->date('data');
            $t->unsignedTinyInteger('copos')->default(0);
            $t->decimal('litros', 4, 2)->default(0);
            $t->timestamps();

            $t->unique(['user_id', 'data']);
        });

        // ============== ANOTAÇÕES ==============
        Schema::create('books', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('titulo');
            $t->string('status', 20)->default('em_andamento'); // em_andamento | pausado | concluido
            $t->unsignedSmallInteger('ordem')->default(0);
            $t->timestamps();
            $t->softDeletes();

            $t->index(['user_id', 'status']);
        });

        Schema::create('pages', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete(); // denormalizado p/ scope
            $t->foreignId('book_id')->constrained()->cascadeOnDelete();
            $t->unsignedSmallInteger('numero')->default(1);
            $t->string('titulo');
            $t->longText('conteudo')->nullable();
            $t->unsignedSmallInteger('ordem')->default(0);
            $t->timestamps();
            $t->softDeletes();

            $t->index(['book_id', 'ordem']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
        Schema::dropIfExists('books');
        Schema::dropIfExists('water_logs');
        Schema::dropIfExists('nutrition_logs');
        Schema::dropIfExists('meal_logs');
        Schema::dropIfExists('meals');
        Schema::dropIfExists('workout_sessions');
        Schema::dropIfExists('exercises');
        Schema::dropIfExists('workouts');
        Schema::dropIfExists('streaks');
        Schema::dropIfExists('habit_logs');
        Schema::dropIfExists('habits');
        Schema::dropIfExists('goals');
        Schema::dropIfExists('profiles');
    }
};
