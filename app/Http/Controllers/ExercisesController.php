<?php

namespace App\Http\Controllers;

use App\Models\{Exercise, Workout};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExercisesController extends Controller
{
    public function store(Request $request, Workout $workout)
    {
        $data = $this->validateExercise($request);
        $data['user_id']    = Auth::id();
        $data['workout_id'] = $workout->id;
        if (empty($data['ordem'])) {
            $data['ordem'] = (int) ($workout->exercises()->max('ordem')) + 1;
        }

        Exercise::create($data);

        return back()->with('ok', 'Exercício adicionado.');
    }

    public function update(Request $request, Exercise $exercise)
    {
        $data = $this->validateExercise($request);
        $exercise->update($data);

        return back()->with('ok', 'Exercício atualizado.');
    }

    public function destroy(Exercise $exercise)
    {
        $exercise->delete();
        return back()->with('ok', 'Exercício removido.');
    }

    private function validateExercise(Request $request): array
    {
        return $request->validate([
            'nome'         => ['required', 'string', 'max:160'],
            'icone'        => ['nullable', 'string', 'max:30'],
            'series'       => ['required', 'integer', 'min:1', 'max:20'],
            'tipo'         => ['nullable', 'string', 'max:20'],
            'rep_min'      => ['nullable', 'integer', 'min:0', 'max:9999'],
            'rep_max'      => ['nullable', 'integer', 'min:0', 'max:9999'],
            'segundos_min' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'segundos_max' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'por_lado'     => ['nullable', 'boolean'],
            'observacao'   => ['nullable', 'string', 'max:255'],
            'ordem'        => ['nullable', 'integer', 'min:0', 'max:99'],
        ]);
    }
}
