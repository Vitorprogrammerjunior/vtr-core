<?php

namespace App\Http\Controllers;

use App\Models\{Exercise, ExerciseSet};
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ExerciseSetsController extends Controller
{
    /** Liga/desliga uma série específica de hoje. Cria se não existir (ligando). */
    public function toggle(Exercise $exercise, int $serie)
    {
        abort_if($serie < 1 || $serie > (int) $exercise->series, 422, 'Série inválida.');

        $hoje = Carbon::today()->toDateString();
        $set  = ExerciseSet::where('exercise_id', $exercise->id)
            ->whereDate('data', $hoje)
            ->where('serie_num', $serie)
            ->first();

        if ($set) {
            $set->feita = !$set->feita;
            $set->save();
        } else {
            ExerciseSet::create([
                'exercise_id' => $exercise->id,
                'data'        => $hoje,
                'serie_num'   => $serie,
                'feita'       => true,
            ]);
        }

        return back();
    }

    /** Atualiza carga/reps/segundos da série (sem mexer no feita). */
    public function update(Request $request, Exercise $exercise, int $serie)
    {
        abort_if($serie < 1 || $serie > (int) $exercise->series, 422);

        $data = $request->validate([
            'carga'    => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'reps'     => ['nullable', 'integer', 'min:0', 'max:9999'],
            'segundos' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        $hoje = Carbon::today()->toDateString();
        $set  = ExerciseSet::where('exercise_id', $exercise->id)
            ->whereDate('data', $hoje)
            ->where('serie_num', $serie)
            ->first();

        if ($set) {
            $set->fill($data)->save();
        } else {
            ExerciseSet::create(array_merge($data, [
                'exercise_id' => $exercise->id,
                'data'        => $hoje,
                'serie_num'   => $serie,
                'feita'       => false,
            ]));
        }

        return back();
    }

    /** Marca todas as séries do exercício como feitas (atalho 1-toque). */
    public function completeAll(Exercise $exercise)
    {
        $hoje = Carbon::today()->toDateString();
        for ($n = 1; $n <= (int) $exercise->series; $n++) {
            $set = ExerciseSet::where('exercise_id', $exercise->id)
                ->whereDate('data', $hoje)
                ->where('serie_num', $n)
                ->first();
            if ($set) {
                if (!$set->feita) { $set->feita = true; $set->save(); }
            } else {
                ExerciseSet::create([
                    'exercise_id' => $exercise->id,
                    'data'        => $hoje,
                    'serie_num'   => $n,
                    'feita'       => true,
                ]);
            }
        }

        return back();
    }
}
