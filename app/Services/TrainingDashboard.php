<?php

namespace App\Services;

use App\Models\{Workout, Exercise, ExerciseSet};
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Agrega os dados da tela /treinos com tracking por série.
 * Permite navegar entre os dias da semana (passado=read-only, hoje=ativo, futuro=preview).
 */
class TrainingDashboard
{
    private const DIAS_LABEL = [1 => 'SEGUNDA', 2 => 'TERÇA', 3 => 'QUARTA', 4 => 'QUINTA', 5 => 'SEXTA', 6 => 'SÁBADO', 7 => 'DOMINGO'];
    private const DIAS_CURTO = [1 => 'SEG', 2 => 'TER', 3 => 'QUA', 4 => 'QUI', 5 => 'SEX', 6 => 'SAB', 7 => 'DOM'];

    public function build(?int $diaSelecionado = null): array
    {
        $hoje      = Carbon::today();
        $diaHoje   = $hoje->dayOfWeekIso;
        $dia       = $diaSelecionado && $diaSelecionado >= 1 && $diaSelecionado <= 7 ? $diaSelecionado : $diaHoje;
        $dataAlvo  = $hoje->copy()->startOfWeek(Carbon::MONDAY)->addDays($dia - 1);
        $dataStr   = $dataAlvo->toDateString();

        $ehHoje    = $dia === $diaHoje;
        $ehPassado = $dataAlvo->lt($hoje);
        $ehFuturo  = $dataAlvo->gt($hoje);

        $workout = Workout::where('ativo', true)
            ->where('dia_semana', $dia)
            ->with('exercises')
            ->first();

        $exercicios   = $this->exerciciosComSets($workout, $dataStr);
        $totalSeries  = (int) $exercicios->sum('series_total');
        $feitasSeries = (int) $exercicios->sum('series_feitas');
        $percent      = $totalSeries > 0 ? (int) round(($feitasSeries / $totalSeries) * 100) : 0;
        $diaConcluido = $totalSeries > 0 && $feitasSeries === $totalSeries;
        $isWeekend    = $dia >= 6;

        $treino = [
            'workout'       => $workout,
            'titulo'        => $workout?->nome ?? ($isWeekend ? 'Descanso' : 'Sem treino'),
            'intensidade'   => $workout?->intensidade ?? ($isWeekend ? 'Recuperação total' : '—'),
            'dia'           => $dia,
            'dia_label'     => self::DIAS_LABEL[$dia] ?? '',
            'dia_curto'     => self::DIAS_CURTO[$dia] ?? '',
            'data'          => $dataStr,
            'data_br'       => $dataAlvo->format('d/m'),
            'eh_hoje'       => $ehHoje,
            'eh_passado'    => $ehPassado,
            'eh_futuro'     => $ehFuturo,
            'series_feitas' => $feitasSeries,
            'series_total'  => $totalSeries,
            'percent'       => $percent,
            'concluido'     => $diaConcluido,
            'is_weekend'    => $isWeekend,
        ];

        $splitSemanal = $this->splitSemanal($dia, $diaHoje, $hoje);
        $amanha       = $this->amanha($diaHoje);
        $statusHoje   = $this->statusDia($treino, $amanha);
        $consistencia = $this->consistencia();

        return [
            'treinoHoje'     => $treino,
            'exercicios'     => $exercicios,
            'splitSemanal'   => $splitSemanal,
            'amanha'         => $amanha,
            'statusHoje'     => $statusHoje,
            'consistencia'   => $consistencia,
            'diaSelecionado' => $dia,
            'diaHoje'        => $diaHoje,
        ];
    }

    private function exerciciosComSets(?Workout $w, string $data): Collection
    {
        if (!$w) return collect();

        $exs = $w->exercises()->orderBy('ordem')->get();
        $exIds = $exs->pluck('id')->all();

        $sets = ExerciseSet::whereIn('exercise_id', $exIds)
            ->whereDate('data', $data)
            ->get()
            ->groupBy('exercise_id');

        return $exs->map(function (Exercise $e) use ($sets) {
            $total = max(1, (int) ($e->series ?? 1));
            $existing = $sets->get($e->id, collect())->keyBy('serie_num');

            $series = [];
            for ($n = 1; $n <= $total; $n++) {
                $s = $existing->get($n);
                $series[] = [
                    'n'        => $n,
                    'feita'    => (bool) ($s?->feita ?? false),
                    'carga'    => $s?->carga,
                    'reps'     => $s?->reps,
                    'segundos' => $s?->segundos,
                ];
            }

            $feitas = collect($series)->where('feita', true)->count();

            return [
                'id'            => $e->id,
                'nome'          => $e->nome,
                'icone'         => $e->icone ?: 'dumbbell',
                'tipo'          => $e->tipo ?? 'forca',
                'observacao'    => $e->observacao,
                'por_lado'      => (bool) $e->por_lado,
                'rep_min'       => $e->rep_min,
                'rep_max'       => $e->rep_max,
                'segundos_min'  => $e->segundos_min,
                'segundos_max'  => $e->segundos_max,
                'series_total'  => $total,
                'series_feitas' => $feitas,
                'concluido'     => $feitas === $total,
                'series'        => $series,
                'rotulo'        => $this->rotuloExercicio($e),
            ];
        });
    }

