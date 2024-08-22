<?php

namespace App\Models;

use CodeIgniter\Model;

class Detailed_dsr extends Model
{
    protected $table         = 'detailed_dsr';
    protected $primaryKey    = 'dsrID';
    protected $allowedFields = [
        'ds_cash', 'ds_gcash', 'ds_unpaid', 'ds_total', 'col_cash',
        'col_gcash','col_total','item_cash','item_gcash',
        'item_total','total_cash','total_gcash','total_expenses',
        'expected_cash','expected_gcash','remittance','variance','status',
        'sales_date','date_created','userID','shift','dateSettled','varSettledAmt'
    ];
}
