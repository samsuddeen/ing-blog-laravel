<?php

namespace App\Http\Requests\Comment;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
     public function authorize(): bool
    {
        return $this->user()->can('create comments');
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string'],
            'commentable_id' => ['required', 'integer'],
            'commentable_type' => ['required', 'string', Rule::in(['App\Models\Post'])],
        ];
    }
}