    private function rotuloExercicio(Exercise $e): string
    {
        $total = (int) $e->series;
        if ($e->segundos_min) {
            $faixa = $e->segundos_max && $e->segundos_max !== $e->segundos_min
                ? "{$e->segundos_min}–{$e->segundos_max}s"
                : "{$e->segundos_min}s";
            return "{$total}× {$faixa}";
        }
        if ($e->rep_min) {
            $faixa = $e->rep_max && $e->rep_max !== $e->rep_min
                ? "{$e->rep_min}–{$e->rep_max}"
                : (string) $e->rep_min;
            return "{$total}× {$faixa} reps";
        }
        return "{$total} séries";
    }

    /**
     * Split semanal com data real. Marca passado/hoje/futuro/selecionado e calcula
     * percent feito de cada dia útil baseado em séries efetivamente feitas naquela data.
     */
    private function splitSemanal(int $diaSelecionado, int $diaHoje, Carbon $hoje): array
    {
        $segunda = $hoje->copy()->startOfWeek(Carbon::MONDAY);

        $byDia = Workout::where('ativo', true)
            ->orderBy('dia_semana')
            ->with('exercises')
            ->get()
            ->keyBy('dia_semana');

        $exIds = $byDia->flatMap(fn ($w) => $w->exercises->pluck('id')->all())->all();
        $iniSemana = $segunda->toDateString();
        $fimSemana = $segunda->copy()->addDays(6)->toDateString();
        $setsSemana = empty($exIds) ? collect() : ExerciseSet::whereIn('exercise_id', $exIds)
            ->whereBetween('data', [$iniSemana, $fimSemana])
            ->where('feita', true)
            ->get()
            ->groupBy(fn ($s) => $s->data instanceof Carbon ? $s->data->toDateString() : (string) $s->data);

        return collect([1, 2, 3, 4, 5, 6, 7])->map(function (int $d) use ($byDia, $setsSemana, $segunda, $diaSelecionado, $diaHoje) {
            $w = $byDia->get($d);
            $isWeekend = $d >= 6;
            $passou = $d < $diaHoje;
            $hojeFlag = $d === $diaHoje;
            $selecionado = $d === $diaSelecionado;
            $data = $segunda->copy()->addDays($d - 1)->toDateString();

            $feito = false; $parcial = 0;
            if ($w) {
                $totalSeries = (int) $w->exercises->sum('series');
                $feitasNoDia = (int) ($setsSemana->get($data)?->count() ?? 0);
                $parcial = $totalSeries > 0 ? (int) round($feitasNoDia / $totalSeries * 100) : 0;
                $feito = $totalSeries > 0 && $feitasNoDia >= $totalSeries;
            }

            return [
                'dia'         => self::DIAS_CURTO[$d],
                'dia_iso'     => $d,
                'titulo'      => $w?->nome ?? ($isWeekend ? 'Descanso' : '—'),
                'icone'       => $w?->icone ?? ($isWeekend ? 'leaf' : 'clock'),
                'feito'       => $feito,
                'percent'     => $parcial,
                'ativo'       => $hojeFlag,
                'selecionado' => $selecionado,
                'passou'      => $passou,
                'descanso'    => !$w,
            ];
        })->all();
    }

    private function amanha(int $diaHoje): array
    {
        $proximoDia = $diaHoje >= 7 ? 1 : $diaHoje + 1;
        $diaTreino = $proximoDia;
        for ($i = 0; $i < 7; $i++) {
            $w = Workout::where('ativo', true)->where('dia_semana', $diaTreino)->first();
            if ($w) {
                return [
                    'dia'              => $diaTreino,
                    'label'            => self::DIAS_LABEL[$diaTreino],
                    'curto'            => self::DIAS_CURTO[$diaTreino],
                    'titulo'           => $w->nome,
                    'intensidade'      => $w->intensidade ?? '',
                    'icone'            => $w->icone ?? 'dumbbell',
                    'total_exercicios' => $w->exercises()->count(),
                    'eh_amanha'        => $diaTreino === $proximoDia,
                ];
            }
            $diaTreino = $diaTreino >= 7 ? 1 : $diaTreino + 1;
        }
        return ['titulo' => '—', 'label' => '—', 'curto' => '—', 'icone' => 'leaf', 'total_exercicios' => 0, 'eh_amanha' => false, 'intensidade' => ''];
    }

