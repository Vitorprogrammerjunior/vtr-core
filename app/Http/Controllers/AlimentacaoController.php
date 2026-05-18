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
}
