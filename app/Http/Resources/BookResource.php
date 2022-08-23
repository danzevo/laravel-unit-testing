<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        foreach($this->authors as $row) {
            $row->makeHidden(['pivot']);
        }

        return [
            'id' => $this->id,
            'isbn' => $this->isbn,
            'title' => $this->title,
            'description' => $this->description,
            'authors' => $this->authors,
            'review' => [
                'avg' => round($this->reviews->avg('review')),
                'count' => $this->reviews->count()
            ],
        ];
    }
}
