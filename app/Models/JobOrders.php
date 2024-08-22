<?php

namespace App\Models;

use CodeIgniter\Model;

class JobOrders extends Model
{
    protected $table = 'job_orders';
    protected $primaryKey = 'joID ';
    protected $allowedFields = [
        'joNo',
        'qrCode',
        'transID',
        'washerNo',
        'washDate',
        'washBy',
        'dryerNo',
        'dryDate',
        'dryBy',
        'foldDate',
        'foldBy',
        'readyDate',
        'readyBy',
        'status',
        'rackNo'
    ];
}
