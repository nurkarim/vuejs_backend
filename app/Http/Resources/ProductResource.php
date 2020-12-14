<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{

    public function toArray($request)
    {
        if ($this->image==null || $this->image==""){
            $image='no-image.jpg';
        }else{
            $image=$this->image;
        }
        return [
            'id'=>$this->id,
            'title'=>$this->title,
            'description'=>$this->description,
            'price'=>$this->price,
            'image'=>url('/').'/images/'.$image,
            'created_at'=>date('d/m/Y h:i',strtotime($this->created_at)),
        ];
    }
}
