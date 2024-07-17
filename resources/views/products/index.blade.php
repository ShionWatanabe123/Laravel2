@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">商品一覧画面</h1>

    <!-- 検索フォームのセクション -->
    <div class="search mt-5">
        <!-- 検索フォーム。GETメソッドで、商品一覧のルートにデータを送信 -->
        <form action="{{ route('products.index') }}" method="GET" class="row g-3">
            <!-- 商品名検索用の入力欄 -->
            <div class="col-sm-12 col-md-3">
                <input type="text" name="search" class="form-control" placeholder="商品キーワード" value="{{ request('search') }}">
            </div>

            <!-- メーカー名検索用のセレクトボックス -->
            <div class="col-sm-12 col-md-3">
                <select name="company_id" class="form-select">
                    <option value="">メーカーを選択</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->company_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 絞り込みボタン -->
            <div class="col-sm-12 col-md-1">
                <button class="btn btn-outline-secondary" type="submit">検索</button>
            </div>
        </form>
    </div>

    <div class="products mt-5">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>商品画像</th>
                    <th>商品名</th>
                    <th>価格</th>
                    <th>在庫数</th>
                    <th>メーカー名</th>
                    <th><a href="{{ route('products.create') }}" class="btn btn-primary mb-3">商品新規登録</a></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td><img src="{{ asset($product->img_path) }}" alt="商品画像" width="100"></td>
                        <td>{{ $product->product_name }}</td>
                        <td>{{ $product->price }}</td>
                        <td>{{ $product->stock }}</td>
                        <td>{{ $product->company->company_name }}</td>
                        <td>
                            <a href="{{ route('products.show', $product) }}" class="btn btn-info btn-sm mx-1">詳細表示</a>
                            <form method="POST" action="{{ route('products.destroy', $product) }}" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm mx-1">削除</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- JavaScript for error handling -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteForms = document.querySelectorAll('.delete-form');

    deleteForms.forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const confirmation = confirm('本当に削除しますか？');
            if (confirmation) {
                try {
                    form.submit();
                } catch (error) {
                    alert('削除に失敗しました。再度お試しください。');
                }
            }
        });
    });
});
</script>
@endsection
