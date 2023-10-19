<?php

namespace App\Http\ResultResource;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ResultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "survey" => $request->survey,
            "name" => $request->name,
            "price" => $request->price,
            'url' => $request->url,
            "created_at" => Carbon::now()->format('Y-m-d'),
            "updated_at" => Carbon::now()->format("Y-m-d"),
        ];
    }
}
