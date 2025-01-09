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
    $user->sendEmailVerificationNotification();
    
    //新規会員登録後メール認証ページへリダイレクト
    return redirect()->route('verification.notice');
    }

    public function showVerifyEmail()
{
    return view('auth.verify');
}

public function verifyEmail(EmailVerificationRequest $request)
{
    $request->fulfill();

    //初回のみプロフィール設定画面へリダイレクト
    if (!$request->user()->has_profile) {
        return redirect()->route('mypage.profile.edit');
    }

    //既にプロフィールが設定されている場合は商品一覧画面へ
    return redirect()->route('item');
}
    public function resendVerificationEmail(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', '認証メールを再送信しました');
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
            return redirect()->route('item');
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
