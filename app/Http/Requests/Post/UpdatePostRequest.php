<?php

namespace App\Http\Requests\Post;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
   public function authorize(): bool
    {
        $post = $this->route('post');

        return $this->user()->can('edit any posts') ||
               ($this->user()->can('edit own posts') && $this->user()->id === $post->user_id);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'published_at' => ['nullable', 'date'],
            'tags' => ['required', 'array'],
            'tags.*' => ['exists:tags,id'],
        ];
    }
}
