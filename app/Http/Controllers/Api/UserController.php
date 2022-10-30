<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Registra o usuário
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getByToken(Request $request)
    {
        try {
            return $request->user();
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ]);
        }
    }

    /**
     * Registra o usuário
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        try {
            // Validação
            $validaUsuario = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
            ]);

            if($validaUsuario->fails()) {
                return response()->json([
                    'message' => 'Dados inválidos.',
                    'request' => $request->name,
                    'errors' => $validaUsuario->errors()
                ], 401);
            }

            // Criação
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'message' => 'Usuário criado com sucesso!',
                'token' => $user->createToken("JWT")->plainTextToken,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ]);
        }
    }

    /**
     * Loga o usuário
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        try {
            // Validação
            $validaUsuario = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if($validaUsuario->fails()) {
                return response()->json([
                    'message' => 'Dados inválidos.',
                    'errors' => $validaUsuario->errors()
                ], 401);
            }

            // Login
            if(!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'message' => 'Email ou senha incorretos.'
                ], 401);
            }

            $user = Auth::user();

            return response()->json([
                'message' => 'Logado com sucesso.',
                'token' => $user->createToken('JWT')->plainTextToken
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ]);
        }
    }
}
