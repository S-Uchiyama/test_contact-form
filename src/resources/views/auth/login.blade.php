@extends('layouts.common')

@section('title', 'Login')
@section('body_class', 'bg-auth')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('header_right')
  <a class="header-link" href="{{ route('register') }}">register</a>
@endsection

@section('content')

  <h1 class="page-title">Login</h1>

  <div class="auth-card">
    <form method="POST" action="{{ route('login') }}">
      @csrf

      <div class="auth-field">
        <label class="auth-label" for="email">メールアドレス</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="例: test@example.com">
        @error('email') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div class="auth-field">
        <label class="auth-label" for="password">パスワード</label>
        <input id="password" type="password" name="password" placeholder="例: coachtech106">
        @error('password') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div class="auth-actions">
        <button class="btn auth-btn" type="submit">ログイン</button>
      </div>
    </form>
  </div>

@endsection
