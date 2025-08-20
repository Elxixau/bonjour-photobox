<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Addon extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'harga'];

        // Relasi ke Kategori (many-to-one)
    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    // Relasi ke OrderAddon (one-to-many)
    public function orderAddons()
    {
        return $this->hasMany(OrderAddon::class, 'addons_id');
    }

    // Accessor untuk harga format Rupiah
    public function getHargaFormattedAttribute()
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }
}
