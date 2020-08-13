<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use AuthenticatesUsers;
   /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard/home';

    public function username()
    {
        return 'username';
    }

    public function showLoginForm()
    {
        if (Auth::check() === true) {
            return redirect(url('/' . config('constants.dashboard.path') . '/home'));
        }

        return view('index');
    }

    public function login(Request $request)
    {
        if (in_array('', $request->only('username', 'password'))) {
            return $this->message->error('Ooops, informe todos os dados para efetuar o login')->setStatus(422)->getResponse();
        }

        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
        ];


        if (!Auth::attempt($credentials)) {
            return $this->message->error('Ooops, usuário e senha não conferem')->setStatus(422)->getResponse();
        }
        return $this->message
            ->success('Seja bem-vindo ' . Auth::user()->name)
            ->setData(['redirect' => url('/' . config('constants.dashboard.path') . '/home')])
            ->getResponse();
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('showLogin');
    }
}
