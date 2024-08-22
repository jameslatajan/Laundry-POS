<?php

namespace App\Models;

use CodeIgniter\Model;

class expenses extends Model
{
    protected $table         = 'expenses';
    protected $primaryKey    = 'expID';
    protected $allowedFields = ['particular', 'amount', 'dateCreated', 'createdBy', 'status', 'expDate'];
}
