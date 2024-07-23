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
        $search = $request->input('search');
        $company_id = $request->input('company_id');

        $query = Product::query();

        if ($search) {
            $query->where('product_name', 'like', "%{$search}%");
        }

        if ($company_id) {
            $query->where('company_id', $company_id);
        }

        $products = $query->with('company')->get();
        $companies = Company::all();

        return view('products.index', compact('products', 'companies'));
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
            return redirect()->route('products.index')->with('success', '商品が正常に削除されました。');
        } catch (Exception $e) {
            return redirect()->back()->with('error', '商品削除中にエラーが発生しました。' . $e->getMessage());
        }
    }
}
