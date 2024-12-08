<?php

namespace App\Http\Controllers;

use App\Models\Resena;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $resenas = Resena::where('user_id', $user->id)->get();

        return view('user.dashboard', compact('resenas'));
    }
}
