<?php

namespace App\Controllers;

use App\Models\JobOrders;
use App\Models\transactions;
use App\Models\users;
use CodeIgniter\Controller;

require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class Transaction extends BaseController
{
    protected $table;
    protected $pfield;
    protected $controller_page;

    public function __construct()
    {
        $this->data['module_title']    = 'TRANSACTION';
        $this->data['module_desc']     = 'Description about transactions';
        $this->data['controller_page'] = $this->controller_page = site_url('transaction');
        $this->data['current_module']  = 'transactions';
        $this->data['current_menu']    = '';
        $this->table                   = 'transactions';
        $this->pfield                  = 'transID';
    }

    public function list()
    {
        $data = $this->data;

        $condition_fields = array(
            array(
                'variable'      => 'transID',
                'field'         => $this->table . '.transID',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'customer',
                'field'         => $this->table . '.customer',
                'default_value' => "",
                'operator'      => 'like_after'
            ),
            array(
                'variable'      => 'mobile',
                'field'         => $this->table . '.mobile',
                'default_value' => "",
                'operator'      => 'like_after'
            ),
            array(
                'variable'      => 'status',
                'field'         => $this->table . '.status',
                'default_value' => "",
                'operator'      => 'where'
            ),
            array(
                'variable'      => 'paymentMethod',
                'field'         => $this->table . '.paymentMethod',
                'default_value' => "",
                'operator'      => 'where'
            ),
            array(
                'variable'      => 'userID',
                'field'         =>  'users.userID',
                'default_value' => "",
                'operator'      => 'where'
            ),
        );

        // sorting fields
        $sorting_fields = array(
            'dateCreated' => 'desc',
        );

        //get the controller
        $controller = service('uri')->getSegment(1);

        if ($this->request->getGet('page')) {
            $page = $this->request->getGet('page');
        } else {
            $page = 1;
        }

        // start source of filtering
        $filter_source = 0;  // default/blank
        if ($this->request->getPost('filterflag') || $this->request->getPost('sortby')) {
            $filter_source = 1;
        } else {
            foreach ($condition_fields as $key) {
                if ($this->request->getPost($key['variable'])) {
                    $filter_source = 1;  // form filters
                    break;
                }
            }
        }

        if (!$filter_source) {
            foreach ($condition_fields as $key) {
                if ($this->session->get($controller . '_' . $key['variable']) || $this->session->get($controller . '_sortby') || $this->session->get($controller . '_sortorder')) {
                    $filter_source = 2;  // session
                    break;
                }
            }
        }

        switch ($filter_source) {
            case 1:
                foreach ($condition_fields as $key) {
                    ${$key['variable']} = $this->request->getPost($key['variable']);
                }

                $startDate = $this->request->getPost('startDate');
                $endDate   = $this->request->getPost('endDate');
                $sortby    = $this->request->getPost('sortby');
                $sortorder = $this->request->getPost('sortorder');
                break;
            case 2:
                foreach ($condition_fields as $key) {
                    ${$key['variable']} = $this->session->get($controller . '_' . $key['variable']);
                }

                $startDate = $this->session->get($controller . '_startDate');
                $endDate   = $this->session->get($controller . '_endDate');
                $sortby    = $this->session->get($controller . '_sortby');
                $sortorder = $this->session->get($controller . '_sortorder');
                break;
            default:
                foreach ($condition_fields as $key) {
                    ${$key['variable']} = $key['default_value'];
                }

                $startDate = "";
                $endDate   = "";
                $sortby    = "";
                $sortorder = "";
        }
        //end source of filtering

        if (!$startDate) {
            $startDate = date('Y-m-01');
            $endDate   = date('Y-m-d');
        } else if (strtotime($startDate) > strtotime($endDate)) {
            $endDate = $startDate;
        }

        $builder = $this->db->table($this->table); // initialize table

        //count total rows
        $builder->select('transactions.qrCode');
        $data['count_rows'] = $count = $builder->countAllResults();

        if ($this->request->getPost('limit')) {
            if ($this->request->getPost('limit') == "All") {
                $limit = $count;
            } else {
                $limit = $this->request->getPost('limit');
            }
        } else if ($this->session->get($controller . '_limit')) {
            $limit = $this->session->get($controller . '_limit');
        } else {
            $limit = 8;  // default limit
        }

        // set session variables
        foreach ($condition_fields as $key) {
            $this->session->set($controller . '_' . $key['variable'], ${$key['variable']});
        }

        $this->session->set($controller . '_startDate', $startDate);
        $this->session->set($controller . '_endDate', $endDate);
        $this->session->set($controller . '_sortby', $sortby);
        $this->session->set($controller . '_sortorder', $sortorder);
        $this->session->set($controller . '_limit', $limit);

        // assign data variables for views
        foreach ($condition_fields as $key) {
            $data[$key['variable']] = ${$key['variable']};
        }

        // select
        $builder->select('transactions.qrCode');
        $builder->select('transactions.dateCreated');
        $builder->select('transactions.transID');
        $builder->select('transactions.customer');
        $builder->select('transactions.mobile');
        $builder->select('transactions.cash');
        $builder->select('transactions.gCash');
        $builder->select('transactions.comforterLoad');
        $builder->select('transactions.kiloQty');
        $builder->select('transactions.amountPaid');
        $builder->select('transactions.balance');
        $builder->select('transactions.totalAmount');
        $builder->select('transactions.status');
        $builder->select('transactions.payment1Cash');
        $builder->select('transactions.payment2Cash');
        $builder->select('transactions.payment1Gcash');
        $builder->select('transactions.payment2Gcash');
        $builder->select('transactions.paymentMethod');
        $builder->select('users.userID');
        $builder->select('users.username');
        $builder->select('users.firstName');
        $builder->join('users', 'users.userID = transactions.userID', 'left');

        // set conditions here
        foreach ($condition_fields as $key) {
            $operators = explode('_', $key['operator']);
            $operator  = $operators[0];
            // check if the operator is like
            if (count($operators) > 1) {
                // like operator
                if (trim(${$key['variable']}) != '' && $key['field'])
                    $builder->$operator($key['field'], ${$key['variable']}, $operators[1]);
            } else {
                if (trim(${$key['variable']}) != '' && $key['field'])
                    $builder->$operator($key['field'], ${$key['variable']});
            }
        }

        // date range
        $startDate = date('Y-m-d 00:00:00', strtotime($startDate));
        $endDate   = date('Y-m-d 23:59:59', strtotime($endDate));

        if ($startDate != '' && $startDate != '1970-01-01') {
            $builder->where("(transactions.dateCreated >= '$startDate' and transactions.dateCreated <= '$endDate')");
        }

        // get
        // $data['records'] = $records =  $builder->get()->getResult();
        $data['ttl_rows'] = $ttl_rows = $builder->countAllResults();

        // set pagination
        $data['pagination'] = $pagination = $this->pagination->makeLinks($page, $limit, $ttl_rows, 'custom_view');

        // initilaize select
        $builder->select('transactions.qrCode');
        $builder->select('transactions.dateCreated');
        $builder->select('transactions.transID');
        $builder->select('transactions.customer');
        $builder->select('transactions.mobile');
        $builder->select('transactions.cash');
        $builder->select('transactions.gCash');
        $builder->select('transactions.comforterLoad');
        $builder->select('transactions.kiloQty');
        $builder->select('transactions.amountPaid');
        $builder->select('transactions.balance');
        $builder->select('transactions.totalAmount');
        $builder->select('transactions.status');
        $builder->select('transactions.payment1Cash');
        $builder->select('transactions.payment2Cash');
        $builder->select('transactions.payment1Gcash');
        $builder->select('transactions.payment2Gcash');
        $builder->select('transactions.paymentMethod');
        $builder->select('users.userID');
        $builder->select('users.username');
        $builder->select('users.firstName');
        $builder->join('users', 'users.userID = transactions.userID', 'left');

        // set conditions here
        foreach ($condition_fields as $key) {
            $operators = explode('_', $key['operator']);
            $operator  = $operators[0];
            // check if the operator is like
            if (count($operators) > 1) {
                // like operator
                if (trim(${$key['variable']}) != '' && $key['field'])
                    $builder->$operator($key['field'], ${$key['variable']}, $operators[1]);
            } else {
                if (trim(${$key['variable']}) != '' && $key['field'])
                    $builder->$operator($key['field'], ${$key['variable']});
            }
        }

        // date range
        $startDate = date('Y-m-d 00:00:00', strtotime($startDate));
        $endDate   = date('Y-m-d 23:59:59', strtotime($endDate));

        if ($startDate != '' && $startDate != '1970-01-01') {
            $builder->where("(transactions.dateCreated >= '$startDate' and transactions.dateCreated <= '$endDate')");
        }


        if ($sortby && $sortorder) {
            $builder->orderBy($sortby, $sortorder);

            if (!empty($sorting_fields)) {
                foreach ($sorting_fields as $fld => $s_order) {
                    if ($fld != $sortby) {
                        $builder->orderBy($fld, $s_order);
                    }
                }
            }
        } else {
            $ctr = 1;
            if (!empty($sorting_fields)) {
                foreach ($sorting_fields as $fld => $s_order) {
                    if ($ctr == 1) {
                        $sortby    = $fld;
                        $sortorder = $s_order;
                    }
                    $builder->orderBy($fld, $s_order);

                    $ctr++;
                }
            }
        }

        $offset = 0;
        if ($limit) {
            $offset = ($page - 1) * $limit;
            if ($offset) {
                $builder->limit($limit,  $offset);
            } else {
                $builder->limit($limit);
            }
        }

        // get
        $data['records'] = $records =  $builder->get()->getResult();

        $builder3 = $this->db->table('users');
        $builder3->where('userID !=', 1);
        $cashier = $builder3->get()->getResult();
        $data['cashiers'] = $cashier;

        $data['title'] = 'Transactions';

        // assigning variables
        $data['sortby']     = $sortby;
        $data['sortorder']  = $sortorder;
        $data['limit']      = $limit;
        $data['offset']     = $offset;
        $data['startDate']  = $startDate;
        $data['endDate']    = $endDate;

        echo view('header', $data);
        echo view('pages/transactions/transactions');
        echo view('footer');
    }

    public function release()
    {
        $qrCode  = $this->request->getPost('qrCode');

        $data = array(
            'releasedBy'   => $this->current_user,
            'dateReleased' => date('Y-m-d H:i:s'),
            'status'       => 7,
        );

        $builder = $this->db->table('transactions');
        $builder->set($data);
        $builder->where('qrCode', $qrCode);
        $builder->update();


        return redirect()->to($this->controller_page . '/view/' . $qrCode);
    }

    public function cancel()
    {
        $qrCode          = $this->request->getPost('qrCode');
        $canceledRemarks = $this->request->getPost('canceledRemarks');

        $data = array(
            'canceledBy'      => $this->current_user,
            'dateCanceled'    => date('Y-m-d H:i:s'),
            'canceledRemarks' => $canceledRemarks,
            'status'          => 0,
        );

        $builder = $this->db->table('transactions');
        $builder->set($data);
        $builder->where('qrCode', $qrCode);
        $builder->update();


        return redirect()->to($this->controller_page . '/view/' . $qrCode);
    }

    public function payment1()
    {
        $qrCode              = $this->request->getPost('qrCode');
        $payment1Cash        = $this->request->getPost('payment1Cash');
        $payment1GCash       = $this->request->getPost('payment1GCash');
        $payment1ReferenceNo = $this->request->getPost('payment1ReferenceNo');
        $payment1Date        = $this->request->getPost('payment1Date');

        $builder = $this->db->table('transactions');
        $builder->select('amountPaid, balance, totalAmount');
        $builder->where('qrcode', $qrCode);
        $customer = $builder->get()->getRow();

        $totalPayment  = floatval($payment1Cash)  + floatval($payment1GCash);
        $newAmountPaid = $customer->amountPaid + $totalPayment;
        $newBalance    = $customer->balance - $totalPayment;

        $payment1Method = "";
        if ($payment1Cash) {
            $payment1Method = "Cash";
        } elseif ($payment1GCash) {
            $payment1Method = "GCash";
        } else {
            $payment1Method = "Cash/GCash";
        }

        $data = array(
            'amountPaid'          => $newAmountPaid,
            'payment1Cash'        => $payment1Cash,
            'payment1GCash'       => $payment1GCash,
            'payment1Date'        => $payment1Date . ' ' . date('H:i:s'),
            'balance'             => $newBalance,
            'amountPaid'          => $newAmountPaid,
            'payment1Method'      => $payment1Method,
            'payment1ReferenceNo' => $payment1ReferenceNo,
            'payment1Cashier'     => $this->current_user,
        );

        $builder = $this->db->table('transactions');
        $builder->set($data);
        $builder->where('qrCode', $qrCode);
        $builder->update();

        return redirect()->to($this->controller_page . '/view/' . $qrCode);
    }

    public function payment2()
    {
        $qrCode              = $this->request->getPost('qrCode');
        $payment2Cash        = $this->request->getPost('payment2Cash');
        $payment2GCash       = $this->request->getPost('payment2GCash');
        $payment2ReferenceNo = $this->request->getPost('payment2ReferenceNo');
        $payment2Date        = $this->request->getPost('payment2Date');

        $builder = $this->db->table('transactions');
        $builder->select('amountPaid, balance, totalAmount');
        $builder->where('qrcode', $qrCode);
        $customer = $builder->get()->getRow();

        $totalPayment  = floatval($payment2Cash)  + floatval($payment2GCash);
        $newAmountPaid = $customer->amountPaid + $totalPayment;
        $newBalance    = $customer->balance - $totalPayment;

        $payment2Method = "";
        if ($payment2Cash) {
            $payment2Method = "Cash";
        } elseif ($payment2GCash) {
            $payment2Method = "GCash";
        } else {
            $payment2Method = "Cash/GCash";
        }

        $data = array(
            'amountPaid'          => $newAmountPaid,
            'payment2Cash'        => $payment2Cash,
            'payment2GCash'       => $payment2GCash,
            'payment2Date'        => $payment2Date . ' ' . date('H:i:s'),
            'balance'             => $newBalance,
            'amountPaid'          => $newAmountPaid,
            'payment2Method'      => $payment2Method,
            'payment2ReferenceNo' => $payment2ReferenceNo,
            'payment2Cashier'     => $this->current_user,
        );


        $builder = $this->db->table('transactions');
        $builder->set($data);
        $builder->where('qrCode', $qrCode);
        $builder->update();

        return redirect()->to($this->controller_page . '/view/' . $qrCode);
    }

    public function view($qrCSode)
    {
        $data = $this->data;

        $transactions = new transactions();
        $user         = new users();
        $joborders    = new JobOrders();

        $transactions->select('transID');
        $transactions->select('qrCode');
        $transactions->select('customer');
        $transactions->select('mobile');
        $transactions->select('kiloQty');
        $transactions->select('kiloPrice');
        $transactions->select('kiloAmount');
        $transactions->select('comforterLoad');
        $transactions->select('comforterPrice');
        $transactions->select('comforterAmount');
        $transactions->select('detergentSet');
        $transactions->select('detergentPrice');
        $transactions->select('detergentAmount');
        $transactions->select('bleachLoad');
        $transactions->select('bleachPrice');
        $transactions->select('bleachAmount');
        $transactions->select('totalAmount');
        $transactions->select('amountPaid');
        $transactions->select('referenceNo');
        $transactions->select('paymentMethod');
        $transactions->select('balance');
        $transactions->select('cash');
        $transactions->select('gCash');
        $transactions->select('cashChange');
        $transactions->select('loads');
        $transactions->select('dateCreated');
        $transactions->select('payment1Cashier');
        $transactions->select('payment1Cash');
        $transactions->select('payment1GCash');
        $transactions->select('payment1Date');
        $transactions->select('payment1Method');
        $transactions->select('payment1ReferenceNo');
        $transactions->select('payment2Cashier');
        $transactions->select('payment2Cash');
        $transactions->select('payment2GCash');
        $transactions->select('payment2Cashier');
        $transactions->select('payment2Date');
        $transactions->select('payment2Method');
        $transactions->select('payment2ReferenceNo');
        $transactions->select('dateReleased');
        $transactions->select('dateCanceled');
        $transactions->select('totalLoads');
        $transactions->select('canceledRemarks');
        $transactions->select('isSms');
        $transactions->select('userID');
        $transactions->select('status');
        $transactions->select('remarks');
        $transactions->where('qrCode', $qrCSode);
        $data['customer'] = $rec = $transactions->get()->getRow();

        $joborders->select('job_orders.washBy');
        $joborders->select('job_orders.washDate');
        $joborders->select('job_orders.washerNo');
        $joborders->select('job_orders.transID');
        $joborders->select('job_orders.joNo');
        $joborders->select('job_orders.qrCode');
        $joborders->select('job_orders.joID');
        $joborders->select('job_orders.status');
        $joborders->select('users.username');
        $joborders->select('users.lastName');
        $joborders->select('users.firstName');
        $joborders->select('users.userID');
        $joborders->join('users', 'users.userID=job_orders.washBy', 'left');
        $joborders->where('transID', $rec->transID);
        $joborders->orderBy('joNo', 'asc');
        $data['wash'] = $joborders->get()->getResult();

        $joborders->select('job_orders.dryBy');
        $joborders->select('job_orders.dryDate');
        $joborders->select('job_orders.dryerNo');
        $joborders->select('job_orders.transID');
        $joborders->select('job_orders.joNo');
        $joborders->select('job_orders.qrCode');
        $joborders->select('job_orders.joID');
        $joborders->select('job_orders.status');
        $joborders->select('users.username');
        $joborders->select('users.lastName');
        $joborders->select('users.firstName');
        $joborders->select('users.userID');
        $joborders->join('users', 'users.userID=job_orders.dryBy', 'left');
        $joborders->where('transID', $rec->transID);
        $joborders->orderBy('joNo', 'asc');
        $data['dry'] = $joborders->get()->getResult();

        $joborders->select('job_orders.foldBy');
        $joborders->select('job_orders.foldDate');
        $joborders->select('job_orders.transID');
        $joborders->select('job_orders.joNo');
        $joborders->select('job_orders.qrCode');
        $joborders->select('job_orders.joID');
        $joborders->select('job_orders.status');
        $joborders->select('users.username');
        $joborders->select('users.lastName');
        $joborders->select('users.firstName');
        $joborders->select('users.userID');
        $joborders->join('users', 'users.userID=job_orders.foldBy', 'left');
        $joborders->where('transID',  $rec->transID);
        $joborders->orderBy('joNo', 'asc');
        $data['fold'] = $joborders->get()->getResult();

        $joborders->select('job_orders.readyBy');
        $joborders->select('job_orders.readyDate');
        $joborders->select('job_orders.rackNo');
        $joborders->select('job_orders.transID');
        $joborders->select('job_orders.joNo');
        $joborders->select('job_orders.qrCode');
        $joborders->select('job_orders.joID');
        $joborders->select('job_orders.status');
        $joborders->select('users.username');
        $joborders->select('users.lastName');
        $joborders->select('users.firstName');
        $joborders->select('users.userID');
        $joborders->join('users', 'users.userID=job_orders.readyBy', 'left');
        $joborders->where('transID',  $rec->transID);
        $joborders->orderBy('joNo', 'asc');
        $data['ready'] = $joborders->get()->getResult();

        $rackNos = [];
        foreach ($data['ready'] as $row) {
            $rackNo = $row->rackNo;
            if ($rackNo !== '0' && !in_array($rackNo, $rackNos)) {
                $rackNos[] = $rackNo;
            }
        }

        $data['rackNos'] = $rackNos;

        $transactions->select('users.firstName');
        $transactions->select('users.lastName');
        $transactions->select('users.username');
        $transactions->select('users.userType');
        $transactions->select('users.status');
        $transactions->join('users', 'users.userID=transactions.releasedBy', 'left');
        $transactions->where('transID', $rec->transID);
        $joborders->orderBy('joNo', 'asc');
        $data['releasedBy'] = $transactions->get()->getRow();

        $transactions->select('users.firstName');
        $transactions->select('users.lastName');
        $transactions->select('users.username');
        $transactions->select('users.userType');
        $transactions->select('users.status');
        $transactions->join('users', 'users.userID=transactions.canceledBy', 'left');
        $transactions->where('transID', $rec->transID);
        $joborders->orderBy('joNo', 'asc');
        $data['canceledBy'] = $transactions->get()->getRow();

        $user->select('users.firstName');
        $user->select('users.lastName');
        $user->select('users.username');
        $user->select('users.userType');
        $user->select('users.status');
        $user->where('userID', $rec->userID);
        $cashier         = $user->get()->getRow();
        $data['cashier'] = $cashier;

        $user->select('users.firstName');
        $user->select('users.lastName');
        $user->select('users.username');
        $user->select('users.userType');
        $user->select('users.status');
        $user->where('userID', $rec->payment1Cashier);
        $payment1Cashier         = $user->get()->getRow();
        $data['payment1Cashier'] = $payment1Cashier;

        $user->select('users.firstName');
        $user->select('users.lastName');
        $user->select('users.username');
        $user->select('users.userType');
        $user->select('users.status');
        $user->where('userID', $rec->payment2Cashier);
        $payment2Cashier         = $user->get()->getRow();
        $data['payment2Cashier'] = $payment2Cashier;

        $data['title'] = 'Records';

        echo view('header', $data);
        echo view('pages/transactions/viewCustomer');
        echo view('footer');
    }

    public function resolve()
    {
        $transID = $this->request->getPost('transID');

        $builder = $this->db->table('transactions');

        $builder->set('status', 7);
        $builder->set('releasedBy', $this->current_user);
        $builder->set('dateReleased', date('Y-m-d H:i:s'));
        $builder->where('transID', $transID);
        $builder->update();

        $data = array(
            'status' => 1
        );

        return $this->response->setJSON($data);
    }

    public function totalLoads()
    {
        $transactions = new transactions();
        $transactions->select('transID, kiloQty, comforterLoad, totalLoads');
        $records = $transactions->findAll();

        foreach ($records as $rec) {
            $clothes = $rec['kiloQty'];
            $maxLoad = 7;
            $minLoad = 6;
            $load    = 0;

            if ($clothes == 0) {
                $load = 0;
            } else if ($clothes <= $maxLoad) {
                $load = 1;
            } else {
                $load = $clothes / $minLoad;
            }

            $totalLoads = ceil($load + $rec['comforterLoad']);
            $transactions->set('totalLoads', $totalLoads);
            $transactions->where('transID', $rec['transID']);
            if ($transactions->update()) {
                $result = array(
                    'transID'    => $rec['transID'],
                    'totalLoads' => $rec['totalLoads'],
                );
            }
        }
    }

    public function printlist()
    {
        $data = $this->data;

        $condition_fields = array(
            array(
                'variable'      => 'transID',
                'field'         => $this->table . '.transID',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'customer',
                'field'         => $this->table . '.customer',
                'default_value' => "",
                'operator'      => 'like_after'
            ),
            array(
                'variable'      => 'mobile',
                'field'         => $this->table . '.mobile',
                'default_value' => "",
                'operator'      => 'like_after'
            ),
            array(
                'variable'      => 'status',
                'field'         => $this->table . '.status',
                'default_value' => "",
                'operator'      => 'where'
            ),
            array(
                'variable'      => 'paymentMethod',
                'field'         => $this->table . '.paymentMethod',
                'default_value' => "",
                'operator'      => 'where'
            ),
            array(
                'variable'      => 'userID',
                'field'         =>  'users.userID',
                'default_value' => "",
                'operator'      => 'where'
            ),
        );

        // sorting fields
        $sorting_fields = array(
            'dateCreated' => 'desc',
        );

        //get the controller
        $controller = service('uri')->getSegment(1);

        foreach ($condition_fields as $key) {
            ${$key['variable']} = $this->session->get($controller . '_' . $key['variable']);
        }

        $startDate = $this->session->get($controller . '_startDate');
        $endDate   = $this->session->get($controller . '_endDate');
        $limit     = $this->session->get($controller . '_limit');
        $offset    = $this->session->get($controller . '_offset');
        $sortby    = $this->session->get($controller . '_sortby');
        $sortorder = $this->session->get($controller . '_sortorder');


        // select
        $builder = $this->db->table($this->table);
        $builder->select('transactions.qrCode');
        $builder->select('transactions.dateCreated');
        $builder->select('transactions.transID');
        $builder->select('transactions.customer');
        $builder->select('transactions.mobile');
        $builder->select('transactions.cash');
        $builder->select('transactions.gCash');
        $builder->select('transactions.comforterLoad');
        $builder->select('transactions.kiloQty');
        $builder->select('transactions.amountPaid');
        $builder->select('transactions.balance');
        $builder->select('transactions.totalAmount');
        $builder->select('transactions.status');
        $builder->select('transactions.payment1Cash');
        $builder->select('transactions.payment2Cash');
        $builder->select('transactions.payment1Gcash');
        $builder->select('transactions.payment2Gcash');
        $builder->select('transactions.paymentMethod');
        $builder->select('users.userID');
        $builder->select('users.username');
        $builder->join('users', 'users.userID = transactions.userID', 'left');

        // set conditions here
        foreach ($condition_fields as $key) {
            $operators = explode('_', $key['operator']);
            $operator  = $operators[0];
            // check if the operator is like
            if (count($operators) > 1) {
                // like operator
                if (trim(${$key['variable']}) != '' && $key['field'])
                    $builder->$operator($key['field'], ${$key['variable']}, $operators[1]);
            } else {
                if (trim(${$key['variable']}) != '' && $key['field'])
                    $builder->$operator($key['field'], ${$key['variable']});
            }
        }

        // date range
        $startDate = date('Y-m-d 00:00:00', strtotime($startDate));
        $endDate   = date('Y-m-d 23:59:59', strtotime($endDate));

        if ($startDate != '' && $startDate != '1970-01-01') {
            $builder->where("(transactions.dateCreated >= '$startDate' and transactions.dateCreated <= '$endDate')");
        }

        if ($sortby && $sortorder) {
            $builder->orderBy($sortby, $sortorder);

            if (!empty($sorting_fields)) {
                foreach ($sorting_fields as $fld => $s_order) {
                    if ($fld != $sortby) {
                        $builder->orderBy($fld, $s_order);
                    }
                }
            }
        } else {
            $ctr = 1;
            if (!empty($sorting_fields)) {
                foreach ($sorting_fields as $fld => $s_order) {
                    if ($ctr == 1) {
                        $sortby    = $fld;
                        $sortorder = $s_order;
                    }
                    $builder->orderBy($fld, $s_order);
                    $ctr++;
                }
            }
        }

        if ($limit) {
            if ($offset) {
                $builder->limit($limit, $offset);
            } else {
                $builder->limit($limit);
            }
        }

        // get
        $data['records'] = $records =  $builder->get()->getResult();

        // assigning variables
        $data['sortby']     = $sortby;
        $data['sortorder']  = $sortorder;
        $data['limit']      = $limit;
        $data['offset']     = $offset;

        echo view('pages/transactions/printlist', $data);
    }

    public function exportlist()
    {
        $condition_fields = array(
            array(
                'variable'      => 'transID',
                'field'         => $this->table . '.transID',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'customer',
                'field'         => $this->table . '.customer',
                'default_value' => "",
                'operator'      => 'like_after'
            ),
            array(
                'variable'      => 'mobile',
                'field'         => $this->table . '.mobile',
                'default_value' => "",
                'operator'      => 'like_after'
            ),
            array(
                'variable'      => 'status',
                'field'         => $this->table . '.status',
                'default_value' => "",
                'operator'      => 'where'
            ),
            array(
                'variable'      => 'paymentMethod',
                'field'         => $this->table . '.paymentMethod',
                'default_value' => "",
                'operator'      => 'where'
            ),
            array(
                'variable'      => 'userID',
                'field'         =>  'users.userID',
                'default_value' => "",
                'operator'      => 'where'
            ),
        );

        // sorting fields
        $sorting_fields = array(
            'dateCreated' => 'desc',
        );

        //get the controller
        $controller = service('uri')->getSegment(1);

        foreach ($condition_fields as $key) {
            ${$key['variable']} = $this->session->get($controller . '_' . $key['variable']);
        }

        $startDate = $this->session->get($controller . '_startDate');
        $endDate   = $this->session->get($controller . '_endDate');
        $limit     = $this->session->get($controller . '_limit');
        $offset    = $this->session->get($controller . '_offset');
        $sortby    = $this->session->get($controller . '_sortby');
        $sortorder = $this->session->get($controller . '_sortorder');

        // select
        $builder = $this->db->table($this->table);
        $builder->select('transactions.qrCode');
        $builder->select('transactions.dateCreated');
        $builder->select('transactions.transID');
        $builder->select('transactions.customer');
        $builder->select('transactions.mobile');
        $builder->select('transactions.cash');
        $builder->select('transactions.gCash');
        $builder->select('transactions.comforterLoad');
        $builder->select('transactions.kiloQty');
        $builder->select('transactions.amountPaid');
        $builder->select('transactions.balance');
        $builder->select('transactions.totalAmount');
        $builder->select('transactions.status');
        $builder->select('transactions.payment1Cash');
        $builder->select('transactions.payment2Cash');
        $builder->select('transactions.payment1Gcash');
        $builder->select('transactions.payment2Gcash');
        $builder->select('transactions.paymentMethod');
        $builder->select('users.userID');
        $builder->select('users.username');
        $builder->select('users.firstName');
        $builder->join('users', 'users.userID = transactions.userID', 'left');

        // set conditions here
        foreach ($condition_fields as $key) {
            $operators = explode('_', $key['operator']);
            $operator  = $operators[0];
            // check if the operator is like
            if (count($operators) > 1) {
                // like operator
                if (trim(${$key['variable']}) != '' && $key['field'])
                    $builder->$operator($key['field'], ${$key['variable']}, $operators[1]);
            } else {
                if (trim(${$key['variable']}) != '' && $key['field'])
                    $builder->$operator($key['field'], ${$key['variable']});
            }
        }

        // date range
        $startDate = date('Y-m-d 00:00:00', strtotime($startDate));
        $endDate   = date('Y-m-d 23:59:59', strtotime($endDate));

        if ($startDate != '' && $startDate != '1970-01-01') {
            $builder->where("(transactions.dateCreated >= '$startDate' and transactions.dateCreated <= '$endDate')");
        }

        if ($sortby && $sortorder) {
            $builder->orderBy($sortby, $sortorder);

            if (!empty($sorting_fields)) {
                foreach ($sorting_fields as $fld => $s_order) {
                    if ($fld != $sortby) {
                        $builder->orderBy($fld, $s_order);
                    }
                }
            }
        } else {
            $ctr = 1;
            if (!empty($sorting_fields)) {
                foreach ($sorting_fields as $fld => $s_order) {
                    if ($ctr == 1) {
                        $sortby    = $fld;
                        $sortorder = $s_order;
                    }
                    $builder->orderBy($fld, $s_order);
                    $ctr++;
                }
            }
        }

        if ($limit) {
            if ($offset) {
                $builder->limit($limit, $offset);
            } else {
                $builder->limit($limit);
            }
        }

        // assigning variables
        $data['sortby']     = $sortby;
        $data['sortorder']  = $sortorder;
        $data['limit']      = $limit;
        $data['offset']     = $offset;

        // get
        $records =  $builder->get()->getResult();

        $title = 'TRANSACTIONS';

        $filename    =   $title . '-' . date('mdYHi') . '.xlsx';
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A2', 'LABACHINE LAUNDRY LOUNGE');
        $sheet->setCellValue('A3',  $title);
        $sheet->mergeCells("A2:G2");
        $sheet->mergeCells("A3:G3");

        $sheet->getColumnDimension('A')->setWidth(12);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(10);

        $sheet->mergeCells('A2:G2');
        $sheet->mergeCells('A3:G3');

        $sheet->getStyle('A2:G2')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A3:G3')->getAlignment()->setHorizontal('center');

        $insideBorderStyle = [
            'borders' => [
                'inside' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => '000000'],
                ],
            ],
        ];

        $outsideBorderStyle = [
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => '000000'],
                ],
            ],
        ];

        $startCol      = 5;
        $rowStartCount = 6;
        $rowCount      = 6;

        if ($records) {
            $sheet->setCellValue('A' . $startCol, 'TRANSACTION DATE');
            $sheet->setCellValue('b' . $startCol, 'SERIES No.');
            $sheet->setCellValue('c' . $startCol, 'CUSTOMER');
            $sheet->setCellValue('d' . $startCol, 'MOBILE');
            $sheet->setCellValue('e' . $startCol, 'CLOTHES');
            $sheet->setCellValue('f' . $startCol, 'COMFORTER');
            $sheet->setCellValue('g' . $startCol, 'CASH');
            $sheet->setCellValue('h' . $startCol, 'GCASH');
            $sheet->setCellValue('i' . $startCol, 'AMOUNT PAID');
            $sheet->setCellValue('j' . $startCol, 'UNPAID');
            $sheet->setCellValue('k' . $startCol, 'AMOUNT DUE');
            $sheet->setCellValue('l' . $startCol, 'PAYMENT METHOD');
            $sheet->setCellValue('m' . $startCol, 'STATUS');
            $sheet->setCellValue('n' . $startCol, 'CASHIER');

            $statusList  = array(
                1 => 'Received',
                3 => 'Wash',
                4 => 'Dry',
                5 => 'Fold',
                6 => 'Ready',
                7 => 'Released',
                0 => 'Cancelled',
            );

            foreach ($records as $rec) {
                $status = "";
                if (in_array($rec->status, array_keys($statusList))) {
                    $status =  $statusList[$rec->status];
                }


                $totalCash  = $rec->cash + $rec->payment1Cash + $rec->payment2Cash;
                $totalGcash = $rec->gCash + $rec->payment1Gcash + $rec->payment2Gcash;

                $sheet->setCellValue('A' . $rowCount, date('d/m/Y',  strtotime($rec->dateCreated)));
                $sheet->setCellValue('B' . $rowCount, str_pad($rec->transID, 4, "0", STR_PAD_LEFT));
                $sheet->setCellValue('C' . $rowCount, $rec->customer);
                $sheet->setCellValue('D' . $rowCount, $rec->mobile);
                $sheet->setCellValue('E' . $rowCount, $rec->kiloQty);
                $sheet->setCellValue('F' . $rowCount, $rec->comforterLoad);
                $sheet->setCellValue('G' . $rowCount, $totalCash);
                $sheet->setCellValue('H' . $rowCount, $totalGcash);
                $sheet->setCellValue('I' . $rowCount, $rec->amountPaid);
                $sheet->setCellValue('j' . $rowCount, $rec->balance);
                $sheet->setCellValue('k' . $rowCount, $rec->totalAmount);
                $sheet->setCellValue('l' . $rowCount, $rec->paymentMethod);
                $sheet->setCellValue('M' . $rowCount, $status);
                $sheet->setCellValue('N' . $rowCount, $rec->firstName);
                $rowCount++;
            }
            $sheet->setCellValue('f' . $rowCount, 'Total');
            $sheet->setCellValue('g' . $rowCount, '=SUM(g' . $rowStartCount . ':' . 'g' . $rowCount . ')');
            $sheet->setCellValue('h' . $rowCount, '=SUM(h' . $rowStartCount . ':' . 'h' . $rowCount . ')');
            $sheet->setCellValue('i' . $rowCount, '=SUM(i' . $rowStartCount . ':' . 'i' . $rowCount . ')');
            $sheet->setCellValue('j' . $rowCount, '=SUM(j' . $rowStartCount . ':' . 'j' . $rowCount . ')');
            $sheet->setCellValue('k' . $rowCount, '=SUM(k' . $rowStartCount . ':' . 'k' . $rowCount . ')');

            $sheet->getStyle('A' . $startCol . ':' . 'N' . $rowCount)->applyFromArray($insideBorderStyle);
            $sheet->getStyle('A' . $startCol . ':' . 'N' . $rowCount)->applyFromArray($outsideBorderStyle);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        die;
    }
}
