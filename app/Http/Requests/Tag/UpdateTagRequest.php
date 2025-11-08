<?php

namespace App\Http\Requests\Tag;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTagRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
   public function authorize(): bool
    {
        return $this->user()->can('edit tags');
    }

    public function rules(): array
    {
        $tagId = $this->route('tag')->id;

        return [
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('tags')->ignore($tagId)],
        ];
    }
}
