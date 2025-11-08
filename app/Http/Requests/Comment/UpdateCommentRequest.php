<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
   public function authorize(): bool
    {
        $comment = $this->route('comment');

        return $this->user()->can('edit any comments') ||
               ($this->user()->can('edit own comments') && $this->user()->id === $comment->user_id);
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string'],
        ];
    }
}
