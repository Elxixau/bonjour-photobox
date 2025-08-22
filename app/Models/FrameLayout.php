<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class FrameLayout extends Model
{
    
    use HasFactory;

    protected $fillable = [
        'frame_id',
        'slot_number',
        'x',
        'y',
        'width',
        'height',
    ];

    protected $casts = [
        'config' => 'array',
    ];

    // relasi ke Frame
    public function frame()
    {
        return $this->belongsTo(Frame::class);
    }

}
