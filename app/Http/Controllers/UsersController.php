<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UsersController extends Controller
{


  
   

    public function store(Request $request)
    {
        // Valida los datos del formulario
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido_materno' => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'roles' => 'array', // Asegura que los roles sean un array
        ]);

        // Crea un nuevo usuario con los datos proporcionados
        $user = User::create([
            'nombre' => $request->nombre,
            'apellido_materno' => $request->apellido_materno,
            'apellido_paterno' => $request->apellido_paterno,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hashea la contraseña
            'roles' => $request->roles, // Asigna los roles como un array

        ]);


        return response()->json(['message' => '¡Usuario creado exitosamente!']);
    }

    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|string|email',
        'password' => 'required|string',
    ]);

    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        // Obtener el usuario autenticado
        $user = Auth::user();

        // Verificamos si el usuario tiene alguno de los roles definidos
        if (in_array('user', $user->roles)) {
            return redirect('/formulario');  // Redirige directamente a la URL /formulario
        } elseif (in_array('administrador', $user->roles) || in_array('superadmin', $user->roles) || in_array('dj', $user->roles)) {
            return redirect('/dashboard');  // Redirige al dashboard
        }
    }

    return back()->withErrors([
        'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
    ])->onlyInput('email');
}


    
    public function dashboard()
    {
        $user = Auth::user();
        $userName = $user->nombre . ' ' . $user->apellido_paterno . ' ' . $user->apellido_materno;
        return view('administrativas.dashboard', ['userName' => $userName]);
    }


    public function listUsers()
    {
        $rolesAdministrativos = ['capturista', 'buscador', 'consultor', 'administrador', 'estadisticas'];
        $users = User::whereIn('roles', $rolesAdministrativos)->get();
        return response()->json($users);
    }


    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Usuario eliminado exitosamente']);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'nombre' => 'required|string|max:255',
            'apellido_materno' => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            'roles' => 'array'
        ];

        // Aplicar la validación del correo electrónico solo si se proporciona un nuevo correo electrónico
        if ($request->email !== $user->email) {
            $rules['email'] = 'required|string|email|max:255|unique:users,email';
        } else {
            $rules['email'] = 'required|string|email|max:255';
        }

        // Validar la solicitud
        $validatedData = $request->validate($rules);

        // Actualizar los campos del usuario
        $user->update($validatedData);

        // Si la contraseña está presente y no está vacía, actualizarla
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return response()->json(['message' => 'Usuario actualizado exitosamente']);
    }

        public function currentUser()
        {
            return response()->json(Auth::user());
        }

}
