<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'staff' => new UserResource($this->staff),
            'title' => $this->title,
            'description' => $this->description,
            'tags' => $this->tags,
            'file' => $this->file,
            'completion_date' => $this->completion_date,
        ];
    }
}
