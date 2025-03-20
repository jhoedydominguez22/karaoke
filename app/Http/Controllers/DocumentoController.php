<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use MongoDB\Client;


class DocumentoController extends Controller
{
    public function getDocumentById($documentId)
    {
        // Obtiene un documento específico por su ID
        $document = DB::connection('mongodb')->collection('nombre_de_la_coleccion')->find($documentId);

        if ($document) {
            return response()->json($document);
        } else {
            return response()->json(['error' => "Documento no encontrado"], 404);
        }
    }

    public function getAllDocuments()
    {
        // Obtiene todos los documentos de la colección
        $documents = DB::connection('mongodb')->collection('solicitudes_canciones')->get();

        return response()->json($documents);
    }

    public function getAllDocumentsDesechados()
    {
        // Obtiene todos los documentos de la colección
        $documents = DB::connection('mongodb')->collection('desechados_collection')->get();

        return response()->json($documents);
    }

    public function storeSolicitud(Request $request)
{
    // Validar los campos recibidos
    $request->validate([
        'nombreUsuario' => 'required|string|max:255', // Nombre del usuario (si es invitado o registrado)
        'cancion' => 'required|string|max:255', // Nombre de la canción
        'artista' => 'required|string|max:255', // Nombre del artista
        'albumCover' => 'nullable|string', // Carátula del álbum (opcional)
        'dedicatoria' => 'nullable|string|max:500', // Dedicatoria (opcional)
        'numeroMesa' => 'nullable|string|max:255', // Validación para el número de mesa

    ]);

    try {
        // Obtener los datos del formulario
        $solicitudData = $request->all();

        // Añadir created_at y updated_at como marcas de tiempo
        $now = Carbon::now()->toDateTimeString();
        $solicitudData['created_at'] = $now;
        $solicitudData['updated_at'] = $now;
        $solicitudData['atendida'] = false;  // Campo atendida


        // Insertar la solicitud en la colección 'solicitudes_canciones'
        DB::connection('mongodb')->collection('solicitudes_canciones')->insert($solicitudData);

        return response()->json(['message' => 'Solicitud enviada con éxito'], 201);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al enviar la solicitud: ' . $e->getMessage()], 500);
    }
}

public function marcarComoAtendida($id)
{
    try {
        // Encontrar la solicitud por su ID
        $solicitud = DB::connection('mongodb')->collection('solicitudes_canciones')->find($id);

        if ($solicitud) {
            // Cambiar el estado de 'atendida' a true
            DB::connection('mongodb')->collection('solicitudes_canciones')->where('_id', $id)->update(['atendida' => true]);

            return response()->json(['message' => 'Solicitud marcada como atendida'], 200);
        }

        return response()->json(['error' => 'Solicitud no encontrada'], 404);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al marcar la solicitud: ' . $e->getMessage()], 500);
    }
}


    public function deleteDocument($documentId)
    {
        // Obtener el documento de la colección MongoDB por su ID
        $document = DB::connection('mongodb')->collection('recursos_collection')->where('_id', $documentId)->first();

        if ($document) {
            // Crear una nueva colección destino (por ejemplo, 'otra_coleccion')
            $targetCollection = 'desechados_collection';

            // Insertar el documento en la nueva colección
            DB::connection('mongodb')->collection($targetCollection)->insert($document);

            // Eliminar el documento de la colección actual
            $result = DB::connection('mongodb')->collection('recursos_collection')->where('_id', $documentId)->delete();

            if ($result) {
                return response()->json(['message' => 'Documento movido a otra colección con éxito']);
            } else {
                return response()->json(['error' => 'Error al eliminar el documento de la colección actual'], 500);
            }
        } else {
            return response()->json(['error' => 'Documento no encontrado'], 404);
        }
    }






    public function updateDocument(Request $request, $documentId)
    {
        try {
            // Validar la presencia de datos en el request
            if (!$request->hasAny(['document_data', 'files'])) {
                return response()->json(['error' => 'No se han proporcionado datos o archivos'], 400);
            }

            // Obtener datos del documento y archivos
            $documentData = json_decode($request->input('document_data'), true);
            $files = $request->file('files', []);

            // Validar que los datos del documento sean un array
            if (!is_array($documentData)) {
                return response()->json(['error' => 'Los datos del documento deben ser un array'], 400);
            }

            // Buscar el documento en la base de datos
            $document = DB::connection('mongodb')->collection('recursos_collection')->where('_id', $documentId)->first();

            if (!$document) {
                return response()->json(['error' => 'Documento no encontrado'], 404);
            }

            // Actualizar campos del documento, excluyendo '_id'
            foreach ($documentData as $key => $value) {
                if ($key !== '_id') {
                    $document[$key] = $value;
                }
            }

            // Manejar archivos adjuntos si se proporcionaron
            if (!empty($files)) {
                $uploadedFiles = [];
                foreach ($files as $file) {
                    if ($file->isValid()) {
                        // Guardar archivo en almacenamiento local
                        $path = $file->store('uploads', 'public');
                        $uploadedFiles[] = $path;
                    }
                }
                // Añadir rutas de archivos subidos al documento
                $document['archivos'] = $uploadedFiles;
            }

            // Actualizar fecha de actualización
            $document['updated_at'] = Carbon::now()->toDateTimeString();

            // Guardar el documento actualizado
            DB::connection('mongodb')->collection('recursos_collection')->where('_id', $documentId)->update($document);

            Log::info('Documento actualizado con éxito');
            return response()->json(['message' => 'Documento actualizado con éxito']);
        } catch (\Exception $e) {
            Log::error('Error al procesar la solicitud: ' . $e->getMessage());
            return response()->json(['error' => 'Error al procesar la solicitud', 'exception' => $e->getMessage()], 500);
        }
    }


