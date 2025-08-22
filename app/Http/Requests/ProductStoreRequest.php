<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProductStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:products,slug'],
            'price' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The product name is required.',
            'name.max' => 'The product name cannot exceed 255 characters.',
            'slug.required' => 'The product slug is required.',
            'slug.unique' => 'This slug is already taken. Please choose a different one.',
            'price.required' => 'The product price is required.',
            'price.numeric' => 'The price must be a valid number.',
            'price.min' => 'The price cannot be negative.',
            'price.decimal' => 'The price must have at most 2 decimal places.',
            'image.image' => 'The uploaded file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpg, jpeg, png, webp.',
            'image.max' => 'The image size cannot exceed 2MB.',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'product name',
            'slug' => 'product slug',
            'price' => 'product price',
            'description' => 'product description',
            'image' => 'product image',
        ];
    }
}
