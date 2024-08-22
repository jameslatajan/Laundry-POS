<?php

namespace App\Models;

use CodeIgniter\Model;

class sale_details extends Model
{
    protected $table = 'sale_details';
    protected $primaryKey = 'detailID';
    protected $allowedFields = ['headerID','itemID','description','price','qty','itemAmount','dateCreated','userID'];
}