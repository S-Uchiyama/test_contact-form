@extends('layouts.common')

@section('title', 'Admin | FashionablyLate')
@section('body_class', 'admin-body')

@section('header_right')
  <form method="post" action="{{ route('logout') }}">
    @csrf
    <button class="header-link" type="submit">logout</button>
  </form>
@endsection

@section('css')
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

@section('content')
  <h1 class="page-title">Admin</h1>

  <section class="admin-panel">
    <form class="filters" method="get" action="{{ route('admin.search') }}">
      <input class="input" type="text" name="q"
             value="{{ request('q') }}"
             placeholder="名前やメールアドレスを入力してください">

      <select class="select" name="gender">
        <option value="">性別</option>
        <option value="all" {{ request('gender') === 'all' ? 'selected' : '' }}>全て</option>
        <option value="1"   {{ request('gender') === '1'   ? 'selected' : '' }}>男性</option>
        <option value="2"   {{ request('gender') === '2'   ? 'selected' : '' }}>女性</option>
        <option value="3"   {{ request('gender') === '3'   ? 'selected' : '' }}>その他</option>
      </select>

      <select class="select" name="category_id">
        <option value="">お問い合わせの種類</option>
        @foreach($categories as $c)
          <option value="{{ $c->id }}" {{ (string)request('category_id') === (string)$c->id ? 'selected' : '' }}>
            {{ $c->content }}
          </option>
        @endforeach
      </select>

      <input class="input input-date" type="date" name="date" value="{{ request('date') }}">

      <button class="btn-admin is-primary" type="submit">検索</button>
      <a class="btn-admin" href="{{ route('reset') }}">リセット</a>
    </form>

    <div class="actions-row">
      <a class="btn-admin" href="{{ route('admin.export', request()->query()) }}">エクスポート</a>

      <div class="pagination-wrap">
        {{ $contacts->onEachSide(1)->links('vendor.pagination.admin') }}
      </div>
    </div>
  </section>

  <section class="admin-panel">
    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th>お名前</th>
            <th class="col-gender">性別</th>
            <th>メールアドレス</th>
            <th class="col-category">お問い合わせの種類</th>
            <th>お問い合わせ内容</th>
            <th class="th-actions"></th>
          </tr>
        </thead>
        <tbody>
          @forelse($contacts as $contact)
            <tr>
              <td>{{ $contact->last_name }} {{ $contact->first_name }}</td>

              <td class="col-gender">
                @php $g=[1=>'男性',2=>'女性',3=>'その他']; @endphp
                {{ $g[$contact->gender] ?? $contact->gender }}
              </td>

              <td>{{ $contact->email }}</td>

              <td class="col-category">
                {{ $contact->category->content ?? '' }}
              </td>

              <td class="td-content">
                {{ \Illuminate\Support\Str::limit($contact->detail ?? $contact->content ?? '', 40) }}
              </td>

              <td class="td-actions">
                <button type="button" class="btn-detail js-detail" data-id="{{ $contact->id }}">
                  詳細
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="empty">該当データがありません</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>

  <div class="modal-backdrop" id="modalBackdrop" hidden></div>
  <div class="modal" id="detailModal" hidden aria-hidden="true" role="dialog" aria-modal="true">
    <div class="modal-card">
      <div class="modal-header">
        <button class="modal-close" type="button" id="modalClose" aria-label="close">×</button>
      </div>

      <div class="modal-body" id="modalBody"></div>

      <div class="modal-footer">
        <form method="post" action="{{ route('admin.delete') }}" id="modalDeleteForm"
              onsubmit="return confirm('削除しますか？');">
          @csrf
          <input type="hidden" name="id" id="modalDeleteId" value="">
          <button type="submit" class="btn-danger">削除</button>
        </form>
      </div>
    </div>
  </div>

  <script>
  (() => {
    const backdrop = document.getElementById('modalBackdrop');
    const modal = document.getElementById('detailModal');
    const body = document.getElementById('modalBody');
    const deleteId = document.getElementById('modalDeleteId');

    function openModal() {
      backdrop.hidden = false;
      modal.hidden = false;
      modal.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
    }

    function closeModal() {
      backdrop.hidden = true;
      modal.hidden = true;
      modal.setAttribute('aria-hidden', 'true');
      body.innerHTML = '';
      deleteId.value = '';
      document.body.style.overflow = '';
    }

    document.getElementById('modalClose')?.addEventListener('click', closeModal);
    backdrop.addEventListener('click', closeModal);
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeModal();
    });

    document.querySelectorAll('.js-detail').forEach(btn => {
      btn.addEventListener('click', async () => {
        const id = btn.dataset.id;

        const res = await fetch(`{{ url('/admin') }}/${id}`, {
          headers: { 'Accept': 'application/json' }
        });

        if (!res.ok) return;

        const d = await res.json();

        deleteId.value = d.id ?? id;

        body.innerHTML = `
          <div class="kv">
            <div class="k">お名前</div><div class="v">${esc(d.name ?? '')}</div>
            <div class="k">性別</div><div class="v">${esc(d.gender_text ?? '')}</div>
            <div class="k">メールアドレス</div><div class="v">${esc(d.email ?? '')}</div>
            <div class="k">電話番号</div><div class="v">${esc(d.tel ?? '')}</div>
            <div class="k">住所</div><div class="v">${esc(d.address ?? '')}</div>
            <div class="k">建物名</div><div class="v">${esc(d.building ?? '')}</div>
            <div class="k">お問い合わせの種類</div><div class="v">${esc(d.category ?? '')}</div>
            <div class="k">お問い合わせ内容</div><div class="v v-pre">${esc(d.detail ?? '')}</div>
          </div>
        `;
        openModal();
      });
    });

    function esc(s) {
      return String(s).replace(/[&<>"']/g, c => ({
        '&':'&amp;',
        '<':'&lt;',
        '>':'&gt;',
        '"':'&quot;',
        "'":'&#39;'
      }[c]));
    }
  })();
  </script>
@endsection
