<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'commentable_type' => $this->commentable_type,
            'commentable_id' => $this->commentable_id,

            'user' => new UserResource($this->whenLoaded('user')),
            'post' => $this->when(
                $this->commentable_type === 'App\Models\Post' && $this->relationLoaded('commentable'),
                new PostResource($this->commentable)
            ),

            'can_edit' => $this->when(
                $request->user(),
                fn() => $request->user()->can('edit any comments') ||
                        ($request->user()->can('edit own comments') && $request->user()->id === $this->user_id)
            ),
            'can_delete' => $this->when(
                $request->user(),
                fn() => $request->user()->can('delete any comments') ||
                        ($request->user()->can('delete own comments') && $request->user()->id === $this->user_id) ||
                        ($this->commentable_type === 'App\Models\Post' && $this->commentable->user_id === $request->user()->id)
            ),

            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
