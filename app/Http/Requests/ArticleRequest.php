<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_name' => 'required|string|max:255',
            'company_id' => 'required|integer|exists:companies,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'comment' => 'nullable|string|max:1000',
            'img_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    /**
     * Get the custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'product_name.required' => '商品名を入力してください。',
            'product_name.string' => '商品名は文字列である必要があります。',
            'product_name.max' => '商品名は255文字以内で入力してください。',
            'company_id.required' => 'メーカーを選択してください。',
            'company_id.integer' => 'メーカーIDは整数である必要があります。',
            'company_id.exists' => '選択されたメーカーは存在しません。',
            'price.required' => '価格を入力してください。',
            'price.numeric' => '価格は数値である必要があります。',
            'price.min' => '価格は0以上である必要があります。',
            'stock.required' => '在庫数を入力してください。',
            'stock.integer' => '在庫数は整数である必要があります。',
            'stock.min' => '在庫数は0以上である必要があります。',
            'comment.string' => 'コメントは文字列である必要があります。',
            'comment.max' => 'コメントは1000文字以内で入力してください。',
            'img_path.image' => '商品画像は画像ファイルである必要があります。',
            'img_path.mimes' => '商品画像の形式はjpeg, png, jpg, gif, svgのいずれかである必要があります。',
            'img_path.max' => '商品画像のサイズは2048KB以内である必要があります。',
        ];
    }
}
