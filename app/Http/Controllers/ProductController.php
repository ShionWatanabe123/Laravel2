<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Company;
use Illuminate\Http\Request;
use Exception;
use App\Http\Requests\ArticleRequest;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::all();

        // 検索条件を取得またはセッションから復元
        $search = $request->input('search', session('search'));
        $company_id = $request->input('company_id', session('company_id'));
        $min_price = $request->input('min_price', session('min_price'));
        $max_price = $request->input('max_price', session('max_price'));
        $min_stock = $request->input('min_stock', session('min_stock'));
        $max_stock = $request->input('max_stock', session('max_stock'));

        $query = Product::query();

        if ($search) {
            $query->where('product_name', 'LIKE', "%{$search}%");
        }

        if ($company_id) {
            $query->where('company_id', $company_id);
        }

        if ($min_price) {
            $query->where('price', '>=', $min_price);
        }

        if ($max_price) {
            $query->where('price', '<=', $max_price);
        }

        if ($min_stock) {
            $query->where('stock', '>=', $min_stock);
        }

        if ($max_stock) {
            $query->where('stock', '<=', $max_stock);
        }

        // ソート機能の追加
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');

        $query->orderBy($sort, $direction);

        $products = $query->paginate(10)->appends($request->query());

        // 検索条件をセッションに保存
        session([
            'search' => $search,
            'company_id' => $company_id,
            'min_price' => $min_price,
            'max_price' => $max_price,
            'min_stock' => $min_stock,
            'max_stock' => $max_stock,
        ]);

        return view('products.index', [
            'products' => $products,
            'companies' => $companies,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function search(Request $request)
    {
        $query = Product::query();

        $search = $request->input('search', session('search'));
        $company_id = $request->input('company_id', session('company_id'));
        $min_price = $request->input('min_price', session('min_price'));
        $max_price = $request->input('max_price', session('max_price'));
        $min_stock = $request->input('min_stock', session('min_stock'));
        $max_stock = $request->input('max_stock', session('max_stock'));

        if ($search) {
            $query->where('product_name', 'LIKE', "%{$search}%");
        }

        if ($company_id) {
            $query->where('company_id', $company_id);
        }

        if ($min_price) {
            $query->where('price', '>=', $min_price);
        }

        if ($max_price) {
            $query->where('price', '<=', $max_price);
        }

        if ($min_stock) {
            $query->where('stock', '>=', $min_stock);
        }

        if ($max_stock) {
            $query->where('stock', '<=', $max_stock);
        }

        $products = $query->with('company')->get();

        // 検索条件をセッションに保存
        session([
            'search' => $search,
            'company_id' => $company_id,
            'min_price' => $min_price,
            'max_price' => $max_price,
            'min_stock' => $min_stock,
            'max_stock' => $max_stock,
        ]);

        return response()->json(['products' => $products]);
    }

    public function create()
    {
        $companies = Company::all();
        return view('products.create', compact('companies'));
    }

    public function store(ArticleRequest $request)
    {
        try {
            $product = new Product([
                'product_name' => $request->get('product_name'),
                'company_id' => $request->get('company_id'),
                'price' => $request->get('price'),
                'stock' => $request->get('stock'),
                'comment' => $request->get('comment'),
            ]);

            if ($request->hasFile('img_path')) {
                $filename = $request->img_path->getClientOriginalName();
                $filePath = $request->img_path->storeAs('products', $filename, 'public');
                $product->img_path = '/storage/' . $filePath;
            }

            $product->save();

            return redirect()->route('products.index')->with('success', '商品が正常に登録されました。');
        } catch (Exception $e) {
            return redirect()->back()->with('error', '商品登録中にエラーが発生しました。' . $e->getMessage());
        }
    }

    public function show(Product $product)
    {
        return view('products.show', ['product' => $product]);
    }

    public function edit(Product $product)
    {
        $companies = Company::all();
        return view('products.edit', compact('product', 'companies'));
    }

    public function update(ArticleRequest $request, Product $product)
    {
        try {
            $product->product_name = $request->product_name;
            $product->price = $request->price;
            $product->stock = $request->stock;
            $product->comment = $request->comment;

            if ($request->hasFile('img_path')) {
                $filename = $request->img_path->getClientOriginalName();
                $filePath = $request->img_path->storeAs('products', $filename, 'public');
                $product->img_path = '/storage/' . $filePath;
            }

            $product->save();

            return redirect()->route('products.index')->with('success', '商品が正常に更新されました。');
        } catch (Exception $e) {
            return redirect()->back()->with('error', '商品更新中にエラーが発生しました。' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        try {
            $product->delete();
            return response()->json(['success' => '商品が正常に削除されました。']);
        } catch (Exception $e) {
            return response()->json(['error' => '商品削除中にエラーが発生しました。' . $e->getMessage()], 500);
        }
    }
}
