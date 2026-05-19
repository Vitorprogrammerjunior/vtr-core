<?php

namespace App\Services;

use App\Models\{Profile, WaterLog, Meal, MealLog};
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

/**
 * Agrega os dados de alimentação do dia: refeições, hidratação,
 * cardápio semanal e estado de "dia concluído". Read-only.
 */
class NutritionDashboard
{
    public function __construct(private Profile $profile, private CarbonInterface $hoje) {}

    public static function forToday(Profile $profile): self
    {
        return new self($profile, Carbon::today());
    }

    public function build(): array
    {
        $data    = $this->hoje->toDateString();
        $hojeIso = ((int) $this->hoje->dayOfWeek + 6) % 7;

        $water      = WaterLog::whereDate('data', $data)->first();
        $hidratacao = $this->hidratacao($water);

        // Carrega TODAS as refeições ativas do plano (uma vez) e usa nas duas visões.
        $allMeals = Meal::where('ativo', true)->orderBy('ordem')->orderBy('horario')->get();

        $cardapioSemanal = $this->cardapioSemanal($allMeals, $hojeIso);

        [$refeicoes, $consistencia, $proximaRefeicao] = $this->refeicoesEConsistencia(
            $allMeals, $data, $hojeIso, $hidratacao['percent'] >= 80
        );

        $diaConcluido = $consistencia['feitas'] > 0
            && $consistencia['feitas'] === $consistencia['total'];

        $amanha = $this->amanha($cardapioSemanal, $hojeIso);

        $statusHoje = $this->statusHoje($consistencia, $hidratacao, $diaConcluido, $amanha);

        $proteina = $this->proteina($data);

        return compact(
            'hidratacao',
            'proteina',
            'refeicoes', 'proximaRefeicao', 'consistencia',
            'cardapioSemanal', 'diaConcluido', 'amanha', 'statusHoje'
        );
    }

    /**
     * Proteína consumida hoje vs meta (peso_kg × 2, ou meta_proteina_g do perfil).
     */
    private function proteina(string $data): array
    {
        $consumida = (int) MealLog::whereDate('data', $data)
            ->where('feita', true)
            ->join('meals', 'meal_logs.meal_id', '=', 'meals.id')
            ->selectRaw('SUM(COALESCE(meal_logs.proteina_g, meals.proteina_g, 0)) as total')
            ->value('total');

        $meta = $this->profile->peso_kg
            ? (int) round((float) $this->profile->peso_kg * 2)
            : (int) ($this->profile->meta_proteina_g ?: 150);

        $percent = $meta > 0 ? (int) round(min(100, ($consumida / $meta) * 100)) : 0;

        $baseCalculo = $this->profile->peso_kg
            ? number_format((float) $this->profile->peso_kg, 1, ',', '.') . ' kg × 2g'
            : 'meta manual';

        return [
            'consumida'     => $consumida,
            'meta'          => $meta,
            'percent'       => $percent,
            'base_calculo'  => $baseCalculo,
        ];
    }

