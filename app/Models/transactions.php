<?php

namespace App\Models;

use CodeIgniter\Model;

class transactions extends Model
{

    protected $table = 'transactions';
    protected $primaryKey = 'transID';
    protected $allowedFields =
    [
        'qrCode', 'customer', 'mobile', 'tranType', 'kiloQty', 'kiloPrice', 'kiloAmount', 'comforterLoad', 'comforterPrice', 'comforterAmount', 'detergentSet', 'detergentPrice',
        'detergentAmount', 'totalAmount', 'amountPaid', 'balance', 'balance', 'cashChange', 'loads', 'washerNo', 'dryerNo', 'dateCreated', 'dateWashed', 'dateDrying', 'dateFolded',
        'dateReleased', 'payment1', 'payment1Date', 'payment2', 'payment2Date', 'status', 'remarks', 'userID', 'sortedBy', 'washedBy', 'driedBy', 'folderBy', 'readyBy', 'releasedBy',
        'canceledBy', 'dateCanceled', 'canceledRemarks', 'paymentMethod', 'referenceNo', 'payment1Method', 'payment1ReferenceNo', 'payment2Method', 'payment2ReferenceNo', 'bleachLoad',
        'bleachAmount', 'bleachPrice', 'cash', 'gCash', 'payment1Cash', 'payment1GCash', 'payment1Cashier', 'payment2Cash', 'payment2GCash', 'payment2Cashier','dateDried', 'foldedBy', 
        'dateReady', 'totalLoads', 'isSms'
    ];
}
