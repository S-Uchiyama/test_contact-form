@extends('layouts.common')

@section('title', 'Contact')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/contact.css') }}">
@endsection

@section('content')
  <h1 class="page-title">Contact</h1>

  <form class="form" method="POST" action="{{ route('contact.confirm') }}">
    @csrf

    <div class="form-row">
      <div class="form-label">お名前<span class="req">※</span></div>
      <div>
        <div class="name-grid">
          <div>
            <input type="text" name="last_name" value="{{ old('last_name', request('last_name')) }}" placeholder="例: 山田">
            @error('last_name') <div class="error">{{ $message }}</div> @enderror
          </div>
          <div>
            <input type="text" name="first_name" value="{{ old('first_name', request('first_name')) }}" placeholder="例: 太郎">
            @error('first_name') <div class="error">{{ $message }}</div> @enderror
          </div>
        </div>
      </div>
    </div>

    <div class="form-row">
      <div class="form-label">性別<span class="req">※</span></div>
      <div>
        <div class="gender">
          <label>
            <input type="radio" name="gender" value="1" {{ old('gender', request('gender')) == '1' ? 'checked' : '' }}>
            男性
          </label>
          <label>
            <input type="radio" name="gender" value="2" {{ old('gender', request('gender')) == '2' ? 'checked' : '' }}>
            女性
          </label>
          <label>
            <input type="radio" name="gender" value="3" {{ old('gender', request('gender')) == '3' ? 'checked' : '' }}>
            その他
          </label>
        </div>
        @error('gender') <div class="error">{{ $message }}</div> @enderror
      </div>
    </div>

    <div class="form-row">
      <div class="form-label">メールアドレス<span class="req">※</span></div>
      <div>
        <input type="email" name="email" value="{{ old('email', request('email')) }}" placeholder="例: test@example.com">
        @error('email') <div class="error">{{ $message }}</div> @enderror
      </div>
    </div>

    <div class="form-row">
      <div class="form-label">電話番号<span class="req">※</span></div>
      <div>
        <div class="tel-wrap">
          <input class="tel" type="text" name="tel1" value="{{ old('tel1', request('tel1')) }}" maxlength="5" placeholder="080">
          <span class="hyphen">-</span>
          <input class="tel" type="text" name="tel2" value="{{ old('tel2', request('tel2')) }}" maxlength="5" placeholder="1234">
          <span class="hyphen">-</span>
          <input class="tel" type="text" name="tel3" value="{{ old('tel3', request('tel3')) }}" maxlength="5" placeholder="5678">
        </div>

        @if($errors->hasAny(['tel1','tel2','tel3']))
          <div class="error">
            {{ $errors->first('tel1') ?: $errors->first('tel2') ?: $errors->first('tel3') }}
          </div>
        @endif
      </div>
    </div>

    <div class="form-row">
      <div class="form-label">住所<span class="req">※</span></div>
      <div>
        <input type="text" name="address" value="{{ old('address', request('address')) }}" placeholder="例: 東京都渋谷区千駄ヶ谷1-2-3">
        @error('address') <div class="error">{{ $message }}</div> @enderror
      </div>
    </div>

    <div class="form-row">
      <div class="form-label">建物名</div>
      <div>
        <input type="text" name="building" value="{{ old('building', request('building')) }}" placeholder="例: 千駄ヶ谷マンション101">
        @error('building') <div class="error">{{ $message }}</div> @enderror
      </div>
    </div>

    <div class="form-row">
      <div class="form-label">お問い合わせの種類<span class="req">※</span></div>
      <div>
        <select name="category_id">
          <option value="">選択してください</option>
          @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ old('category_id', request('category_id')) == $category->id ? 'selected' : '' }}>
              {{ $category->content }}
            </option>
          @endforeach
        </select>
        @error('category_id') <div class="error">{{ $message }}</div> @enderror
      </div>
    </div>

    <div class="form-row" style="align-items:start;">
      <div class="form-label">お問い合わせ内容<span class="req">※</span></div>
      <div>
        <textarea name="detail" placeholder="お問い合わせ内容をご記載ください">{{ old('detail', request('detail')) }}</textarea>
        @error('detail') <div class="error">{{ $message }}</div> @enderror
      </div>
    </div>

    <div class="actions">
      <button class="btn" type="submit">確認画面</button>
    </div>
  </form>
@endsection
