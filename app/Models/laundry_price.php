<?php

namespace App\Models;

use CodeIgniter\Model;

class laundry_price extends Model
{
    protected $table = 'laundry_price';
    protected $primaryKey = 'priceID';
    protected $allowedFields = ['category', 'kilo', 'comforter', 'detergent'];
}
