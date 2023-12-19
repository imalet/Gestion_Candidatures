<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormationController extends Controller
{

    /**
     * @OA\Get(
     *     path="/formations",
     *     tags={"Formation"},
     *     operationId="index()",
     *     summary="Liste toutes les formations",
     *     description="Point de terminaison pour récupérer la liste de toutes les formations.",
     *     @OA\Response(
     *         response=200,
     *         description="Liste de toutes les formations",
     *         @OA\JsonContent(
     *             example={"formations": {{"id": 1, "nom": "Formation 1"}, {"id": 2, "nom": "Formation 2"}}}
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function index()
    {
        $formations = Formation::all();

        return response()->json(['formations' => $formations], 200);
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
     *     path="/formations",
     *     tags={"Formation"},
     *     operationId="store()",
     *     summary="Enregistre une nouvelle formation",
     *     description="Point de terminaison pour enregistrer une nouvelle formation.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="titre", type="string", maxLength=255),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="date_fin_candidature", type="string", format="date", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Enregistrement effectué avec succès",
     *         @OA\JsonContent(
     *             example={"message": "Enregistrement effectué avec succès"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation échouée",
     *         @OA\JsonContent(
     *             example={"errors": {"titre": {"Le champ titre est requis."}, "description": {"Le champ description est requis."}}}
     *         )
     *     ),
     *     @OA\Response(response=500, description="Enregistrement échoué")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'date_fin_candidature' => 'date|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $newData = new Formation();
        $newData->titre = $request->titre;
        $newData->description = $request->description;
        $newData->date_fin_candidature = $request->date_fin_candidature;

        if ($newData->save()) {
            return response()->json([
                'message' => 'Enregistrement effectué avec succès'
            ], 200);
        }

        return response()->json([
            'message' => 'Enregistrement échoué'
        ], 500);
    }

    /**
     * @OA\Get(
     *     path="/formations/{id}",
     *     tags={"Formation"},
     *     operationId="show",
     *     summary="Affiche les détails d'une formation",
     *     description="Point de terminaison pour afficher les détails d'une formation.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la formation",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails de la formation",
     *         @OA\JsonContent(
     *             example={"formations": {"id": 1, "titre": "Formation 1", "description": "Description de la formation", "date_fin_candidature": "2023-01-01"}}
     *         )
     *     ),
     *     @OA\Response(response=404, description="Formation non trouvée"),
     *     @OA\Response(response=500, description="Erreur lors de la récupération des détails")
     * )
     */
    public function show(string $id)
    {
        $formations = Formation::findOrFail($id);
        return response()->json(['formations' => $formations], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
    }

    /**
     * @OA\Put(
     *     path="/formations/{id}",
     *     tags={"Formation"},
     *     operationId="update",
     *     summary="Mise à jour d'une formation existante",
     *     description="Point de terminaison pour mettre à jour une formation existante.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la formation",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="titre", type="string", maxLength=255),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="date_fin_candidature", type="string", format="date", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mise à jour effectuée avec succès",
     *         @OA\JsonContent(
     *             example={"message": "Mise à jour effectuée avec succès"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation échouée",
     *         @OA\JsonContent(
     *             example={"errors": {"titre": {"Le champ titre est requis."}, "description": {"Le champ description est requis."}}}
     *         )
     *     ),
     *     @OA\Response(response=404, description="Formation non trouvée"),
     *     @OA\Response(response=500, description="Échec de la mise à jour")
     * )
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'date_fin_candidature' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $formation = Formation::findOrFail($id);

        $formation->titre = $request->titre;
        $formation->description = $request->description;
        $formation->date_fin_candidature = $request->date_fin_candidature;

        if ($formation->save()) {
            return response()->json([
                'message' => 'Mise à jour effectuée avec succès'
            ], 200);
        }

        return response()->json([
            'message' => 'Échec de la mise à jour'
        ], 500);
    }

    /**
     * @OA\Delete(
     *     path="/formations/{id}",
     *     tags={"Formation"},
     *     operationId="destroy",
     *     summary="Suppression d'une formation",
     *     description="Point de terminaison pour supprimer une formation existante.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la formation",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Formation supprimée avec succès",
     *         @OA\JsonContent(
     *             example={"Message": "Supprimé"}
     *         )
     *     ),
     *     @OA\Response(response=404, description="Formation non trouvée", @OA\JsonContent(example={"Message": "Connais pas"})),
     *     @OA\Response(response=500, description="Échec de la suppression")
     * )
     */
    public function destroy(string $id)
    {
        $formation = Formation::find($id);
        if (!$formation) {
            return response()->json(['Message' => 'Connais pas']);
        }

        $formation->delete();
        return response()->json(['Message' => 'Supprimé']);
    }
}
