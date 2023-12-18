<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormationController extends Controller
{
    
    /**
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
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
     * Display the specified resource.
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
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
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
