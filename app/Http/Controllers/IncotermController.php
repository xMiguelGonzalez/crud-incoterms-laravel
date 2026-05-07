<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Incoterm;

class IncotermController extends Controller
{

    // OBTENER LOS INCOTERMS

    
    public function index()
    {

      try {
        $incoterms = Incoterm::with(['incotermType', 'trackingStep'])->get();
        return response()->json([
            'success' => true,
            'data' => $incoterms
        ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los incoterms',
                'error' => $e->getMessage()
            ], 500);
        }
    }




}
