<?php

namespace App\Controllers;

use App\Models\laundry_price;
use App\Models\transactions;

class Form extends BaseController
{
    protected $new_data;
    protected $module;
    protected $table;
    protected $pfield;
    protected $controller_page;

    public function __construct()
    {
        $this->new_data['module_title']    = 'ORDERS';
        $this->new_data['module_desc']     = 'Description about transactions';
        $this->new_data['controller_page'] = $this->controller_page = site_url(uri_string());
        $this->new_data['current_module']  = 'home';
        $this->new_data['current_menu']    = '';
        $this->table                       = 'laundry_price';
        $this->pfield                      = 'price';
    }

    public function checkUrl($url)
    {
        $priceIDs = array(
            'regular'        => 1,
            'regular2'       => 1,
            'student'        => 3,
            'diystudent'     => 4,
            'diyregular'     => 2,
            'expressregular' => 5,
            'expressstudent' => 6,
        );

        if (isset($priceIDs[$url])) {
            return $priceIDs[$url];
        } else {
            echo 'invalid url';
        }
    }

    public function form()
    {
        $data = $this->new_data;

        $laundry_price = new laundry_price();
        $transactions  = new transactions();

        $priceID = $this->checkUrl(uri_string());

        $laundry_price->select('priceID');
        $laundry_price->where('priceID', $priceID);
        $checkResult = $laundry_price->countAllResults();

        if ($checkResult) {
            $config = $this->db->table('config');
            $config->select('*');
            $config->where('name', 'Max Load');
            $data['maxLoad'] = $config->get()->getRow();

            $config = $this->db->table('config');
            $config->select('*');
            $config->where('name', 'Min Load');
            $data['minLoad'] = $config->get()->getRow();

            $config = $this->db->table('config');
            $config->select('*');
            $config->where('name', 'remarks');
            $remarks = $config->get()->getRow();

            $data['remarks'] = explode(', ', $remarks->value);

            $laundry_price->select('*');
            $laundry_price->where('priceID', $priceID);
            $data['laundry'] = $laundry =  $laundry_price->get()->getRow();

            $transactions->select('customer, mobile');
            $transactions->orderBy('customer', 'ASC');
            $transactions->distinct();
            $data['customers'] = $transactions->get()->getResult();

            $data['title'] = $laundry->category;

            return view('pages/form/form', $data);
        } else {
            return redirect()->to('/');
        }
    }

    public function save()
    {
        $inputFields = array(
            'tranType',
            'customer',
            'mobile',
            'kiloQty',
            'kiloPrice',
            'kiloAmount',
            'comforterLoad',
            'comforterAmount',
            'comforterPrice',
            'detergentSet',
            'detergentPrice',
            'detergentAmount',
            'totalAmount',
            'cashChange',
            'remarks',
            'referenceNo',
            'bleachLoad',
            'bleachPrice',
            'bleachAmount',
            'totalLoads',
            'cash',
            'gCash',
        );

        $fields = array();

        $fields['cash']  = 0;
        $fields['gCash'] = 0;
        foreach ($inputFields as $f) {
            $fields[$f] = $this->request->getPost($f);
        }

        // set total paid
        $totalPaid = $fields['cash'] +  $fields['gCash'];

        //check balance
        $balance = 0;
        if ($fields['totalAmount'] > $totalPaid) {
            $balance    = $fields['totalAmount'] - $totalPaid;
        }

        // check amount paid
        $amountPaid = 0;
        if ($totalPaid >= $fields['totalAmount']) {
            $amountPaid = $fields['totalAmount'];
        } else {
            $amountPaid = $totalPaid;
        }

        // check cash change
        $cashChange = 0;
        if ($fields['cash'] > $fields['totalAmount']) {
            // set the cash field
            $fields['cash'] = $fields['totalAmount'];
            $cashChange = $fields['totalAmount'] - $fields['cash'];
        }

        //set payment method
        $paymentMethod = "";
        if ($fields['cash'] && $fields['gCash']) {
            $paymentMethod = 'Cash/Gcash';
        } else if ($fields['cash']) {
            $paymentMethod = 'Cash';
        } else if ($fields['gCash']) {
            $paymentMethod = 'GCash';
        }

        $fields['customer']      = strtoupper($fields['customer']);
        $fields['amountPaid']    = $amountPaid;
        $fields['cashChange']    = $cashChange;
        $fields['balance']       = $balance;
        $fields['paymentMethod'] = $paymentMethod;
        $fields['userID']        = $this->current_user;
        $fields['dateCreated']   = date('Y-m-d H:i:s');
        $fields['status']        = 1;

        $transactions = $this->db->table('transactions');
        $transactions->insert($fields);

        $transID = $this->db->insertID();

        //set qrCode
        $transactions->set('qrCode', md5($transID));
        $transactions->where('transID', $transID);
        $transactions->update();

        $transactions->where('transID', $transID);
        $dbResult = $transactions->get()->getRow();

        //set job orders
        $joborders = $this->db->table('job_orders');
        for ($i = 1; $i <= $fields['totalLoads']; $i++) {

            $order = array(
                'joNo'    => $i,
                'transID' => $transID,
                'status'  => 1
            );

            $joborders->insert($order);

            $joID = $this->db->insertID();

            $joborders->set('qrCode', md5($joID));
            $joborders->where('joID', $joID);
            $joborders->update();
        }

        $response = array(
            'qrCode' => $dbResult->qrCode
        );

        return $this->response->setJSON($response);
    }

