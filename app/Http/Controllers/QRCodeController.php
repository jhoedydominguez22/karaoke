<?php

namespace App\Http\Controllers;

use App\Models\QRCodeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use MongoDB\Client;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeController extends Controller
{
    public function guardarQR(Request $request)
    {
        try {
            // Validar los datos
            $request->validate([
                'numeroMesa' => 'required|integer|max:255',
                'qrUrl' => 'required|string'
            ]);

            // Obtener datos del request
            $data = [
                'numeroMesa' => $request->numeroMesa,
                'qrUrl' => $request->qrUrl,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];

            // Guardar en MongoDB
            DB::connection('mongodb')->collection('qrcodes')->insert($data);

            return response()->json(['message' => 'Código QR guardado con éxito'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al guardar el QR: ' . $e->getMessage()], 500);
        }
    }

    
    public function listarQRCodes()
{
    try {
        // Obtener todos los QR almacenados en MongoDB
        $qrCodes = DB::connection('mongodb')->collection('qrcodes')->get();

        return response()->json($qrCodes);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al obtener los códigos QR: ' . $e->getMessage()], 500);
    }
}

    // Eliminar un QR por ID
    public function eliminarQR($id)
    {
        DB::connection('mongodb')->collection('qrcodes')->where('_id', $id)->delete();
        return response()->json(['message' => 'Código QR eliminado correctamente']);
    }
}
