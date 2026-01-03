<?php

namespace App\Http\Controllers\Api\Term;

use App\Http\Controllers\Controller;
use App\Models\Term;
use Illuminate\Http\Request;

class TermController extends Controller
{
    public function show_terms()
    {
        $term = Term::where('is_active', true)->where('title', 'Term & Conditions')->first();

        if (!$term) {
            return response()->json([
                'message' => 'Terms and Conditions not found.',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Terms and Conditions fetched successfully',
            'data' => $term
        ], 200);
    }
    public function propertyRules()
    {
        $property = Term::where('is_active', true)->where('title', 'Property Rule')->first();

        if (!$property) {
            return response()->json([
                'message' => 'Property Rules not found.',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Property Rules fetched successfully',
            'data' => $property
        ], 200);
    }
}
