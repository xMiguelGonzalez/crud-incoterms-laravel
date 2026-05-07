<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Incoterm;
use Illuminate\Support\Facades\Validator;

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



        // CREAR UN NUEVO INCOTERM

        public function store(Request $request) {

            
        
            $validator = Validator::make($request->all(), [

            'INCOTERM_TYPE_ID' => 'required|exists:INCOTERM_TYPES,ID',
            'TRACKING_STEP_ID' => 'required|exists:TRACKING_STEPS,ID',
            
            ]);

            if ($validator->fails()) {

                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación de los datos',
                    'errors' => $validator->errors()
                ], 422);

            }

            try {
                $incoterm = Incoterm::create($request->all());

                return response()->json([
                    'success' => true,
                    'message' => 'Incoterm creado exitosamente',
                    'data' => $incoterm
                ], 201);

            } catch (\Exception $e) {

                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear el incoterm',
                    'error' => $e->getMessage()
                ], 500);
                
            }

         }

         public function show($id) {

            try {

                $incoterm = Incoterm::with(['incotermType', 'trackingStep'])->find($id);

                if (!$incoterm) {

                    return response()->json([
                        'success' => false,
                        'message' => 'Incoterm no encontrado'
                    ], 404);

                }
                return response()->json([
                    'success' => true,
                    'data' => $incoterm
                ], 200);

            } catch (\Exception $e) {

                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener el incoterm',
                    'error' => $e->getMessage()
                ], 500);
                
            }

         }

         public function update(Request $request, $id) {

            $validator = Validator::make($request->all(), [
                'INCOTERM_TYPE_ID' => 'sometimes|exists:INCOTERM_TYPES,ID',
                'TRACKING_STEP_ID' => 'sometimes|exists:TRACKING_STEPS,ID',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación de los datos',
                    'errors' => $validator->errors()
                ], 422);
            }

            try {
                $incoterm = Incoterm::find($id);

                if (!$incoterm) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Incoterm no encontrado'
                    ], 404);
                }

                $incoterm->update($request->all());

                return response()->json([
                    'success' => true,
                    'message' => 'Incoterm actualizado exitosamente',
                    'data' => $incoterm
                ], 200);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el incoterm',
                    'error' => $e->getMessage()
                ], 500);
            }
         }

         function destroy($id) {

            try {
                $incoterm = Incoterm::find($id);

                if (!$incoterm) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Incoterm no encontrado'
                    ], 404);
                }

                $incoterm->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Incoterm eliminado exitosamente'
                ], 200);


            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar el incoterm',
                    'error' => $e->getMessage()
                ], 500);
            }
            
         }


    }














