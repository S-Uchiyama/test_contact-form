@extends('layouts.common')

@section('title', 'Thanks')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/thanks.css') }}">
@endsection

@section('hide_header')
@endsection

@section('content')
  <div class="thanks">
    <div class="thanks-bg" aria-hidden="true">Thank you</div>

    <div class="thanks-inner">
      <p class="thanks-message">お問い合わせありがとうございました</p>
      <a class="btn thanks-btn" href="{{ route('contact.create') }}">HOME</a>
    </div>
  </div>
@endsection
