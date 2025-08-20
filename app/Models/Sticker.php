<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sticker extends Model
{
    
    use HasFactory;

    protected $fillable = ['name', 'img_path', 'active'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
