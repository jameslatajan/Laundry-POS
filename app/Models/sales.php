<?php

namespace App\Models;

use CodeIgniter\Model;

class sales extends Model
{
    protected $table = 'sales';
    protected $primaryKey = 'salesID';
    protected $allowedFields = ['itemID', 'description', 'price', 'qty' ,'dateCreated', 'userID','itemCost', 'amount','salesDate','valeBy','paymentMethod','referenceNo','cashier'];
}
