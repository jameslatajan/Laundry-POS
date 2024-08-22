<?php

namespace App\Models;

use CodeIgniter\Model;

class Config extends Model
{
    protected $table         = 'config';
    protected $primaryKey    = 'configID';
    protected $allowedFields = ['name','value','description'];
}
