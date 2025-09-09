<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KategoriBackground extends Model
{
  
    use HasFactory;

    protected $table = 'kategori_backgrounds';

    protected $fillable = [
        'kategori_id',
        'background_video',
        'background_color',
    ];

    /**
     * Relasi ke kategori (many to one).
     */
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }
    

}
