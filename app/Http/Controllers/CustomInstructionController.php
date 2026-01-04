<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomInstructionController extends Controller
{
    /**
     * Affiche la page des instructions personnalisées
     */
    public function edit()
    {
        // Récupère les instructions depuis la session
        $instructions = session('custom_instructions', '');

        return Inertia::render('Settings/Instructions', [
            'instructions' => $instructions
        ]);
    }

    /**
     * Sauvegarde les instructions personnalisées
     */
    public function update(Request $request)
    {
        $request->validate([
            'instructions' => 'nullable|string|max:3000',
        ]);

        // Sauvegarde dans la session
        session(['custom_instructions' => $request->instructions]);

        return back()->with('success', 'Instructions personnalisées sauvegardées');
    }
}
