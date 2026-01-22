@extends('layouts.common')

@section('title', 'Confirm')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/confirm.css') }}">
@endsection

@section('content')
  <h1 class="page-title">Confirm</h1>

  <div class="confirm-box">
    <table class="confirm-table">
      <tbody>
        <tr>
          <th>お名前</th>
          <td>{{ $data['last_name'] }} {{ $data['first_name'] }}</td>
        </tr>

        <tr>
          <th>性別</th>
          <td>{{ $genderText }}</td>
        </tr>

        <tr>
          <th>メールアドレス</th>
          <td>{{ $data['email'] }}</td>
        </tr>

        <tr>
          <th>電話番号</th>
          <td>{{ $data['tel'] }}</td>
        </tr>

        <tr>
          <th>住所</th>
          <td>{{ $data['address'] }}</td>
        </tr>

        <tr>
          <th>建物名</th>
          <td>{{ $data['building'] }}</td>
        </tr>

        <tr>
          <th>お問い合わせの種類</th>
          <td>{{ $category?->content }}</td>
        </tr>

        <tr>
          <th>お問い合わせ内容</th>
          <td class="confirm-detail">{{ $data['detail'] }}</td>
        </tr>
      </tbody>
    </table>

    <div class="confirm-actions">
      {{-- 送信ボタン --}}
      <form method="POST" action="{{ route('contact.store') }}">
        @csrf
        @foreach($data as $key => $value)
          <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach

        {{-- tel1〜tel3 を送る（storeで必要な場合） --}}
        <input type="hidden" name="tel1" value="{{ request('tel1') }}">
        <input type="hidden" name="tel2" value="{{ request('tel2') }}">
        <input type="hidden" name="tel3" value="{{ request('tel3') }}">

        <button class="btn" type="submit">送信</button>
      </form>

      {{-- 修正ボタン --}}
      <form method="GET" action="{{ route('contact.create') }}">
        @foreach($data as $key => $value)
          <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach

        <input type="hidden" name="tel1" value="{{ request('tel1') }}">
        <input type="hidden" name="tel2" value="{{ request('tel2') }}">
        <input type="hidden" name="tel3" value="{{ request('tel3') }}">

        <button class="btn" type="submit">修正</button>
      </form>
    </div>
  </div>
@endsection
