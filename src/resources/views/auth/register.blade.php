@extends('layouts.common')

@section('title', 'Register')
@section('body_class', 'bg-auth')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('header_right')
  <a class="header-link" href="{{ route('login') }}">login</a>
@endsection

@section('content')

  <h1 class="page-title">Register</h1>

  <div class="auth-card">
    <form method="POST" action="{{ route('register') }}">
      @csrf

      <div class="auth-field">
        <label class="auth-label" for="name">お名前</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="例: 山田太郎">
        @error('name') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div class="auth-field">
        <label class="auth-label" for="email">メールアドレス</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="例: test@example.com">
        @error('email') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div class="auth-field">
        <label class="auth-label" for="password">パスワード</label>
        <input id="password" type="password" name="password" placeholder="パスワード">
        @error('password') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div class="auth-actions">
        <button class="btn auth-btn" type="submit">登録</button>
      </div>
    </form>
  </div>

@endsection
