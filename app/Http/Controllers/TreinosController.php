<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Services\TrainingDashboard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TreinosController extends Controller
{
    public function index(Request $request, TrainingDashboard $dashboard)
    {
        $user    = Auth::user();
        $profile = Profile::firstOrCreate(['user_id' => $user->id]);

        $dia = (int) $request->query('dia', 0);
        $data = $dashboard->build($dia ?: null);

        return view('treinos', array_merge(
            ['user' => $user, 'profile' => $profile],
            $data
        ));
    }
}
