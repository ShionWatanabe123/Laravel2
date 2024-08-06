@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">商品一覧画面</h1>

    <!-- 検索フォームのセクション -->
    <div class="search mt-5">
        <!-- 検索フォーム -->
        <form id="searchForm" class="row g-3" method="GET" action="{{ route('products.index') }}">
            <!-- 商品名検索用の入力欄 -->
            <div class="col-sm-12 col-md-3">
                <input type="text" name="search" class="form-control" placeholder="商品キーワード" value="{{ request('search', session('search')) }}">
            </div>

            <!-- メーカー名検索用のセレクトボックス -->
            <div class="col-sm-12 col-md-3">
                <select name="company_id" class="form-select">
                    <option value="">メーカーを選択</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}" {{ request('company_id', session('company_id')) == $company->id ? 'selected' : '' }}>{{ $company->company_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 最小価格検索用の入力欄 -->
            <div class="col-sm-12 col-md-2">
                <input type="number" name="min_price" class="form-control" placeholder="最小価格" value="{{ request('min_price', session('min_price')) }}">
            </div>

            <!-- 最大価格検索用の入力欄 -->
            <div class="col-sm-12 col-md-2">
                <input type="number" name="max_price" class="form-control" placeholder="最大価格" value="{{ request('max_price', session('max_price')) }}">
            </div>

            <!-- 最小在庫数検索用の入力欄 -->
            <div class="col-sm-12 col-md-2">
                <input type="number" name="min_stock" class="form-control" placeholder="最小在庫数" value="{{ request('min_stock', session('min_stock')) }}">
            </div>

            <!-- 最大在庫数検索用の入力欄 -->
            <div class="col-sm-12 col-md-2">
                <input type="number" name="max_stock" class="form-control" placeholder="最大在庫数" value="{{ request('max_stock', session('max_stock')) }}">
            </div>

            <!-- 絞り込みボタン -->
            <div class="col-sm-12 col-md-1">
                <button class="btn btn-outline-secondary" type="submit">検索</button>
            </div>
        </form>
    </div>

    <div class="products mt-5">
        <table class="table table-striped" id="productsTable">
            <thead>
                <tr>
                    <th>
                        <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'id', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}">
                            ID @if(request('sort') === 'id') {{ request('direction') === 'asc' ? '▲' : '▼' }} @endif
                        </a>
                    </th>
                    <th>商品画像</th>
                    <th>
                        <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'product_name', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}">
                            商品名 @if(request('sort') === 'product_name') {{ request('direction') === 'asc' ? '▲' : '▼' }} @endif
                        </a>
                    </th>
                    <th>
                        <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'price', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}">
                            価格 @if(request('sort') === 'price') {{ request('direction') === 'asc' ? '▲' : '▼' }} @endif
                        </a>
                    </th>
                    <th>
                        <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'stock', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}">
                            在庫数 @if(request('sort') === 'stock') {{ request('direction') === 'asc' ? '▲' : '▼' }} @endif
                        </a>
                    </th>
                    <th>メーカー名</th>
                    <th><a href="{{ route('products.create') }}" class="btn btn-primary mb-3">商品新規登録</a></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr data-id="{{ $product->id }}">
                        <td>{{ $product->id }}</td>
                        <td><img src="{{ asset($product->img_path) }}" alt="商品画像" width="100"></td>
                        <td>{{ $product->product_name }}</td>
                        <td>{{ $product->price }}</td>
                        <td>{{ $product->stock }}</td>
                        <td>{{ $product->company->company_name }}</td>
                        <td>
                            <a href="{{ route('products.show', $product) }}" class="btn btn-info btn-sm mx-1">詳細表示</a>
                            <button class="btn btn-danger btn-sm mx-1 delete-button" data-id="{{ $product->id }}">削除</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- ページネーションのリンク -->
    <div class="mt-4">
        {{ $products->appends(request()->query())->links() }}
    </div>
</div>

<!-- JavaScript for non-async search handling -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('searchForm');
    const productsTableBody = document.querySelector('#productsTable tbody');

    // 検索フォームの非同期処理
    searchForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(searchForm);
        const params = new URLSearchParams(formData).toString();

        fetch(`{{ route('products.search') }}?${params}`)
            .then(response => response.json())
            .then(data => {
                productsTableBody.innerHTML = '';
                data.products.forEach(product => {
                    const row = document.createElement('tr');
                    row.setAttribute('data-id', product.id);

                    row.innerHTML = `
                        <td>${product.id}</td>
                        <td><img src="/storage/${product.img_path}" alt="商品画像" width="100"></td>
                        <td>${product.product_name}</td>
                        <td>${product.price}</td>
                        <td>${product.stock}</td>
                        <td>${product.company.company_name}</td>
                        <td>
                            <a href="/products/${product.id}" class="btn btn-info btn-sm mx-1">詳細表示</a>
                            <button class="btn btn-danger btn-sm mx-1 delete-button" data-id="${product.id}">削除</button>
                        </td>
                    `;

                    productsTableBody.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Error:', error);
                alert('検索に失敗しました。再度お試しください。');
            });
    });

    // 削除ボタンの非同期処理
    productsTableBody.addEventListener('click', function(event) {
        if (event.target.classList.contains('delete-button')) {
            const button = event.target;
            const productId = button.getAttribute('data-id');
            const row = button.closest('tr');

            if (confirm('本当に削除しますか？')) {
                fetch(`/products/${productId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (response.ok) {
                        row.remove();
                    } else {
                        throw new Error('削除処理に失敗しました。');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('削除に失敗しました。再度お試しください。');
                });
            }
        }
    });
});
</script>
@endsection
