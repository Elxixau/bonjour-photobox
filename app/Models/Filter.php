<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Filter extends Model
{
      protected $fillable = ['name', 'img_path', 'active'];
         // Relasi one-to-one: satu QR untuk satu order
    public function order()
    {
        return $this->hasOne(Order::class, 'qr_id', 'id');
    }
}
