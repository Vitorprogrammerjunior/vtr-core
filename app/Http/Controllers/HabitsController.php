<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\HabitLog;
use Illuminate\Http\Request;

class HabitsController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => ['required', 'string', 'max:120'],
        ]);

        $ordem = (int) (Habit::max('ordem') ?? 0) + 1;

        Habit::create([
            'titulo' => $data['titulo'],
            'frequencia' => 'daily',
            'ativo' => true,
            'ordem' => $ordem,
        ]);

        return back()->with('status', 'Hábito adicionado.');
    }

    public function destroy(Habit $habit)
    {
        $habit->delete();

        return back()->with('status', 'Hábito removido.');
    }

    public function toggle(Habit $habit)
    {
        $hoje = now()->toDateString();

        $log = HabitLog::whereDate('data', $hoje)
            ->where('habit_id', $habit->id)
            ->first();

        if ($log) {
            $log->feito = !$log->feito;
            $log->save();
        } else {
            HabitLog::create([
                'habit_id' => $habit->id,
                'data'     => $hoje,
                'feito'    => true,
            ]);
        }

        return back();
    }
}
