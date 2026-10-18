<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($productId)],
            'description' => ['nullable', 'string'],
            'composition' => ['nullable', 'string'],
            'care_instructions' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'gt:price'],
            'collection' => ['nullable', 'string', 'max:255'],
            'gender' => ['required', 'in:femme,homme,enfant,mixte'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],

            'variants' => ['required', 'array', 'min:1'],
            'variants.*.size' => ['required', 'string', 'max:10'],
            'variants.*.color_name' => ['required', 'string', 'max:255'],
            'variants.*.color_code' => ['required', 'string', 'max:20'],
            'variants.*.stock' => ['required', 'integer', 'min:0'],
            'variants.*.sku' => ['required', 'string', 'max:255'],

            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:4096'],
            'delete_images' => ['nullable', 'array'],
            'delete_images.*' => ['integer', 'exists:product_images,id'],
            'primary_image' => ['nullable', 'integer', 'exists:product_images,id'],
        ];
    }
}
