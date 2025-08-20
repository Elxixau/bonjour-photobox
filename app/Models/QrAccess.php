<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QrAccess extends Model
{
    use HasFactory;
     protected $table = 'qr_access'; 
    protected $fillable = ['url_cloud', 'img_path'];


}
