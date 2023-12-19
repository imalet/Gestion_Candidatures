<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    /**
     * @OA\Post(
     *     path="/register",
     *     tags={"Authentification"},
     *     summary="Inscription d'un nouvel utilisateur",
     *     description="Point de terminaison pour l'inscription d'un utilisateur.",
     *     operationId="enregistrerUtilisateur",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="nom", type="string", maxLength=255),
     *             @OA\Property(property="email", type="string", format="email", maxLength=255),
     *             @OA\Property(property="motDePasse", type="string", minLength=4),
     *             required={"nom", "email", "motDePasse"}
     *         )
     *     ),
     *     @OA\Response(response=200, description="Inscription réussie"),
     *     @OA\Response(response=400, description="Requête incorrecte"),
     *     @OA\Response(response=500, description="Erreur interne du serveur")
     * )
     */

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|string|min:4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ], 400);
        }

        $newUser = new User();
        $newUser->name = $request->name;
        $newUser->email = $request->email;
        $newUser->password = Hash::make($request->password);

        if ($newUser->save()) {
            return response()->json([
                'Message' => 'Register EndPoint',
                'Role Utilisateur' => $request->user()->role
            ], 200);
        }

        return response()->json([
            'Message' => 'Bad register'
        ], 500);
    }

    /**
     * @OA\Post(
     *     path="/login",
     *     tags={"Authentification"},
     *     operationId="login",
     *     summary="Authentification de l'utilisateur",
     *     description="Point de terminaison pour l'authentification d'un utilisateur.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", minLength=4),
     *             required={"email", "password"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Authentification réussie",
     *     ),
     *     @OA\Response(response=401, description="Utilisateur non trouvé dans la base de données")
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:4',
        ]);

        $credentials = $request->only('email', 'password');

        if ($token = Auth::attempt($credentials)) {
            return $this->respondWithToken($token);
        }

        return response()->json(['Message' => 'User pas dans la base'], 401);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    /**
     * @OA\Post(
     *     path="/logout",
     *     tags={"Authentification"},
     *     operationId="logout",
     *     summary="Déconnexion de l'utilisateur",
     *     description="Point de terminaison pour la déconnexion de l'utilisateur.",
     *     @OA\Response(
     *         response=200,
     *         description="Déconnexion réussie",
     *         @OA\JsonContent(
     *             example={"message": "Déconnexion réussie"}
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }
}
