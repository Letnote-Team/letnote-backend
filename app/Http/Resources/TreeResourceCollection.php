<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TreeResourceCollection extends ResourceCollection
{
    public static $wrap = 'items';
    public $preserveKeys = true;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = $this->collection->mapWithKeys(fn ($item) => [
            strval($item->id) => (object) [
                'id' => strval($item->id),
                'children' => $item->children()->pluck('id')->map(fn ($id) => strval($id)),
                'hasChildren' => $item->children()->count() > 0,
                'isExpanded' => false,
                'data' => [
                    'title' => $item->title,
                ],
            ]
        ]);

        $data["0"] = [
            "id" => "0",
            "children" => $this->collection->where('parent_id', null)->pluck('id')->map(fn ($id) => strval($id)),
            'hasChildren' => true,
            "isExpanded" => true,
            "data" => [
                "title" => "root",
            ]
        ];

        return $data;
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return [
            'rootId' => '0',
        ];
    }
}
