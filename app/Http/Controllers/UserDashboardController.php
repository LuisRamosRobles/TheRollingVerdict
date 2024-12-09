<?php

namespace App\Http\Controllers;

use App\Models\Resena;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    /**
     * @OA\Get(
     *     path="/user/dashboard",
     *     summary="Mostrar el panel de control del usuario",
     *     description="Devuelve el panel de control del usuario autenticado con una lista de las reseñas realizadas.",
     *     operationId="getUserDashboard",
     *     tags={"Usuario"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Panel de usuario cargado con éxito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="resenas", type="array", description="Lista de reseñas del usuario",
     *                 @OA\Items(ref="#/components/schemas/Resena")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado (Debe iniciar sesión)"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acción no autorizada (Solo usuarios con rol USER pueden acceder)"
     *     )
     * )
     */
    public function index()
    {
        $user = Auth::user();
        $resenas = Resena::where('user_id', $user->id)->get();

        return view('user.dashboard', compact('resenas'));
    }
}
