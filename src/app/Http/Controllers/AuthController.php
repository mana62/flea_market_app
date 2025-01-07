<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function registerView()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

    Auth::login($user);
    
    return redirect()->route('mypage.profile.edit');
    }

    public function showVerifyEmail()
{
    return view('auth.verify');
}

    public function verifyEmail(EmailVerificationRequest $request)
    {
        $request->fulfill();
        return redirect()->route('mypage.profile.edit');
    }

    public function resendVerificationEmail(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', '認証メールを再送信しました');
    }

    public function loginView()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = [
            'password' => $request->password,
        ];

        $loginField = filter_var($request->input('name-or-mail'), FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        $credentials[$loginField] = $request->input('name-or-mail');

        if (Auth::attempt($credentials)) {
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'name-or-mail' => 'ログイン情報が登録されていません'
        ])->withInput();
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login.view');
    }
}
