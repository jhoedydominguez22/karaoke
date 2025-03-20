<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QRCodeModel extends Model
{
    protected $connection = 'mongodb';  // Usar la conexión MongoDB
    protected $collection = 'qr_codes'; // Colección para los códigos QR

    protected $fillable = ['numeroMesa', 'qrUrl']; // Campos que pueden ser llenados
}
