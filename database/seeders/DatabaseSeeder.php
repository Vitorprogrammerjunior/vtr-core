<?php

namespace Database\Seeders;

use App\Models\{
    User, Profile, Goal, Habit, HabitLog, Streak,
    Workout, Exercise, ExerciseSet, WorkoutSession,
    Meal, MealLog, NutritionLog, WaterLog,
    Book, Page
};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'vitor@vtrcore.app'],
            [
                'name'     => 'Vitor',
                'password' => Hash::make('vtrcore'),
            ]
        );

        // ============== PROFILE ==============
        Profile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'frase_principal'    => 'Disciplina hoje, liberdade amanhã.',
                'foco'               => 'Máximo',
                'status'             => 'Ativo',
                'vtr_number'         => '07',
                'modo_disciplina_on' => true,
                'avatar_path'        => 'images/dashboard-hero.png',
                'meta_calorias'      => 2400,
                'meta_proteina_g'    => 150,
                'meta_carbo_g'       => 250,
                'meta_gordura_g'     => 65,
                'meta_agua_litros'   => 3.0,
            ]
        );

        Streak::updateOrCreate(
            ['user_id' => $user->id],
            ['dias' => 27, 'ultimo_dia' => now()->toDateString()]
        );

        // Limpa & recria os domínios escopados (idempotência)
        $this->resetDomain($user->id);

        // ============== OBJETIVOS ==============
        Goal::create([
            'user_id'   => $user->id,
            'tipo'      => 'main',
            'categoria' => 'fisico',
            'titulo'    => 'FÍSICO 90KG LEAN',
            'prazo'     => '2024-12-31',
            'progresso' => 72,
            'frase'     => 'Não é sobre ser motivado todos os dias, é sobre ser disciplinado todos os dias.',
        ]);

        Goal::create([
            'user_id'                => $user->id,
            'tipo'                   => 'foco',
            'titulo'                 => 'CONSTÂNCIA',
            'subtitulo'              => 'Não quebre a cadeia',
            'total_marcadores'       => 5,
            'marcadores_concluidos'  => 4,
        ]);

        // Metas ativas (CRUD)
        $ativas = [
            ['categoria' => 'fisico',     'titulo' => 'Peso ideal',         'progresso' => 72, 'prazo' => '2024-12-31'],
            ['categoria' => 'leitura',    'titulo' => 'Ler 12 livros',      'progresso' => 58, 'prazo' => '2024-12-31'],
            ['categoria' => 'financeiro', 'titulo' => 'Reserva financeira', 'progresso' => 41, 'prazo' => '2025-06-30'],
            ['categoria' => 'corrida',    'titulo' => 'Corrida 5km',        'progresso' => 80, 'prazo' => '2024-08-15'],
        ];
        foreach ($ativas as $i => $a) {
            Goal::create([
                'user_id'   => $user->id,
                'tipo'      => 'ativa',
                'categoria' => $a['categoria'],
                'titulo'    => $a['titulo'],
                'progresso' => $a['progresso'],
                'prazo'     => $a['prazo'],
                'ordem'     => $i + 1,
            ]);
        }

        // ============== HÁBITOS ==============
        $habits = [
            ['titulo' => 'Acordar 5h30',       'ordem' => 1],
            ['titulo' => 'Treinar',            'ordem' => 2],
            ['titulo' => 'Estudar 1h',         'ordem' => 3],
            ['titulo' => 'Meditar',            'ordem' => 4],
            ['titulo' => 'Sem redes sociais',  'ordem' => 5],
        ];
        foreach ($habits as $h) {
            $habit = Habit::create([
                'user_id'    => $user->id,
                'titulo'     => $h['titulo'],
                'frequencia' => 'daily',
                'ativo'      => true,
                'ordem'      => $h['ordem'],
            ]);
            HabitLog::create([
                'user_id'  => $user->id,
                'habit_id' => $habit->id,
                'data'     => now()->toDateString(),
                'feito'    => true,
            ]);
        }

        // ============== TREINOS (split semanal: Seg→Sex; Sab/Dom = descanso) ==============
        $splits = [
            // dia_semana ISO: 1=seg ... 7=dom
            1 => ['nome' => 'Superior A',     'icone' => 'biceps',   'intensidade' => 'PEITO + COSTAS + OMBROS + BRAÇOS'],
            2 => ['nome' => 'Inferior A',     'icone' => 'run',      'intensidade' => 'QUADRÍCEPS + POSTERIOR + PANTURRILHA'],
            3 => ['nome' => 'Superior B',     'icone' => 'biceps',   'intensidade' => 'PEITO + COSTAS + OMBROS + BRAÇOS'],
            4 => ['nome' => 'Inferior B',     'icone' => 'run',      'intensidade' => 'POSTERIOR + GLÚTEO + QUADRÍCEPS'],
            5 => ['nome' => 'Cardio leve',    'icone' => 'run',      'intensidade' => 'MOBILIDADE + RECUPERAÇÃO ATIVA'],
        ];
        $workouts = [];
        foreach ($splits as $dia => $s) {
            $workouts[$dia] = Workout::create([
                'user_id'        => $user->id,
                'nome'           => $s['nome'],
                'grupo_muscular' => $s['intensidade'],
                'dia_semana'     => $dia,
                'icone'          => $s['icone'],
                'intensidade'    => $s['intensidade'],
                'ativo'          => true,
                'ordem'          => $dia,
            ]);
        }

        // Tipo: forca | abdomen | cardio | aquecimento
        $exsBy = [
            // ===== Treino 1 — Superior A (segunda) =====
            1 => [
                ['nome' => 'Supino reto ou máquina de peito',     'tipo' => 'forca',   'series' => 4, 'rep_min' => 6,  'rep_max' => 10, 'icone' => 'dumbbell'],
                ['nome' => 'Puxada na frente / pulley',           'tipo' => 'forca',   'series' => 4, 'rep_min' => 8,  'rep_max' => 12, 'icone' => 'biceps'],
                ['nome' => 'Desenvolvimento de ombro',            'tipo' => 'forca',   'series' => 3, 'rep_min' => 8,  'rep_max' => 10, 'icone' => 'dumbbell'],
                ['nome' => 'Remada baixa ou remada máquina',      'tipo' => 'forca',   'series' => 3, 'rep_min' => 8,  'rep_max' => 12, 'icone' => 'biceps'],
                ['nome' => 'Tríceps corda',                       'tipo' => 'forca',   'series' => 3, 'rep_min' => 10, 'rep_max' => 15, 'icone' => 'biceps'],
                ['nome' => 'Rosca direta ou alternada',           'tipo' => 'forca',   'series' => 3, 'rep_min' => 10, 'rep_max' => 12, 'icone' => 'biceps'],
                ['nome' => 'Prancha',                             'tipo' => 'abdomen', 'series' => 3, 'segundos_min' => 30, 'segundos_max' => 60, 'icone' => 'clock'],
                ['nome' => 'Abdominal na polia ou máquina',       'tipo' => 'abdomen', 'series' => 3, 'rep_min' => 10, 'rep_max' => 15, 'icone' => 'biceps'],
            ],
            // ===== Treino 2 — Inferior A (terça) =====
            2 => [
                ['nome' => 'Agachamento livre, hack ou leg press','tipo' => 'forca',   'series' => 4, 'rep_min' => 6,  'rep_max' => 10, 'icone' => 'run'],
                ['nome' => 'Cadeira extensora',                   'tipo' => 'forca',   'series' => 3, 'rep_min' => 10, 'rep_max' => 15, 'icone' => 'run'],
                ['nome' => 'Mesa flexora',                        'tipo' => 'forca',   'series' => 3, 'rep_min' => 10, 'rep_max' => 15, 'icone' => 'run'],
                ['nome' => 'Stiff ou levantamento terra romeno',  'tipo' => 'forca',   'series' => 3, 'rep_min' => 8,  'rep_max' => 10, 'icone' => 'dumbbell'],
                ['nome' => 'Panturrilha em pé ou sentado',        'tipo' => 'forca',   'series' => 4, 'rep_min' => 12, 'rep_max' => 20, 'icone' => 'run'],
                ['nome' => 'Cardio final (esteira/bike/escada)',  'tipo' => 'cardio',  'series' => 1, 'segundos_min' => 600, 'segundos_max' => 900, 'icone' => 'run', 'observacao' => '10–15 min inclinado'],
            ],
            // ===== Treino 3 — Superior B (quarta) =====
            3 => [
                ['nome' => 'Supino inclinado halteres ou máquina','tipo' => 'forca',   'series' => 4, 'rep_min' => 8,  'rep_max' => 12, 'icone' => 'dumbbell'],
                ['nome' => 'Remada curvada, cavalinho ou máquina','tipo' => 'forca',   'series' => 4, 'rep_min' => 8,  'rep_max' => 12, 'icone' => 'biceps'],
                ['nome' => 'Elevação lateral',                    'tipo' => 'forca',   'series' => 4, 'rep_min' => 12, 'rep_max' => 20, 'icone' => 'dumbbell'],
                ['nome' => 'Puxada neutra ou barra assistida',    'tipo' => 'forca',   'series' => 3, 'rep_min' => 8,  'rep_max' => 12, 'icone' => 'biceps'],
                ['nome' => 'Crucifixo máquina ou crossover',      'tipo' => 'forca',   'series' => 3, 'rep_min' => 12, 'rep_max' => 15, 'icone' => 'dumbbell'],
                ['nome' => 'Bíceps + tríceps em bi-set',          'tipo' => 'forca',   'series' => 3, 'rep_min' => 10, 'rep_max' => 15, 'icone' => 'biceps', 'observacao' => 'Bi-set: cada exercício no mesmo bloco'],
                ['nome' => 'Elevação de pernas',                  'tipo' => 'abdomen', 'series' => 3, 'rep_min' => 10, 'rep_max' => 15, 'icone' => 'run'],
                ['nome' => 'Prancha lateral',                     'tipo' => 'abdomen', 'series' => 3, 'segundos_min' => 30, 'segundos_max' => 45, 'por_lado' => true, 'icone' => 'clock', 'observacao' => 'Cada lado'],
            ],
            // ===== Treino 4 — Inferior B (quinta) =====
            4 => [
                ['nome' => 'Terra romeno ou stiff',               'tipo' => 'forca',   'series' => 4, 'rep_min' => 6,  'rep_max' => 10, 'icone' => 'dumbbell'],
                ['nome' => 'Leg press',                           'tipo' => 'forca',   'series' => 4, 'rep_min' => 10, 'rep_max' => 12, 'icone' => 'run'],
                ['nome' => 'Afundo, passada ou búlgaro',          'tipo' => 'forca',   'series' => 3, 'rep_min' => 8,  'rep_max' => 12, 'por_lado' => true, 'icone' => 'run', 'observacao' => 'Cada perna'],
                ['nome' => 'Flexora',                             'tipo' => 'forca',   'series' => 3, 'rep_min' => 10, 'rep_max' => 15, 'icone' => 'run'],
                ['nome' => 'Glúteo máquina ou cabo',              'tipo' => 'forca',   'series' => 3, 'rep_min' => 12, 'rep_max' => 15, 'icone' => 'run'],
                ['nome' => 'Panturrilha',                         'tipo' => 'forca',   'series' => 4, 'rep_min' => 12, 'rep_max' => 20, 'icone' => 'run'],
                ['nome' => 'Cardio final moderado',               'tipo' => 'cardio',  'series' => 1, 'segundos_min' => 600, 'segundos_max' => 900, 'icone' => 'run', 'observacao' => '10–15 min moderado'],
            ],
            // ===== Sex — Cardio leve =====
            5 => [
                ['nome' => 'Esteira / bike / caminhada',          'tipo' => 'cardio',  'series' => 1, 'segundos_min' => 1500, 'segundos_max' => 1800, 'icone' => 'run', 'observacao' => '25–30 min ritmo confortável'],
                ['nome' => 'Mobilidade + alongamento',            'tipo' => 'aquecimento','series' => 1, 'segundos_min' => 600, 'segundos_max' => 900, 'icone' => 'leaf', 'observacao' => 'Articulações + cadeia posterior'],
            ],
        ];

        foreach ($exsBy as $dia => $exs) {
            foreach ($exs as $i => $e) {
                Exercise::create(array_merge($e, [
                    'user_id'    => $user->id,
                    'workout_id' => $workouts[$dia]->id,
                    'ordem'      => $i + 1,
                ]));
            }
        }

        // ============== ALIMENTAÇÃO ==============
        // dia_semana: null = todos os dias; 0..6 = seg..dom
        $refeicoes = [
            ['nome' => 'Café da manhã', 'horario' => '07:00:00', 'descricao' => 'Ovos + aveia + banana',         'icone' => 'cutlery', 'dia_semana' => null, 'ordem' => 1],
            ['nome' => 'Pré-treino',    'horario' => '09:30:00', 'descricao' => 'Café + tapioca com mel',        'icone' => 'leaf',    'dia_semana' => 1,    'ordem' => 2], // ter
            ['nome' => 'Pré-treino',    'horario' => '09:30:00', 'descricao' => 'Café + tapioca com mel',        'icone' => 'leaf',    'dia_semana' => 3,    'ordem' => 2], // qui
            ['nome' => 'Almoço',        'horario' => '12:30:00', 'descricao' => 'Arroz, frango e legumes',       'icone' => 'cutlery', 'dia_semana' => null, 'ordem' => 3],
            ['nome' => 'Lanche',        'horario' => '16:00:00', 'descricao' => 'Iogurte + castanhas',           'icone' => 'cutlery', 'dia_semana' => null, 'ordem' => 4],
            ['nome' => 'Jantar',        'horario' => '19:30:00', 'descricao' => 'Peixe + batata-doce + salada',  'icone' => 'cutlery', 'dia_semana' => null, 'ordem' => 5],
            ['nome' => 'Ceia',          'horario' => '22:00:00', 'descricao' => 'Queijo cottage + chia',         'icone' => 'drop',    'dia_semana' => 0,    'ordem' => 6], // seg
            ['nome' => 'Ceia',          'horario' => '22:00:00', 'descricao' => 'Queijo cottage + chia',         'icone' => 'drop',    'dia_semana' => 2,    'ordem' => 6], // qua
            ['nome' => 'Ceia',          'horario' => '22:00:00', 'descricao' => 'Queijo cottage + chia',         'icone' => 'drop',    'dia_semana' => 4,    'ordem' => 6], // sex
            ['nome' => 'Pizza dia livre','horario'=> '20:00:00', 'descricao' => 'Refeição livre — comer com presença', 'icone' => 'leaf', 'dia_semana' => 5, 'ordem' => 7], // sab
        ];
        $today = now()->toDateString();
        $hojeDow = (int) now()->dayOfWeek; // 0=dom..6=sab; convertemos:
        $hojeIso = ($hojeDow + 6) % 7; // 0=seg..6=dom
        foreach ($refeicoes as $r) {
            $meal = Meal::create(array_merge($r, [
                'user_id' => $user->id, 'ativo' => true,
            ]));
            // Marca como feita só se aplica hoje
            $aplicaHoje = $r['dia_semana'] === null || $r['dia_semana'] === $hojeIso;
            if ($aplicaHoje) {
                MealLog::create([
                    'user_id' => $user->id,
                    'meal_id' => $meal->id,
                    'data'    => $today,
                    'feita'   => true,
                ]);
            }
        }

        NutritionLog::updateOrCreate(
            ['user_id' => $user->id, 'data' => $today],
            ['kcal_consumido' => 1850, 'proteina_g' => 142, 'carbo_g' => 210, 'gordura_g' => 58]
        );

        WaterLog::updateOrCreate(
            ['user_id' => $user->id, 'data' => $today],
            ['copos' => 4, 'litros' => 2.30]
        );
    }

    /**
     * Limpa registros antigos do usuário antes de re-seedar (idempotente em dev).
     * Usa withoutGlobalScopes pra alcançar tudo independente do auth.
     */
    private function resetDomain(int $userId): void
    {
        $models = [
            HabitLog::class, Habit::class,
            WorkoutSession::class, ExerciseSet::class, Exercise::class, Workout::class,
            MealLog::class, Meal::class, NutritionLog::class, WaterLog::class,
            Page::class, Book::class,
            Goal::class,
        ];
        foreach ($models as $cls) {
            /** @var class-string<Model> $cls */
            $cls::withoutGlobalScopes()->where('user_id', $userId)->forceDelete();
        }
    }
}
