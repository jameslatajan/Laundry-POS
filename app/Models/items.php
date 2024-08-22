<?php

namespace App\Models;

use CodeIgniter\Model;

class items extends Model
{
    protected $table = 'items';
    protected $primaryKey = 'itemID';
    protected $allowedFields = ['description', 'price', 'qty', 'cost','status'];
}