    public function buscar(Request $request)
    {
        // Validar la entrada
        $validator = Validator::make($request->all(), [
            'palabra_clave' => 'required|string',
        ]);

        // Verificar si la validación falla
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Obtener la palabra clave validada
        $palabraClave = $validator->validated()['palabra_clave'];
        $nombreBaseDatos = 'anzimat';

        // Conectar a MongoDB
        $client = new Client();

        // Obtener todas las colecciones en la base de datos
        $colecciones = $client->$nombreBaseDatos->listCollections();

        $resultados = [];

        $coleccionesOmitir = ['carrousel_images', 'migrations', 'personal_access_tokens', 'users', 'desechados_collection'];

        $camposOmitir = [''];

        // Iterar sobre cada colección y realizar la búsqueda
        foreach ($colecciones as $coleccion) {
            $nombreColeccion = $coleccion->getName();

            // Verificar si la colección debe ser omitida
            if (in_array($nombreColeccion, $coleccionesOmitir)) {
                continue; // Saltar a la siguiente iteración
            }

            // Realizar la búsqueda en la colección actual en todos los campos
            $resultadosColeccion = $client->$nombreBaseDatos->$nombreColeccion
                ->find([
                    '$or' => $this->construirExpresionesDeBusqueda($palabraClave, $nombreColeccion, $camposOmitir)
                ])
                ->toArray();

            // Agregar el nombre de la colección como un campo adicional a cada documento
            foreach ($resultadosColeccion as &$documento) {
                $documento['tipo_coleccion'] = $nombreColeccion;
            }

            // Verificar si hay resultados antes de agregar al array
            if ($resultadosColeccion) {
                $resultados = array_merge($resultados, $resultadosColeccion);
            }
        }

        return response()->json($resultados);
    }

    private function construirExpresionesDeBusqueda($palabraClave, $nombreColeccion, $camposOmitir)
    {
        $expresiones = [];

        // Obtener todos los documentos en la colección
        $client = new Client();
        $colecciones = $client->selectDatabase('anzimat')->selectCollection($nombreColeccion);
        $documentos = $colecciones->find();

        foreach ($documentos as $documento) {
            foreach ($documento as $campo => $valor) {
                // Verificar si el campo debe ser omitido
                if (in_array($campo, $camposOmitir)) {
                    continue; // Saltar a la siguiente iteración
                }

                $expresiones[] = [$campo => ['$regex' => $palabraClave]];
            }
        }

        // Verificar si hay expresiones antes de devolver el resultado
        if (empty($expresiones)) {
            // Devolver una expresión predeterminada (puedes ajustar según tus necesidades)
            $expresiones = [['_id' => ['$exists' => true]]];
        }

        return $expresiones;
    }


    public function buscarDesechados(Request $request)
    {
        // Validar la entrada
        $validator = Validator::make($request->all(), [
            'palabra_clave' => 'required|string',
        ]);

        // Verificar si la validación falla
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Obtener la palabra clave validada
        $palabraClave = $validator->validated()['palabra_clave'];
        $nombreBaseDatos = 'anzimat';

        // Conectar a MongoDB
        $client = new Client();

        // Obtener todas las colecciones en la base de datos
        $colecciones = $client->$nombreBaseDatos->listCollections();

        $resultados = [];

        $coleccionesOmitir = ['carrousel_images', 'migrations', 'personal_access_tokens', 'users', 'recursos_collection'];

        $camposOmitir = [''];

        // Iterar sobre cada colección y realizar la búsqueda
        foreach ($colecciones as $coleccion) {
            $nombreColeccion = $coleccion->getName();

            // Verificar si la colección debe ser omitida
            if (in_array($nombreColeccion, $coleccionesOmitir)) {
                continue; // Saltar a la siguiente iteración
            }

            // Realizar la búsqueda en la colección actual en todos los campos
            $resultadosColeccion = $client->$nombreBaseDatos->$nombreColeccion
                ->find([
                    '$or' => $this->construirExpresionesDeBusqueda($palabraClave, $nombreColeccion, $camposOmitir)
                ])
                ->toArray();

            if ($resultadosColeccion) {
                $resultados = array_merge($resultados, $resultadosColeccion);
            }
        }

        return response()->json($resultados);
    }
}
