<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'order_code', 'kategori_id', 'qr_id',
        'harga_paket', 'total_harga', 'status','waktu',
        'frame_id', 
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function qrAccess()
    {
        return $this->belongsTo(QrAccess::class, 'qr_id','id');
    }

    public function orderAddons()
    {
        return $this->hasMany(OrderAddon::class);
    }

    public function cloudGallery()
    {
        return $this->hasMany(CloudGallery::class);
    }

    public function frame()
{
    return $this->belongsTo(Frame::class);
}




    // HAPUS RELASI OTOMATIS
    protected static function booted()
    {
        static::deleting(function ($order) {
            // Hapus foto di storage
            foreach ($order->cloudGallery as $photo) {
                if ($photo->img_path && \Storage::disk('public')->exists($photo->img_path)) {
                    \Storage::disk('public')->delete($photo->img_path);
                }
                $photo->delete(); // hapus record di DB
            }

            // Kalau mau hapus addons juga
            $order->orderAddons()->delete();
        });
    }

}
