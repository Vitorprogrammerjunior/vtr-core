<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkoutsController extends Controller
{
    public function manage()
    {
        $user = Auth::user();
        $workouts = Workout::with('exercises')->orderBy('dia_semana')->orderBy('ordem')->get();
        $diasLabel = [1 => 'SEGUNDA', 2 => 'TERÇA', 3 => 'QUARTA', 4 => 'QUINTA', 5 => 'SEXTA', 6 => 'SÁBADO', 7 => 'DOMINGO'];

        return view('treinos-gerenciar', compact('user', 'workouts', 'diasLabel'));
    }

    public function edit(Workout $workout)
    {
        $user = Auth::user();
        $workout->load('exercises');
        $diasLabel = [1 => 'SEGUNDA', 2 => 'TERÇA', 3 => 'QUARTA', 4 => 'QUINTA', 5 => 'SEXTA', 6 => 'SÁBADO', 7 => 'DOMINGO'];

        return view('treinos-editar', compact('user', 'workout', 'diasLabel'));
    }

    public function store(Request $request)
    {
        $data = $this->validateWorkout($request);
        $data['user_id'] = Auth::id();
        $data['ativo'] = true;

        $workout = Workout::create($data);

        return redirect()->route('workouts.edit', $workout)->with('ok', 'Treino criado.');
    }

    public function update(Request $request, Workout $workout)
    {
        $data = $this->validateWorkout($request);
        $workout->update($data);

        return back()->with('ok', 'Treino atualizado.');
    }

    public function destroy(Workout $workout)
    {
        $workout->delete();
        return redirect()->route('workouts.manage')->with('ok', 'Treino removido.');
    }

    private function validateWorkout(Request $request): array
    {
        return $request->validate([
            'nome'           => ['required', 'string', 'max:120'],
            'grupo_muscular' => ['nullable', 'string', 'max:120'],
            'dia_semana'     => ['nullable', 'integer', 'min:1', 'max:7'],
            'intensidade'    => ['nullable', 'string', 'max:20'],
            'icone'          => ['nullable', 'string', 'max:30'],
            'ativo'          => ['nullable', 'boolean'],
        ]);
    }
}
