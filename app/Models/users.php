<?php

namespace App\Models;

use CodeIgniter\Model;

class users extends Model
{
    protected $table         = 'users';
    protected $primaryKey    = 'userID';
    protected $allowedFeilds = ['username', 'password', 'userType', 'status', 'isDsr', 'lastLogout', 'lastLogin'];
}
