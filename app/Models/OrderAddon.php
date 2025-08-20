<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderAddon extends Model
{
    use HasFactory;

    protected $table = 'order_addons';

    protected $fillable = [
        'order_id',
        'addons_id',
        'qty',
        'harga',
    ];

    // Relasi ke Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relasi ke Addon
    public function addon()
    {
        return $this->belongsTo(Addon::class, 'addons_id');
    }
}
