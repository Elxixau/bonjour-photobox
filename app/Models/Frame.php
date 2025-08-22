<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Frame extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'img_path', 'active', 'jumlah_layout'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function layouts()
{
    return $this->hasMany(FrameLayout::class);
}

}
