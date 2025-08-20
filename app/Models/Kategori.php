<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kategori extends Model
{
    use HasFactory;
    
    protected $table = 'kategori'; 
    protected $fillable = ['nama', 'harga', 'waktu', 'jumlah_cetak'];

// Relasi ke Addon (one-to-many)
    public function addons()
    {
        return $this->hasMany(Addon::class, 'kategori_id');
    }

    // Relasi ke Order (one-to-many)
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Accessor untuk harga format Rupiah
    public function getHargaFormattedAttribute()
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }
}
