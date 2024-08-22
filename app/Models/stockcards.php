<?php

namespace App\Models;

use CodeIgniter\Model;

class stockcards extends Model
{
    protected $table = 'stockcards';
    protected $primaryKey = 'stockID';
    protected $allowedFields = [
        'date', 'itemID', 'begBal',
        'debit', 'credit', 'endBal', 'refNo', 'remarks', 'insertedBy'
    ];
}
