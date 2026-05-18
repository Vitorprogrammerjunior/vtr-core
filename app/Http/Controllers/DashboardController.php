<?php

namespace App\Http\Controllers;

use App\Models\{Profile, Goal, Streak, Page};
use App\Services\NutritionDashboard;
use App\Services\TrainingDashboard;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $profile = Profile::firstOrCreate(['user_id' => $user->id]);
        $main    = Goal::where('tipo', 'main')->first();
        $foco    = Goal::where('tipo', 'foco')->first();
        $streak  = Streak::firstOrCreate(['user_id' => $user->id], ['dias' => 0]);

        // Resumo de alimentação para o card da home (sem kcal/macros).
        $nut  = NutritionDashboard::forToday($profile)->build();
        $meal = (object) [
            'concluido'   => $nut['diaConcluido'],
            'percent'     => $nut['consistencia']['percent'],
            'feitas'      => $nut['consistencia']['feitas'],
            'total'       => $nut['consistencia']['total'],
            'hidratacao'  => $nut['hidratacao'],
            'proxima'     => $nut['proximaRefeicao'],
        ];

        // Adapter: $workout expõe titulo + progresso + próximo treino (vindo do TrainingDashboard).
        $tr  = (new TrainingDashboard())->build();
        $workout = (object) [
            'titulo'         => $tr['treinoHoje']['titulo'],
            'grupo_muscular' => $tr['treinoHoje']['intensidade'],
            'feitas'         => $tr['treinoHoje']['series_feitas'],
            'total'          => $tr['treinoHoje']['series_total'],
            'percent'        => $tr['treinoHoje']['percent'],
            'concluido'      => $tr['statusHoje']['concluido'],
            'is_weekend'     => $tr['treinoHoje']['is_weekend'],
            'tem_treino'     => (bool) $tr['treinoHoje']['workout'],
            'proximo'        => $tr['amanha'],
        ];

        // Adapter: $notes vira lista de páginas recentes com {titulo, data}.
        $notes = Page::orderByDesc('updated_at')->limit(3)->get()
            ->map(fn ($p) => (object) [
                'titulo' => $p->titulo,
                'data'   => $p->updated_at,
            ]);

        return view('dashboard', compact('user','profile','main','foco','streak','meal','workout','notes'));
    }
}
