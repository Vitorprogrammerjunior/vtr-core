<?php

namespace App\Http\Controllers;

use App\Models\{Meal, MealLog, WaterLog};
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MealsController extends Controller
{
    private const ICONES = ['cutlery', 'leaf', 'drop', 'fire'];

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome'       => ['required', 'string', 'max:80'],
            'horario'    => ['nullable', 'date_format:H:i'],
            'descricao'  => ['nullable', 'string', 'max:160'],
            'icone'      => ['nullable', Rule::in(self::ICONES)],
            'dia_semana' => ['nullable', 'integer', 'between:0,6'],
        ]);

        $ordem = (int) (Meal::max('ordem') ?? 0) + 1;

        Meal::create([
            'nome'       => $data['nome'],
            'horario'    => $data['horario'] ?? null,
            'descricao'  => $data['descricao'] ?? null,
            'icone'      => $data['icone'] ?? 'cutlery',
            'dia_semana' => $data['dia_semana'] ?? null,
            'ativo'      => true,
            'ordem'      => $ordem,
        ]);

        return back()->with('status', 'Refeição adicionada.');
    }

    public function update(Request $request, Meal $meal)
    {
        $data = $request->validate([
            'nome'       => ['required', 'string', 'max:80'],
            'horario'    => ['nullable', 'date_format:H:i'],
            'descricao'  => ['nullable', 'string', 'max:160'],
            'icone'      => ['nullable', Rule::in(self::ICONES)],
            'dia_semana' => ['nullable', 'integer', 'between:0,6'],
        ]);

        $meal->update([
            'nome'       => $data['nome'],
            'horario'    => $data['horario'] ?? $meal->horario,
            'descricao'  => $data['descricao'] ?? $meal->descricao,
            'icone'      => $data['icone'] ?? $meal->icone,
            'dia_semana' => array_key_exists('dia_semana', $data) ? $data['dia_semana'] : $meal->dia_semana,
        ]);

        return back()->with('status', 'Refeição atualizada.');
    }

    public function destroy(Meal $meal)
    {
        $meal->delete();
        return back()->with('status', 'Refeição removida.');
    }

    /** Marca/desmarca a refeição como feita HOJE. */
    public function toggle(Meal $meal)
    {
        $hoje = now()->toDateString();
        $log = MealLog::where('meal_id', $meal->id)->whereDate('data', $hoje)->first();

        if ($log) {
            $log->update(['feita' => ! $log->feita]);
        } else {
            MealLog::create([
                'meal_id' => $meal->id,
                'data'    => $hoje,
                'feita'   => true,
            ]);
        }

        return back();
    }

    /** Registra água do dia (substitui o log do dia). */
    public function water(Request $request)
    {
        $data = $request->validate([
            'litros' => ['required', 'numeric', 'min:0', 'max:10'],
        ]);

        $litros = round((float) $data['litros'], 2);
        $copos  = (int) round($litros / 0.3); // ~300 ml por copo
        $hoje   = now()->toDateString();

        $log = WaterLog::whereDate('data', $hoje)->first();
        if ($log) {
            $log->update(['litros' => $litros, 'copos' => min(255, $copos)]);
        } else {
            WaterLog::create([
                'data'   => $hoje,
                'litros' => $litros,
                'copos'  => min(255, $copos),
            ]);
        }

        return back()->with('status', 'Hidratação atualizada.');
    }
}
