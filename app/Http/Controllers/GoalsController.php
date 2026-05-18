<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GoalsController extends Controller
{
    private const ICONES = ['target', 'dumbbell', 'book', 'bank', 'run', 'biceps', 'brain', 'dollar', 'checklist', 'heart'];

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo'    => ['required', 'string', 'max:120'],
            'icone'     => ['nullable', Rule::in(self::ICONES)],
            'progresso' => ['nullable', 'integer', 'min:0', 'max:100'],
            'prazo'     => ['nullable', 'date'],
        ]);

        $ordem = (int) (Goal::where('tipo', 'ativa')->max('ordem') ?? 0) + 1;

        Goal::create([
            'tipo'      => 'ativa',
            'categoria' => $data['icone'] ?? 'target',
            'titulo'    => $data['titulo'],
            'progresso' => $data['progresso'] ?? 0,
            'prazo'     => $data['prazo'] ?? null,
            'ativo'     => true,
            'ordem'     => $ordem,
        ]);

        return back()->with('status', 'Meta adicionada.');
    }

    public function update(Request $request, Goal $goal)
    {
        $data = $request->validate([
            'titulo'    => ['required', 'string', 'max:120'],
            'icone'     => ['nullable', Rule::in(self::ICONES)],
            'progresso' => ['nullable', 'integer', 'min:0', 'max:100'],
            'prazo'     => ['nullable', 'date'],
            'frase'     => ['nullable', 'string', 'max:240'],
        ]);

        $update = [
            'titulo'    => $data['titulo'],
            'progresso' => $data['progresso'] ?? $goal->progresso,
            'prazo'     => $data['prazo'] ?? $goal->prazo,
            'frase'     => $data['frase'] ?? $goal->frase,
        ];
        if (array_key_exists('icone', $data) && $data['icone'] !== null) {
            $update['categoria'] = $data['icone'];
        }
        $goal->update($update);

        return back()->with('status', 'Meta atualizada.');
    }

    public function destroy(Goal $goal)
    {
        $goal->delete();

        return back()->with('status', 'Meta removida.');
    }
}
