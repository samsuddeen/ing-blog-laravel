<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'status' => $this->status,
            'published_at' => $this->published_at?->toDateTimeString(),

            'author' => new UserResource($this->whenLoaded('user')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),

            'comments_count' => $this->when(
                isset($this->comments_count),
                $this->comments_count
            ),

            'is_published' => $this->isPublished(),
            'can_edit' => $this->when(
                $request->user(),
                fn() => $request->user()->can('edit any posts') ||
                        ($request->user()->can('edit own posts') && $request->user()->id === $this->user_id)
            ),
            'can_delete' => $this->when(
                $request->user(),
                fn() => $request->user()->can('delete any posts') ||
                        ($request->user()->can('delete own posts') && $request->user()->id === $this->user_id)
            ),

            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
