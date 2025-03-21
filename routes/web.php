<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\EstadisticasController;
use App\Http\Controllers\QRCodeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('http://localhost:8000/bienvenida'); // Redirige después de cerrar sesión
})->name('users.logout');

   //VISTAS PUBLICAS
   Route::get('/home', function () {
    return view('publicas.home');
});


Route::get('/bienvenida', function () {
    return view('publicas.bienvenida');
});

Route::get('/formulario', function () {
    return view('publicas.formulario');
});


Route::get('/', function () {
    return view('welcome');
});

Route::get('/capturar', function () {
    return view('administrativas.capturar');
});

Route::get('/consultar', function () {
    return view('administrativas.consultar');
});

Route::get('/desechados', function () {
    return view('administrativas.desechados');
});

Route::get('/buscar', function () {
    return view('administrativas.buscar');
});

Route::get('/buscardesechos', function () {
    return view('administrativas.buscardesechos');
});

Route::get('/estadisticas', function () {
    return view('administrativas.estadisticas');
});

Route::get('/inventario', function () {
    return view('administrativas.inventario');
});





Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/dashboard', [UsersController::class, 'dashboard'])->name('dashboard')->middleware('auth');

Route::get('/Createuser', function () {
    return view('administrativas.createuser');
});

Route::get('/list-user', function () {
    return view('administrativas.listuser');
});

Route::get('/obtenerstats', [EstadisticasController::class, 'obtenerEstadisticas']);

Route::get('/obtenerinventario', [EstadisticasController::class, 'mostrarInventario']);

Route::get('/exportar-inventario', [EstadisticasController::class, 'exportarInventario']);


Route::get('/currentuser', [UsersController::class, 'CurrentUser']);

Route::post('/buscar', [DocumentoController::class, 'buscar']);

Route::post('/desechadosbusqueda', [DocumentoController::class, 'buscarDesechados']);

//RUTAS PARA USUARIOS ADMINISTRATIVOS
Route::post('/users', [UsersController::class, 'store'])->name('users.store');
Route::post('/login', [UsersController::class, 'login'])->name('users.login');

Route::get('/list-users', [UsersController::class, 'listUsers'])->name('users.list');
Route::delete('/users/{id}', [UsersController::class, 'destroy'])->name('users.destroy');
Route::put('/users/{id}', [UsersController::class, 'update'])->name('users.update');



Route::get('/documentos/{documentId}', [DocumentoController::class, 'getDocumentById']);
Route::get('/documentos', [DocumentoController::class, 'getAllDocuments']);
Route::get('/documentosDesechados', [DocumentoController::class, 'getAllDocumentsDesechados']);
Route::delete('/documentos/{documentId}', [DocumentoController::class, 'deleteDocument']);
Route::post('/documentos/update/{documentId}', [DocumentoController::class, 'updateDocument']);
Route::get('/ultimos-documentos', [DocumentoController::class, 'obtenerUltimosDocumentos']);

//NUEVO PROYECTO


Route::post('/solicitud', [DocumentoController::class, 'storeSolicitud']);
Route::put('/solicitudes/{id}/atendida', [DocumentoController::class, 'marcarComoAtendida']);
Route::post('/guardar-qr', [QRCodeController::class, 'guardarQR']);
Route::get('/listado-qr', [QRCodeController::class, 'listarQRCodes']);
Route::delete('/eliminar-qr/{id}', [QRCodeController::class, 'eliminarQR']);