    private function statusDia(array $t, array $amanha): array
    {
        if ($t['eh_passado']) {
            if (!$t['workout']) {
                return [
                    'titulo' => 'DIA DE DESCANSO', 'subtitulo' => $t['titulo'],
                    'detalhe' => $t['data_br'], 'percent' => 0, 'concluido' => false, 'passado' => true,
                ];
            }
            if ($t['concluido']) {
                return [
                    'titulo' => 'DIA CONCLUÍDO', 'subtitulo' => $t['titulo'],
                    'detalhe' => "{$t['series_feitas']}/{$t['series_total']} séries · {$t['data_br']}",
                    'percent' => $t['percent'], 'concluido' => true, 'passado' => true,
                ];
            }
            if ($t['series_feitas'] > 0) {
                return [
                    'titulo' => 'TREINO PARCIAL', 'subtitulo' => $t['titulo'],
                    'detalhe' => "{$t['series_feitas']}/{$t['series_total']} séries · {$t['data_br']}",
                    'percent' => $t['percent'], 'concluido' => false, 'passado' => true,
                ];
            }
            return [
                'titulo' => 'NÃO REGISTRADO', 'subtitulo' => $t['titulo'],
                'detalhe' => $t['data_br'], 'percent' => 0, 'concluido' => false, 'passado' => true,
            ];
        }

        if ($t['eh_futuro']) {
            return [
                'titulo'    => $t['workout'] ? 'PROGRAMADO' : 'DIA DE DESCANSO',
                'subtitulo' => $t['titulo'],
                'detalhe'   => "{$t['dia_label']} · {$t['data_br']}",
                'percent'   => 0,
                'concluido' => false,
                'passado'   => false,
            ];
        }

        // HOJE
        if ($t['is_weekend']) {
            return [
                'titulo' => 'DIA DE DESCANSO', 'subtitulo' => 'Recuperação é parte do treino.',
                'detalhe' => "Próximo treino: {$amanha['curto']} · {$amanha['titulo']}",
                'percent' => 100, 'concluido' => true, 'passado' => false,
            ];
        }
        if ($t['concluido']) {
            return [
                'titulo' => 'TREINO CONCLUÍDO', 'subtitulo' => 'Prepare-se para o próximo.',
                'detalhe' => "{$amanha['curto']}: {$amanha['titulo']} · {$amanha['total_exercicios']} exercícios",
                'percent' => 100, 'concluido' => true, 'passado' => false,
            ];
        }
        if (!$t['workout']) {
            return [
                'titulo' => 'SEM TREINO HOJE', 'subtitulo' => 'Aproveite pra mobilidade.',
                'detalhe' => "Próximo: {$amanha['curto']} · {$amanha['titulo']}",
                'percent' => 0, 'concluido' => false, 'passado' => false,
            ];
        }
        return [
            'titulo' => 'TREINO EM ANDAMENTO', 'subtitulo' => $t['titulo'],
            'detalhe' => "{$t['series_feitas']}/{$t['series_total']} séries · {$t['percent']}%",
            'percent' => $t['percent'], 'concluido' => false, 'passado' => false,
        ];
    }

    private function consistencia(): array
    {
        $inicio = Carbon::today()->subDays(27);
        $sets = ExerciseSet::whereDate('data', '>=', $inicio->toDateString())
            ->where('feita', true)
            ->get();

        $diasComTreino = $sets->pluck('data')
            ->map(fn ($d) => $d instanceof Carbon ? $d->toDateString() : (string) $d)
            ->unique()
            ->count();

        $diasUteis = 0;
        for ($i = 0; $i < 28; $i++) {
            $d = Carbon::today()->subDays($i)->dayOfWeekIso;
            if ($d <= 5) $diasUteis++;
        }
        $percent = $diasUteis > 0 ? min(100, (int) round($diasComTreino / $diasUteis * 100)) : 0;

        $serie = [];
        for ($w = 3; $w >= 0; $w--) {
            $ini = Carbon::today()->subDays(($w + 1) * 7 - 1);
            $fim = Carbon::today()->subDays($w * 7);
            $count = $sets
                ->filter(function ($s) use ($ini, $fim) {
                    $d = $s->data instanceof Carbon ? $s->data : Carbon::parse($s->data);
                    return $d->between($ini, $fim);
                })
                ->pluck('data')
                ->map(fn ($d) => $d instanceof Carbon ? $d->toDateString() : (string) $d)
                ->unique()
                ->count();
            $serie[] = ['x' => $fim->format('d/m'), 'y' => $count];
        }

        return [
            'percent'    => $percent,
            'concluidos' => $diasComTreino,
            'serie'      => $serie,
        ];
    }
}
