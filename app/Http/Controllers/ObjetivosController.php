<?php

namespace App\Http\Controllers;

use App\Models\{Profile, Goal, Streak, Habit, HabitLog};
use Illuminate\Support\Facades\Auth;

class ObjetivosController extends Controller
{
    private const CATEGORIA_ICONE = [
        'fisico'     => 'dumbbell',
        'financeiro' => 'bank',
        'mental'     => 'book',
        'rotina'     => 'checklist',
        'corrida'    => 'run',
        'forca'      => 'biceps',
        'leitura'    => 'book',
        'dinheiro'   => 'dollar',
        'saude'      => 'heart',
    ];

    /** Agrupamento de categoria -> área macro (Corpo / Mente / Finanças / Rotina). */
    private const CATEGORIA_AREA = [
        'fisico'     => 'corpo',
        'corrida'    => 'corpo',
        'forca'      => 'corpo',
        'saude'      => 'corpo',
        'mental'     => 'mente',
        'leitura'    => 'mente',
        'financeiro' => 'financas',
        'dinheiro'   => 'financas',
        'rotina'     => 'rotina',
    ];

    public function index()
    {
        $user = Auth::user();
        $profile = Profile::firstOrCreate(['user_id' => $user->id]);

        // === META PRINCIPAL ===
        $main = Goal::where('tipo', 'main')->first();
        $metaPrincipal = [
            'id'        => $main?->id,
            'titulo'    => $main?->titulo ?? '—',
            'prazo'     => $main?->prazo?->format('d/m/Y') ?? '—',
            'prazo_iso' => $main?->prazo?->format('Y-m-d') ?? '',
            'progresso' => $main?->progresso ?? 0,
            'frase'     => $main?->frase ?? '',
        ];

        // === METAS ATIVAS ===
        $metasAtivasModels = Goal::where('tipo', 'ativa')
            ->where('ativo', true)
            ->orderBy('ordem')
            ->get();

        $metasAtivas = $metasAtivasModels
            ->map(fn (Goal $g) => [
                'id'         => $g->id,
                'categoria'  => $g->categoria ?: 'target',
                'icone'      => self::CATEGORIA_ICONE[$g->categoria ?? ''] ?? ($g->categoria ?: 'target'),
                'titulo'     => $g->titulo,
                'progresso'  => (int) $g->progresso,
                'prazo_iso'  => $g->prazo?->format('Y-m-d') ?? '',
            ])
            ->all();

        // === HÁBITOS DO DIA ===
        $hoje = now()->toDateString();
        $logsHoje = HabitLog::whereDate('data', $hoje)->pluck('feito', 'habit_id');
        $habitos = Habit::where('ativo', true)->orderBy('ordem')->get()
            ->map(fn ($h) => [
                'id'     => $h->id,
                'titulo' => $h->titulo,
                'feito'  => (bool) ($logsHoje[$h->id] ?? false),
            ])
            ->all();

        // === CONSISTÊNCIA (últimos 7 dias) ===
        $streak = Streak::firstOrCreate(['user_id' => $user->id], ['dias' => 0]);
        $consistencia = [
            'percent' => $this->calcConsistencia(7),
            'dias'    => $streak->dias,
        ];

        // === PRÓXIMO PASSO DE HOJE ===
        // Loop diário: quantos hábitos faltam hoje + qual o próximo a marcar.
        $totalHabitos = count($habitos);
        $feitosHoje   = count(array_filter($habitos, fn ($h) => $h['feito']));
        $pendentes    = array_values(array_filter($habitos, fn ($h) => !$h['feito']));
        $proximoPasso = [
            'total'   => $totalHabitos,
            'feitos'  => $feitosHoje,
            'faltam'  => max(0, $totalHabitos - $feitosHoje),
            'next'    => $pendentes[0] ?? null,   // ['id','titulo','feito']
            'allDone' => $totalHabitos > 0 && $feitosHoje === $totalHabitos,
        ];

        // === ÁREAS — média do progresso das metas em cada bucket; rotina = consistência ===
        $areas = $this->buildAreas($metasAtivasModels, $consistencia['percent']);

        return view('objetivos', compact(
            'user', 'profile',
            'metaPrincipal', 'metasAtivas', 'consistencia',
            'areas', 'proximoPasso', 'habitos'
        ));
    }

    /**
     * % de hábitos marcados como "feito" nos últimos $dias dias.
     * Total esperado = nº hábitos ativos × dias.
     */
    private function calcConsistencia(int $dias): int
    {
        $total = Habit::where('ativo', true)->count() * $dias;
        if ($total <= 0) return 0;

        $feitos = HabitLog::whereDate('data', '>=', now()->subDays($dias - 1)->toDateString())
            ->where('feito', true)
            ->count();

        return (int) round(min(100, ($feitos / $total) * 100));
    }

    /**
     * Constrói as 4 áreas com base nas metas ativas (média por bucket).
     * Rotina é sempre derivada da consistência dos hábitos.
     */
    private function buildAreas(\Illuminate\Support\Collection $metas, int $rotinaPercent): array
    {
        $buckets = ['corpo' => [], 'mente' => [], 'financas' => []];
        foreach ($metas as $g) {
            $area = self::CATEGORIA_AREA[$g->categoria ?? ''] ?? null;
            if ($area && isset($buckets[$area])) {
                $buckets[$area][] = (int) $g->progresso;
            }
        }
        $avg = fn (array $vals) => empty($vals) ? null : (int) round(array_sum($vals) / count($vals));

        return [
            ['key' => 'corpo',    'icone' => 'biceps',    'titulo' => 'Corpo',    'desc' => 'Treino, energia e composição', 'progresso' => $avg($buckets['corpo']),    'count' => count($buckets['corpo'])],
            ['key' => 'mente',    'icone' => 'brain',     'titulo' => 'Mente',    'desc' => 'Leitura, foco e aprendizado',  'progresso' => $avg($buckets['mente']),    'count' => count($buckets['mente'])],
            ['key' => 'financas', 'icone' => 'dollar',    'titulo' => 'Finanças', 'desc' => 'Planejamento e segurança',     'progresso' => $avg($buckets['financas']), 'count' => count($buckets['financas'])],
            ['key' => 'rotina',   'icone' => 'checklist', 'titulo' => 'Rotina',   'desc' => 'Hábitos, agenda e disciplina', 'progresso' => $rotinaPercent,             'count' => null],
        ];
    }
}