    public function print($qrCode)
    {
        $data = array();

        $builder =  $this->db->table('transactions');
        $builder->select('transID');
        $builder->select('qrCode');
        $builder->select('customer');
        $builder->select('mobile');
        $builder->select('tranType');
        $builder->select('kiloQty');
        $builder->select('kiloPrice');
        $builder->select('kiloAmount');
        $builder->select('dateCreated');
        $builder->select('comforterLoad');
        $builder->select('comforterPrice');
        $builder->select('comforterAmount');
        $builder->select('detergentSet');
        $builder->select('detergentPrice');
        $builder->select('detergentAmount');
        $builder->select('bleachLoad');
        $builder->select('bleachPrice');
        $builder->select('bleachAmount');
        $builder->select('totalAmount');
        $builder->select('amountPaid');
        $builder->select('paymentMethod');
        $builder->select('referenceNo');
        $builder->select('balance');
        $builder->select('cash');
        $builder->select('gCash');
        $builder->select('cashChange');
        $builder->select('totalLoads');
        $builder->select('userID');
        $builder->select('status');
        $builder->where('qrCode', $qrCode);
        $data['customer'] = $customer = $builder->get()->getRow();

        $builder =  $this->db->table('users');
        $builder->select('username');
        $builder->select('firstName');
        $builder->select('lastName');
        $builder->select('userID');
        $builder->select('status');
        $builder->select('userType');
        $builder->select('empID');
        $builder->where('userID', $customer->userID);
        $data['cashier'] =  $cashier = $builder->get()->getRow();

        $data['title'] = 'Claim Slip';
        $data['description'] = 'This is ' . $customer->tranType . ' transaction';

        return view('pages/preview/print', $data);
    }

    public function job_order_print($qrCode)
    {
        $data = array();

        $builder =  $this->db->table('transactions');
        $builder->select('transID');
        $builder->select('qrCode');
        $builder->select('customer');
        $builder->select('mobile');
        $builder->select('tranType');
        $builder->select('kiloQty');
        $builder->select('kiloPrice');
        $builder->select('kiloAmount');
        $builder->select('comforterLoad');
        $builder->select('dateCreated');
        $builder->select('comforterPrice');
        $builder->select('comforterAmount');
        $builder->select('detergentSet');
        $builder->select('detergentPrice');
        $builder->select('detergentAmount');
        $builder->select('bleachLoad');
        $builder->select('bleachPrice');
        $builder->select('bleachAmount');
        $builder->select('totalAmount');
        $builder->select('amountPaid');
        $builder->select('paymentMethod');
        $builder->select('referenceNo');
        $builder->select('balance');
        $builder->select('cash');
        $builder->select('gCash');
        $builder->select('cashChange');
        $builder->select('totalLoads');
        $builder->select('remarks');
        $builder->select('userID');
        $builder->select('status');
        $builder->where('qrCode', $qrCode);
        $data['customer'] = $customer = $builder->get()->getRow();

        $builder =  $this->db->table('job_orders');
        $builder->select('joID');
        $builder->select('qrCode');
        $builder->select('joNo');
        $builder->select('transID');
        $builder->select('status');
        $builder->where('transID', $customer->transID);
        $data['joborders'] = $joborders = $builder->get()->getResult();

        $data['title']       = 'Job Order ' . $customer->tranType;
        $data['description'] = 'This is ' . $customer->tranType . ' transaction';

        return view('pages/preview/job_order_print', $data);
    }

    public function getCustomers($customer)
    {
        $transactions = new transactions();
        $transactions->select('customer, mobile');
        $transactions->like('customer', $customer);
        $transactions->distinct('customer'); // Use only distinct('customer') for distinct customers
        $transactions->orderBy('customer');
        $result = $transactions->get()->getResult();

        $formattedResult = [];
        foreach ($result as $row) {
            $formattedResult[] = [
                'label' => $row->customer . ' - ' . $row->mobile,
                'value' => $row->customer, // Replace with your method to get mobile number
                'mobile' => $row->mobile,
            ];
        }

        return $this->response->setJSON($formattedResult);
    }
}
