<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Product extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        return [
            'title' => $this->title,
            'price' => $this->price,
            'offer_price' => $this->offer_price,
            'quantity' => $this->quantity,
            'action' => "<a class='btn btn-primary' href=".route('admin.product.edit', $this->id ).">Edit</a> <a class='btn btn-danger' href='".route('admin.product.destroy',$this->id)."'>Delete</a>",
        ];


    }
}