<?php

namespace App\Models;

use CodeIgniter\Model;

class Dsr extends Model
{
    protected $table         = 'dsr';
    protected $primaryKey    = 'dsrID';
    protected $allowedFields = [
        'salesDate', 'totalCash', 'totalGcash', 'totalSales', 'totalExpenses', 'inventorySales',
        'totalCollection', 'actualCash', 'userID', 'checkBy', 'totalUnpaid', 'dateCreated', 'variance',
        'cash_collection','gcash_collection', 'cash_inventorySales', 'gcash_inventorySales'
    ];
}
