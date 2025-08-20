<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CloudGallery extends Model
{
    use HasFactory;

     protected $table = 'cloud_gallery';  // <-- tambahkan ini
    protected $fillable = ['order_id', 'img_path'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    
}
