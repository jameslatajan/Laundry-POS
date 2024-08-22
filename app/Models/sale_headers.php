<?php

namespace App\Models;

use CodeIgniter\Model;

class sale_headers extends Model
{
    protected $table = 'sale_headers';
    protected $primaryKey = 'headerID';
    protected $allowedFields = ['totalAmount','amountPaid','gCash','cash','cashChange','balance','dateCreated','userID'];
}