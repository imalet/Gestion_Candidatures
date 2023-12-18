<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Candidature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CandidatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
     * Accepte or denie.
     */

    

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
