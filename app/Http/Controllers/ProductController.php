<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Company;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // 検索用のクエリパラメータ
        $search = $request->input('search');
        $company_id = $request->input('company_id');

        // クエリビルダーを使用して条件を設定
        $query = Product::query();

        if ($search) {
            $query->where('product_name', 'like', "%{$search}%");
        }

        if ($company_id) {
            $query->where('company_id', $company_id);
        }

        // クエリを実行して結果を取得
        $products = $query->with('company')->get();

        // メーカーリストを取得
        $companies = Company::all();

        // ビューにデータを渡す
        return view('products.index', compact('products', 'companies'));
    }

    public function create()
    {
        // 商品作成画面で会社の情報が必要なので、全ての会社の情報を取得します。
        $companies = Company::all();

        // 商品作成画面を表示します。その際に、先ほど取得した全ての会社情報を画面に渡します。
        return view('products.create', compact('companies'));
    }

    // 送られたデータをデータベースに保存するメソッドです
    public function store(Request $request)
    {
        // リクエストされた情報を確認して、必要な情報が全て揃っているかチェックします。
        $request->validate([
            'product_name' => 'required',
            'company_id' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'comment' => 'nullable',
            'img_path' => 'nullable|image|max:2048',
        ]);

        // 新しく商品を作ります。そのための情報はリクエストから取得します。
        $product = new Product([
            'product_name' => $request->get('product_name'),
            'company_id' => $request->get('company_id'),
            'price' => $request->get('price'),
            'stock' => $request->get('stock'),
            'comment' => $request->get('comment'),
        ]);

        // リクエストに画像が含まれている場合、その画像を保存します。
        if ($request->hasFile('img_path')) {
            $filename = $request->img_path->getClientOriginalName();
            $filePath = $request->img_path->storeAs('products', $filename, 'public');
            $product->img_path = '/storage/' . $filePath;
        }

        // 作成したデータベースに新しいレコードとして保存します。
        $product->save();

        // 全ての処理が終わったら、商品一覧画面に戻ります。
        return redirect('products');
    }

    public function show(Product $product)
    {
        // 商品詳細画面を表示します。その際に、商品の詳細情報を画面に渡します。
        return view('products.show', ['product' => $product]);
    }

    public function edit(Product $product)
    {
        // 商品編集画面で会社の情報が必要なので、全ての会社の情報を取得します。
        $companies = Company::all();

        // 商品編集画面を表示します。その際に、商品の情報と会社の情報を画面に渡します。
        return view('products.edit', compact('product', 'companies'));
    }

    public function update(Request $request, Product $product)
    {
        // リクエストされた情報を確認して、必要な情報が全て揃っているかチェックします。
        $request->validate([
            'product_name' => 'required',
            'price' => 'required',
            'stock' => 'required',
        ]);

        // 商品の情報を更新します。
        $product->product_name = $request->product_name;
        $product->price = $request->price;
        $product->stock = $request->stock;

        // 更新した商品を保存します。
        $product->save();

        // 全ての処理が終わったら、商品一覧画面に戻ります。
        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        // 商品を削除します。
        $product->delete();

        // 全ての処理が終わったら、商品一覧画面に戻ります。
        return redirect('/products');
    }
}
?>