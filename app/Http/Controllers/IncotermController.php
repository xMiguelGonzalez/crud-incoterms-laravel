<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IncotermType;
use App\Models\Incoterm;
use Illuminate\Support\Facades\Validator;
use App\Utils\Utilitat;

class IncotermController
{

    // OBTENER LOS INCOTERMS


    public function index()
    {

        try {

            $tipos = IncotermType::with(['trackingSteps'])->get();

            return response()->json([
                'success' => true,
                'data' => $tipos
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => Utilitat::errorMessage($e)
            ], 500);
        }
    }



    // CREAR UN NUEVO INCOTERM

    public function store(Request $request)
    {



        $validator = Validator::make($request->all(), [

            'CODE' => 'required|string|max:10',
            'NAME' => 'required|string|max:255',
            'STEPS' => 'array',
            'STEPS.*' => 'exists:TRACKING_STEPS,ID'

        ]);

        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => 'Error de validación de los datos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $tipo = IncotermType::create($request->only(['CODE', 'NAME']));


            // Guardamos los pasos en la tabla intermedia INCOTERMS


            if ($request->has('STEPS')) {

                foreach ($request->STEPS as $step_id) {
                    Incoterm::create([
                        'INCOTERM_TYPE_ID' => $tipo->ID,
                        'TRACKING_STEP_ID' => $step_id
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Incoterm creado exitosamente',
                'data' => $tipo
            ], 201);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => Utilitat::errorMessage($e)
            ], 500);
        }
    }

    // Mostrar Incoterm por ID

    public function show($id)
    {

        try {

            $tipo = IncotermType::with(['trackingSteps'])->find($id);

            if (!$tipo) {

                return response()->json([
                    'success' => false,
                    'message' => 'Incoterm no encontrado'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'data' => $tipo
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => Utilitat::errorMessage($e)
            ], 500);
        }
    }

    // Actualizar un Incoterm existente

    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'CODE' => 'sometimes|string|max:10',
            'NAME' => 'sometimes|string|max:255',
            'STEPS' => 'array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación de los datos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $tipo = IncotermType::find($id);

            if (!$tipo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Incoterm no encontrado'
                ], 404);
            }

            $tipo->update($request->only(['CODE', 'NAME']));


            if ($request->has('STEPS')) {

                // limpiamos los pasos viejos
                Incoterm::where('INCOTERM_TYPE_ID', $tipo->ID)->delete();

                // Luego guardamos los nuevos seleccionados

                foreach ($request->STEPS as $step_id) {
                    Incoterm::create([
                        'INCOTERM_TYPE_ID' => $tipo->ID,
                        'TRACKING_STEP_ID' => $step_id
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Incoterm actualizado exitosamente',
                'data' => $tipo
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => Utilitat::errorMessage($e)
            ], 500);
        }
    }

    // Eliminar un Incoterm

    public function destroy($id)
    {

        try {
            $tipo = IncotermType::find($id);

            if (!$tipo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Incoterm no encontrado'
                ], 404);
            }

            Incoterm::where('INCOTERM_TYPE_ID', $tipo->ID)->delete();

            $tipo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Incoterm eliminado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => Utilitat::errorMessage($e)
            ], 500);
        }
    }
}
