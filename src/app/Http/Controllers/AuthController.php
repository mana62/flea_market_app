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
        //データベースに保存
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        //自動ログインとメール認証通知
        Auth::login($user);
        $user->sendEmailVerificationNotification();

        //メール認証ページへリダイレクト
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

        //初回はプロフィール編集ページへ、それ以外は商品一覧へ
        return $request->user()->has_profile
            ? redirect()->route('item')
            : redirect()->route('mypage.profile.edit');
    }

    //認証メールを再送信
    public function resendVerificationEmail(Request $request)
    {
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
        //ユーザー名またはメールアドレスでログイン
        $loginField = filter_var($request->input('name-or-mail'), FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        $credentials = [$loginField => $request->input('name-or-mail'), 'password' => $request->password];

        //認証成功時、商品一覧へリダイレクト
        if (Auth::attempt($credentials)) {
            return redirect()->route('item');
        }

        //認証失敗時
        return back()->withErrors(['name-or-mail' => 'ログイン情報が登録されていません'])->withInput();
    }

    //ログアウト処理
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login.view');
    }
}
