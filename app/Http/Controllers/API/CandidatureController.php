<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Candidature;
use App\Models\Formation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CandidatureController extends Controller
{

    /**
     * @OA\Get(
     *     path="/candidatures",
     *     tags={"Candidature"},
     *     operationId="index",
     *     summary="Liste toutes les candidatures",
     *     description="Point de terminaison pour récupérer toutes les candidatures.",
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="ID de l'utilisateur",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="formation_id",
     *         in="query",
     *         description="ID de la formation",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="etat_candidature",
     *         in="query",
     *         description="État de la candidature (attente, accepte, refuse)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"attente", "accepte", "refuse"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste de toutes les candidatures",
     *         @OA\JsonContent(
     *             example={"Message": "Candidatures filtrées", "Candidatures": {{"id": 1, "nom": "Nom1", "email": "email1@example.com"}, {"id": 2, "nom": "Nom2", "email": "email2@example.com"}}}
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function index()
    {
        return response()->json([
            'Message' => 'Toutes les Candidatures',
            'Candidatures' => Candidature::all()
        ]);
    }

    /**
     * @OA\Get(
     *     path="/candidatures/acceptees",
     *     tags={"Candidature"},
     *     operationId="candidatureAccepter",
     *     summary="Liste des candidatures acceptées",
     *     description="Point de terminaison pour récupérer les candidatures acceptées.",
     *     @OA\Response(
     *         response=200,
     *         description="Liste des candidatures acceptées",
     *         @OA\JsonContent(
     *             example={"Message": "Candidatures acceptées", "Candidatures": {{"id": 1, "nom": "Nom1", "email": "email1@example.com"}, {"id": 2, "nom": "Nom2", "email": "email2@example.com"}}}
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function candidatureAccepter()
    {
        return response()->json([
            'Message' => 'Toutes les Candidatures',
            'Candidatures' => Candidature::where('etat_candidature', 'accepte')->get()
        ]);
    }

    /**
     * @OA\Get(
     *     path="/candidatures/refusees",
     *     tags={"Candidature"},
     *     operationId="candidatureRefuser",
     *     summary="Liste des candidatures refusées",
     *     description="Point de terminaison pour récupérer les candidatures refusées.",
     *     @OA\Response(
     *         response=200,
     *         description="Liste des candidatures refusées",
     *         @OA\JsonContent(
     *             example={"Message": "Candidatures refusées", "Candidatures": {{"id": 1, "nom": "Nom1", "email": "email1@example.com"}, {"id": 2, "nom": "Nom2", "email": "email2@example.com"}}}
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function candidatureRefuser()
    {
        return response()->json([
            'Message' => 'Toutes les Candidatures',
            'Candidatures' => Candidature::where('etat_candidature', 'refuser')->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }


    /**
     * @OA\Post(
     *     path="/candidatures/{id_formation}",
     *     tags={"Candidature"},
     *     operationId="store",
     *     summary="Enregistre une candidature pour une formation",
     *     description="Point de terminaison pour enregistrer une candidature pour une formation spécifiée.",
     *     @OA\Parameter(
     *         name="id_formation",
     *         in="path",
     *         description="ID de la formation",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Enregistrement de la candidature réussi",
     *         @OA\JsonContent(
     *             example={"message": "Enregistrement de la candidature réussi", "id_formation": "123", "user_id": "456"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Vous avez déjà candidaté à cette formation",
     *         @OA\JsonContent(
     *             example={"message": "Vous avez déjà candidaté à cette formation"}
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=500, description="Échec de l'enregistrement de la candidature")
     * )
     */
    public function store(string $id_formation)
    {

        $existingCandidature = Candidature::where('formation_id', $id_formation)
            ->where('user_id', Auth::user()->id)
            ->first();


        if ($existingCandidature) {
            return response()->json([
                'message' => 'Vous avez déjà candidaté à cette formation'
            ], 400);
        }


        $newCandidature = new Candidature();
        $newCandidature->formation_id = $id_formation;
        $newCandidature->user_id = Auth::user()->id;

        if ($newCandidature->save()) {
            return response()->json([
                'message' => 'Enregistrement de la candidature réussi',
                'id_formation' => $id_formation,
                'user_id' => Auth::user()->id
            ], 200);
        }

        return response()->json([
            'message' => 'Échec de l\'enregistrement de la candidature'
        ], 500);
    }

    /**
     * @OA\Get(
     *     path="/candidatures/{candidature_id}/{etat}",
     *     tags={"Candidature"},
     *     operationId="acceptDenieCandidature",
     *     summary="Modifier l'état d'une candidature",
     *     description="Point de terminaison pour modifier l'état (accepter/refuser) d'une candidature spécifiée.",
     *     @OA\Parameter(
     *         name="candidature_id",
     *         in="path",
     *         description="ID de la candidature",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="etat",
     *         in="path",
     *         description="État de la candidature (accepte, refuse)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"accepte", "refuse"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Modification de l'état de la candidature réussie",
     *         @OA\JsonContent(
     *             example={"Message": "Good Modifie Cadidature"}
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=404, description="Candidature non trouvée")
     * )
     */
    public function acceptDenieCandidature(string $candidature_id, string $etat)
    {
        $candidature = Candidature::findOrFail($candidature_id);
        $candidature->etat_candidature = $etat;
        $candidature->save();

        return response()->json([
            'Message' => 'Good Modifie Cadidature',
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    
}
