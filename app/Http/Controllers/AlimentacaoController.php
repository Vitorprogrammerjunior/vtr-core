<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Services\NutritionDashboard;
use Illuminate\Http\Request;

class AlimentacaoController extends Controller
{
    public function index(Request $request)
    {
        $user    = $request->user();
        $profile = Profile::firstOrCreate(['user_id' => $user->id]);

        $data = NutritionDashboard::forToday($profile)->build();

        return view('alimentacao', array_merge(
            ['user' => $user, 'profile' => $profile],
            $data
        ));
    }

    public function updatePerfil(Request $request)
    {
        $data = $request->validate([
            'peso_kg' => ['nullable', 'numeric', 'between:30,300'],
        ]);

        $profile = Profile::firstOrCreate(['user_id' => $request->user()->id]);
        $profile->update(['peso_kg' => $data['peso_kg'] ?: null]);

        return back()->with('status', 'Peso atualizado.');
    }
}
