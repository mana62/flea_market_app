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
    //会員登録ページを表示
    public function registerPage()
    {
        return view('auth.register');
    }

    //会員登録処理
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'has_profile' => false,
        ]);
        Auth::login($user);
        $user->sendEmailVerificationNotification();

        return redirect()->route('verification.notice');
    }

    //メール認証ページ表示
    public function showVerifyEmail()
    {
        return view('auth.verify');
    }

    //メール認証処理
    public function verifyEmail(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return $request->user()->has_profile
            ? redirect()->route('item')
            : redirect()->route('mypage.profile.edit');
    }

    //認証メールを再送信
    public function resendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('item');
        }

        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', '認証メールを再送信しました');
    }

    //ログインページ表示
    public function loginPage()
    {
        return view('auth.login');
    }

    //ログイン処理
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {
            return redirect()->route('item');
        }

        return back()->withErrors(['email' => 'ログイン情報が登録されていません'])->withInput();
    }

    //ログアウト処理
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login.view');
    }
}
