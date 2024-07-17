@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <!-- 成功メッセージの表示 -->
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <!-- エラーメッセージの表示 -->
                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form id="updateForm" method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="id" class="form-label">ID</label>
                                <input type="text" class="form-control" id="id" name="id" value="{{ $product->id }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="product_name" class="form-label">商品名</label>
                                <input type="text" class="form-control" id="product_name" name="product_name" value="{{ $product->product_name }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="company_id" class="form-label">メーカー名</label>
                                <select class="form-select" id="company_id" name="company_id">
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ $product->company_id == $company->id ? 'selected' : '' }}>{{ $company->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="price" class="form-label">金額</label>
                                <input type="number" class="form-control" id="price" name="price" value="{{ $product->price }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="stock" class="form-label">在庫数</label>
                                <input type="number" class="form-control" id="stock" name="stock" value="{{ $product->stock }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="comment" class="form-label">コメント</label>
                                <textarea id="comment" name="comment" class="form-control" rows="3">{{ $product->comment }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="img_path" class="form-label">商品画像:</label>
                                <input id="img_path" type="file" name="img_path" class="form-control">
                                <img src="{{ asset($product->img_path) }}" alt="商品画像" class="product-image">
                            </div>

                            <div style="display: flex; gap: 20px;">
                                <button type="submit" class="btn btn-primary" style="background-color: orange; border-color: orange; width: 150px; height: 45px;">更新</button>
                                <a href="{{ route('products.index') }}" class="btn btn-primary" style="width: 150px; height: 45px;">戻る</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for error handling -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const updateForm = document.getElementById('updateForm');
            
            updateForm.addEventListener('submit', function(event) {
                event.preventDefault();
                try {
                    updateForm.submit();
                } catch (error) {
                    alert('更新に失敗しました。再度お試しください。');
                }
            });
        });
    </script>
@endsection
