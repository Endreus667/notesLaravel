<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function loginSubmit(Request $request)
    {
        // Validação dos dados de entrada
        $request->validate([
            'text_username' => 'required|string|min:3|max:16',
            'text_password' => 'required|string|min:6|max:24',
        ], [
            'text_username.required' => 'O username é obrigatório.',
            'text_username.string' => 'O username deve ser uma string.',
            'text_username.min' => 'O username deve ter no mínimo 3 caracteres.',
            'text_username.max' => 'O username deve ter no máximo 16 caracteres.',
            'text_password.required' => 'A senha é obrigatória.',
            'text_password.string' => 'A senha deve ser uma string.',
            'text_password.min' => 'A senha deve ter no mínimo 6 caracteres.',
            'text_password.max' => 'A senha deve ter no máximo 24 caracteres.',
        ]);

        // Sanitização dos inputs
        $username = htmlspecialchars($request->input('text_username'));
        $password = htmlspecialchars($request->input('text_password'));

        // Verificar se o número de tentativas foi excedido
        if (RateLimiter::tooManyAttempts('login:' . $username, 5)) {
            return back()->with('loginError', 'Você excedeu o número de tentativas. Tente novamente após 1 minuto.');
        }

        // Verificar se o usuário existe
        $user = User::where('username', $username)
            ->whereNull('deleted_at')
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            // Incrementa o contador de tentativas após falha no login
            RateLimiter::hit('login:' . $username);

            return back()
                ->withInput()
                ->with('loginError', 'Username ou senha incorretos.');
        }

        // Atualiza o último login
        $user->last_login = now();
        $user->save();

        // Reseta as tentativas após login bem-sucedido
        RateLimiter::clear('login:' . $username);

        // Login do usuário
        session([
            'user' => [
                'id' => $user->id,
                'username' => $user->username
            ]
        ]);

        // Redirecionar para a página inicial ou página de sucesso
        return redirect()->to('/');
    }

    // Realiza o logout do usuário
    public function logout()
    {
        session()->forget('user');
        return redirect()->to('/login');
    }
}