    private function hidratacao(?WaterLog $w): array
    {
        $consumido = (float) ($w?->litros ?? 0);
        $meta      = (float) ($this->profile->meta_agua_litros ?: 3.0);
        $percent   = $meta > 0 ? (int) round(min(100, ($consumido / $meta) * 100)) : 0;
        $copos     = (int) ($w?->copos ?? 0);
        $totalCopos = 5;

        return [
            'consumido' => $consumido,
            'meta'      => $meta,
            'percent'   => $percent,
            'copos'     => array_map(fn ($i) => $i < $copos, range(0, $totalCopos - 1)),
        ];
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Meal>  $allMeals
     * @return array{0: array, 1: array, 2: array}  [$refeicoes, $consistencia, $proximaRefeicao]
     */
    private function refeicoesEConsistencia($allMeals, string $data, int $hojeIso, bool $hidratacaoOk): array
    {
        $meals = $allMeals->filter(
            fn (Meal $m) => $m->dia_semana === null || (int) $m->dia_semana === $hojeIso
        )->values();

        $logs = MealLog::whereDate('data', $data)->pluck('feita', 'meal_id');

        $refeicoes = $meals->map(fn (Meal $m) => [
            'id'        => $m->id,
            'nome'      => $m->nome,
            'horario'   => $m->horario ? substr($m->horario, 0, 5) : '',
            'descricao' => $m->descricao ?? '',
            'icone'     => $m->icone ?: 'cutlery',
            'feita'     => (bool) ($logs[$m->id] ?? false),
        ])->all();

        $proxima = collect($refeicoes)->firstWhere('feita', false) ?? ($refeicoes[0] ?? null);
        $proximaRefeicao = $proxima ? [
            'id'        => $proxima['id'],
            'horario'   => $proxima['horario'],
            'titulo'    => mb_strtoupper($proxima['nome']),
            'descricao' => $proxima['descricao'],
            'feita'     => $proxima['feita'],
        ] : ['id' => null, 'horario' => '--:--', 'titulo' => '—', 'descricao' => '', 'feita' => false];

        $itens = collect($refeicoes)
            ->map(fn ($r) => ['titulo' => $r['nome'], 'feito' => $r['feita']])
            ->push(['titulo' => 'Hidratação', 'feito' => $hidratacaoOk])
            ->all();

        $feitas = collect($itens)->where('feito', true)->count();
        $total  = count($itens);
        $percent = $total > 0 ? (int) round(($feitas / $total) * 100) : 0;
        $rotulo = match (true) {
            $percent >= 100 => 'CONCLUÍDO',
            $percent >= 90  => 'EXCELENTE',
            $percent >= 70  => 'MUITO BOM',
            $percent >= 50  => 'REGULAR',
            default         => 'ABAIXO',
        };

        $consistencia = compact('percent', 'rotulo', 'feitas', 'total', 'itens');

        return [$refeicoes, $consistencia, $proximaRefeicao];
    }

    /**
     * Monta cardápio agrupado por dia (0=Seg..6=Dom).
     * Refeições com dia_semana=null entram em todos os dias.
     */
    private function cardapioSemanal($allMeals, int $hojeIso): array
    {
        $labels = ['SEG', 'TER', 'QUA', 'QUI', 'SEX', 'SÁB', 'DOM'];

        $dias = [];
        for ($i = 0; $i < 7; $i++) {
            $itens = $allMeals
                ->filter(fn (Meal $m) => $m->dia_semana === null || (int) $m->dia_semana === $i)
                ->sortBy(fn (Meal $m) => $m->horario ?? '99:99')
                ->map(fn (Meal $m) => [
                    'id'         => $m->id,
                    'nome'       => $m->nome,
                    'horario'    => $m->horario ? substr($m->horario, 0, 5) : '',
                    'descricao'  => $m->descricao ?? '',
                    'icone'      => $m->icone ?: 'cutlery',
                    'fixa'       => $m->dia_semana === null,
                    'proteina_g' => (int) ($m->proteina_g ?? 0),
                ])
                ->values()
                ->all();

            $dias[] = [
                'idx'   => $i,
                'label' => $labels[$i],
                'hoje'  => $i === $hojeIso,
                'itens' => $itens,
            ];
        }
        return $dias;
    }

    /** Resumo do próximo dia, para o painel "Prepare-se para amanhã". */
    private function amanha(array $cardapioSemanal, int $hojeIso): array
    {
        $idx     = ($hojeIso + 1) % 7;
        $diaName = ['SEGUNDA','TERÇA','QUARTA','QUINTA','SEXTA','SÁBADO','DOMINGO'][$idx];
        $itens   = $cardapioSemanal[$idx]['itens'] ?? [];

        return [
            'idx'      => $idx,
            'label'    => $diaName,
            'total'    => count($itens),
            'primeira' => $itens[0] ?? null,
        ];
    }

    private function statusHoje(array $consistencia, array $hidratacao, bool $concluido, array $amanha): array
    {
        if ($concluido) {
            $sub = $amanha['total'] > 0
                ? sprintf('Amanhã (%s): %d refeições planejadas.', $amanha['label'], $amanha['total'])
                : sprintf('Amanhã (%s): nenhuma refeição planejada.', $amanha['label']);
            return [
                'titulo'    => 'DIA CONCLUÍDO',
                'subtitulo' => 'Prepare-se para amanhã.',
                'detalhe'   => $sub,
                'percent'   => 100,
                'concluido' => true,
            ];
        }

        $refTotal  = max(0, $consistencia['total'] - 1);
        $refFeitas = collect($consistencia['itens'])
            ->where('titulo', '!=', 'Hidratação')
            ->where('feito', true)
            ->count();

        $sub = sprintf(
            '%d/%d refeições · %s / %s L',
            $refFeitas, $refTotal,
            number_format($hidratacao['consumido'], 1, ',', '.'),
            number_format($hidratacao['meta'], 1, ',', '.'),
        );

        return [
            'titulo'    => 'DIA EM PROGRESSO',
            'subtitulo' => $consistencia['rotulo'],
            'detalhe'   => $sub,
            'percent'   => $consistencia['percent'],
            'concluido' => false,
        ];
    }
}
