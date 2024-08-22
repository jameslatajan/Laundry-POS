<?php

namespace App\Controllers;

use App\Models\transactions;
use App\Models\users;
use App\Models\Detailed_dsr;
use App\Models\expenses;
use App\Models\sales;
use stdClass;

class Home extends BaseController
{
    public function __construct()
    {
        $this->data['current_module'] = 'home';
        $this->data['current_menu']   = '';
    }

    public function index()
    {
        $builder =  $this->db->table('loginlogs');
        $builder->select('*');
        $builder->orderBy('date', 'desc');
        $builder->limit(1);
        $loginlogs = $builder->get()->getRow();

        if ($loginlogs->logs == 'login') {
            $this->session->set("loggedInUser", $loginlogs->userID);
            return redirect()->to('/');
        }

        return view('login');
    }

    public function dashboard()
    {
        $data = $this->data;

        $builder = $this->db->table('laundry_price');
        $builder->select('laundry_order');
        $builder->select('category');
        $builder->select('kilo');
        $builder->select('comforter');
        $builder->select('detergent');
        $builder->select('bleach');
        $builder->select('status');
        $builder->orderBy('laundry_order', 'asc');
        $laundry_price = $builder->get()->getResult();

        $builder = $this->db->table('laundry_price');
        $builder->select('laundry_order');
        $data['count'] = $builder->countAllResults();

        // $config->where('name', 'Daily sms');
        // $isSms = $config->get()->getRow();

        $sendSms = false;
        // if (trim($isSms->value) < date('Y-m-d')) {
        //     $sendSms = true;
        // }

        // $sendSms = true; // for testing
        $data['sendSms'] = $sendSms;
        $data['laundry_price'] = $laundry_price;

        echo view('header', $data);
        echo view('home');
        echo view('footer');
    }

    public function signin()
    {
        helper(['form']);
        $session = session();

        $rules = array(
            'username' => 'required|min_length[4]',
            'password' => 'required|min_length[4]',
        );

        $data = array(
            'success' => false,
            'msg'     => 'Username and Password Not Found',
            'url'     => '',
        );

        if ($this->validate($rules)) {
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');

            $users = $this->db->table('users');
            $users->select('userID');
            $users->select('username');
            $users->select('password');
            $users->select('userType');
            $users->select('lastLogout');
            $users->select('isDsr');
            $users->select('status');
            $users->where('username', $username);
            $result = $users->get()->getRow();

            $host      = gethostname();
            $ipAddress = gethostbyname($host);
            // $hostname  = gethostbyaddr($ipAddress);

            $loginlogs = array();
            if ($result && md5(strval($password)) == $result->password) {
                if (trim($result->userType)  == 'Admin') {
                    $loginlogs = array(
                        'host'      => $host,
                        // 'hostname'  => $ipAddress,
                        'userID'    => $result->userID,
                        'date'      => date('Y-m-d H:i:s'),
                        'operation' => 'success',
                        'logs'      => 'login',
                    );

                    $users = $this->db->table('users');
                    $users->set('lastLogin', date('Y-m-d H:i:s'));
                    $users->where('userID', $result->userID);
                    $users->update();

                    $session->set("loggedInUser", $result->userID);
                    $newdata = array(
                        'username' => $result->username,
                        'userType' => $result->userType,
                    );

                    $builder = $this->db->table('loginlogs');
                    $builder->insert($loginlogs);

                    $users =  $this->db->table('users');
                    $users->set('isDsr', 0);
                    $users->where('userID', $result->userID);
                    $users->update();

                    $session->set($newdata);

                    $data = array(
                        'success' => true,
                        'msg'     => '',
                        'url'     => base_url('dashboard'),
                    );
                } else if ($result->status == 0) {
                    $data = array(
                        'success' => false,
                        'msg'     => 'Inactive account',
                        'url'     => "",
                    );
                } else if ($result->isDsr == 1 && date('Y-m-d', strtotime($result->lastLogout)) == date('Y-m-d')) {
                    $data = array(
                        'success' => false,
                        'msg'     => 'Invalid login DSR already generated',
                        'url'     => "",
                    );
                } else {
                    $loginlogs = array(
                        'host'      => $host,
                        // 'hostname'  => $hostname,
                        'userID'    => $result->userID,
                        'date'      => date('Y-m-d H:i:s'),
                        'operation' => 'success',
                        'operation' => 'login',
                    );

                    $this->db->table('loginlogs')->insert($loginlogs);

                    $users = $this->db->table('users');
                    $users->set('lastLogin', date('Y-m-d H:i:s'));
                    $users->where('userID', $result->userID);
                    $users->update();

                    $session->set("loggedInUser", $result->userID);

                    $newdata = array(
                        'username' =>  $result->username,
                        'userType' =>  $result->userType,
                    );

                    $session->set($newdata);

                    $users->set('isDsr', 0);
                    $users->where('userID', $result->userID);
                    $users->update();

                    $data = array(
                        'success' => true,
                        'msg'     => '',
                        'url'     => base_url('dashboard'),
                    );
                }
            }
        }

        return $this->response->setJSON($data);
    }

    public function logout()
    {
        $users     = new users();
        $salesDate = date('Y-m-d');
        $userID    = session()->get('loggedInUser');

        $users->select('*');
        $users->where('userID', $userID);
        $user = $users->get()->getRow();

        $dsr  = $this->dsr_view($salesDate, $userID);

        $host      = gethostname();
        $ipAddress = gethostbyname($host);
        // $hostname  = gethostbyaddr($ipAddress);

        $loginlogs = array(
            'host'      => $host,
            'hostname'  => $ipAddress,
            'userID'    => $userID,
            'date'      => date('Y-m-d H:i:s'),
            'operation' => 'success',
            'logs'      => 'logout',
        );

        $data = array();
        if (trim($user->userType)  == 'Admin') {
            $data = array(
                'data'     => $dsr,
                'islogout' => true,
                'msg'      => 'Are you sure you want to logout?',
                'url'      => site_url(),
            );

            $this->db->table('loginlogs')->insert($loginlogs);

            $this->db->table('users')
                ->set('lastLogout', date('Y-m-d H:i:s'))
                ->where('userID', $userID)
                ->update();

            session()->destroy();
        } else if ($dsr && date('Y-m-d', strtotime($user->lastLogin)) == date('Y-m-d') && $user->isDsr == 0) {
            $data = array(
                'data'     => $dsr,
                'islogout' => false,
                'msg'      => 'You need to generate daily sales reports.',
                'url'      => site_url("dsr_generate"),
            );
        } else {
            $data = array(
                'data'     => $dsr,
                'islogout' => true,
                'msg'      => 'Are you sure you want to logout?',
                'url'      => site_url(),
            );

            $this->db->table('loginlogs')
                ->insert($loginlogs);

            session()->destroy();
        }

        return $this->response->setJSON($data);
    }

    public function getTransID()
    {
        $transID = $this->request->getPost('transID');

        $transactions = new transactions();
        $transactions->select('transID, qrCode');
        $transactions->where('transID', $transID);
        $transactions->limit(1);
        $getTransID = $transactions->get()->getRow();

        // echo $this->db->getLastQuery();
        // die;
        if ($getTransID) {
            $padTrans = str_pad($getTransID->transID, 4, "0", STR_PAD_LEFT);

            if ($transID === $padTrans) {
                $data = [
                    'success'  => true,
                    'url'      => site_url('transaction/view/') . $getTransID->qrCode,
                    'transPad' => $padTrans,
                    'transID'  => $transID,
                ];
            } else {
                $data = [
                    'success' => false,
                    'msg'     => 'Invalid series no.',
                ];
            }
        } else {
            $data = [
                'success' => false,
                'msg'     => 'Invalid series no.',
            ];
        }

        return $this->response->setJSON($data);
    }


    public function getRecord($dateCreated, $userID)
    {
        $transactions = new transactions();
        $transactions->where('userID', $userID);
        $transactions->like('dateCreated', $dateCreated, 'both');
        $count = $transactions->countAllResults();
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function dsr_view($sales_date, $userID)
    {
        $user  = new users();
        $daily = new Detailed_dsr();

        $setdsr['dsr_true'] = false;
        if ($sales_date != "" && $userID != "") {
            $format_sales_date = date('Y-m-d', strtotime($sales_date));

            $user->where('userID', $userID);
            $setdsr['cashier'] = $cashier = $user->get()->getRow();

            $setdsr['sales']      = $sales      = $this->getSales($format_sales_date, $userID);
            $setdsr['collection'] = $collection = $this->getCollection($format_sales_date, $userID);
            $setdsr['item']       = $item       = $this->getItem($format_sales_date, $userID);
            $setdsr['expenses']   = $expenses   = $this->getExpenses($format_sales_date, $userID);
            $setdsr['canceled']   = $canceled   = $this->get_canceled($format_sales_date, $userID);

            // vars
            $ds_cash   = 0; //all cash
            $ds_gcash  = 0; //all gcash
            $ds_unpaid = 0;
            $ds_total  = 0;
            if ($sales) {
                $ds_cash   = $sales->cash;
                $ds_gcash  = $sales->gcash;
                $ds_unpaid = $sales->unpaid;
                $ds_total  = floatval($sales->cash) + floatval($sales->gcash) + floatval($sales->unpaid);
            }

            $col_cash  = 0;
            $col_gcash = 0;
            if ($collection) {
                foreach ($collection['cash'] as $date => $details) {
                    $temp = $details['payment1Total'] + $details['payment2Total'];
                    $col_cash += floatval($temp);
                }

                foreach ($collection['gcash'] as $date => $details) {
                    $temp = $details['payment1Total'] + $details['payment2Total'];
                    $col_gcash += floatval($temp);
                }
            }

            $item_cash  = 0;
            $item_gcash = 0;
            if ($item) {
                $item_cash  = floatval($item->cash->total);
                $item_gcash = floatval($item->gcash->total);
            }

            $total_expenses = 0;
            if ($expenses) {
                foreach ($expenses as $exp) {
                    $total_expenses += floatval($exp->amount);
                }
            }

            $col_total    = $col_cash + $col_gcash;
            $item_total   = $item_cash + $item_gcash;
            $col_total    = $col_cash + $col_gcash;
            $total_cash   = $ds_cash + $item_cash + $col_cash - $total_expenses;
            $total_gcash  = $ds_gcash + $item_gcash + $col_gcash;
            $date_created = date('Y-m-d H:i:s');

            $setdsr = array(
                'ds_cash'        => $ds_cash,
                'ds_gcash'       => $ds_gcash,
                'ds_unpaid'      => $ds_unpaid,
                'ds_total'       => $ds_total,
                'col_cash'       => $col_cash,
                'col_gcash'      => $col_gcash,
                'col_total'      => $col_total,
                'item_cash'      => $item_cash,
                'item_gcash'     => $item_gcash,
                'item_total'     => $item_total,
                'total_cash'     => $total_cash,
                'total_gcash'    => $total_gcash,
                'total_expenses' => $total_expenses,
                // 'expected_cash'  => $total_cash,
                // 'sales_date'     => $sales_date,
                // 'date_created'   => $date_created,
                'userID'         => $userID,
            );

            $setdsr['collection']  = $collection;
            $setdsr['expenses']    = $expenses;
            // $setdsr['canceled']    = $canceled;
            $setdsr['dsr_true']    = true;

            // $daily->select('remittance, variance');
            // $daily->like('sales_date', $format_sales_date);
            // $daily->where('userID', $userID);
            // $getdsr = $daily->get()->getRow();

            // if ($getdsr) {
            //     $setdsr['remittance'] = $getdsr->remittance;
            //     $setdsr['variance']   = $getdsr->variance;
            //     // $setdsr['variance']   = $total_cash - $getdsr->remittance;
            // }

            // }
            $setdsr['cashier_name'] = $cashier->username;
        }

        $user->select('username, userID');
        $user->where('status !=', 0);
        $user->orderBy('username', 'asc');
        $setdsr['cashier_id'] = $userID;
        $setdsr['sales_date'] = $sales_date;

        return $setdsr;
    }

    public function getExpenses($dateCreated, $userID)
    {
        $expenses = new expenses();
        $expenses->where('createdBy', $userID);
        $expenses->like('expDate', $dateCreated);
        $count = $expenses->countAllResults();

        $result = new stdClass();
        if ($count > 0) {
            $expenses->select('particular');
            $expenses->select('amount');
            $expenses->like('dateCreated', $dateCreated);
            $expenses->where('createdBy', $userID);
            $expenses->where('status !=', 0);
            $result = $expenses->get()->getResult();
            return $result;
        } else {
            return 0;
        }
    }

    public function getItem($dateCreated, $userID)
    {
        $dateFormat = date('Y-m-d', strtotime($dateCreated));

        $sale_header = new sales();
        $sale_header->where('userID', $userID);
        $sale_header->like('salesDate', $dateFormat);
        $count = $sale_header->countAllResults();

        $result = new stdClass();
        if ($count > 0) {
            $sale_header->select('SUM(amount) as total');
            $sale_header->like('salesDate', $dateFormat);
            $sale_header->where('cashier', $userID);
            $sale_header->where('paymentMethod', 'Cash');
            $result->cash = $sale_header->get()->getRow();

            $sale_header->select('SUM(amount) as total');
            $sale_header->like('salesDate', $dateFormat);
            $sale_header->where('cashier', $userID);
            $sale_header->where('paymentMethod', 'Gcash');
            $result->gcash = $sale_header->get()->getRow();

            return $result;
        } else {
            return 0;
        }
    }

    public function getCollection($salesDate, $userID)
    {
        $transactions = new transactions();
        $transactions->where('userID', $userID);
        $transactions->like('dateCreated', $salesDate);
        $count = $transactions->countAllResults();

        $result = array();
        if ($count > 0) {
            $transactions->select('DATE(dateCreated) as dateCreated, payment1Date, payment1Cashier, SUM(payment1Cash) as total');
            $transactions->notLike('dateCreated', $salesDate);
            $transactions->like('payment1Date', $salesDate);
            $transactions->like('payment1Cashier', $userID);
            $transactions->where('status !=', 0);
            $transactions->where('payment1Cash >', 0);
            $transactions->groupBy('DATE(dateCreated)');
            $payment1Cash = $transactions->get()->getResult();

            $transactions->select('DATE(dateCreated) as dateCreated, payment2Date, payment2Cashier, SUM(payment2Cash) as total');
            $transactions->notLike('dateCreated', $salesDate);
            $transactions->like('payment2Date', $salesDate);
            $transactions->like('payment2Cashier', $userID);
            $transactions->where('status !=', 0);
            $transactions->where('payment2Cash >', 0);
            $transactions->groupBy('DATE(dateCreated)');
            $payment2Cash = $transactions->get()->getResult();

            $cash = array();
            // Merge data based on dateCreated
            foreach ($payment1Cash as $payment1) {
                $dateCreated = $payment1->dateCreated;

                if (!isset($cash[$dateCreated])) {
                    $cash[$dateCreated] = array(
                        'dateCreated'   => $dateCreated,
                        'payment1Total' => $payment1->total,
                        'payment2Total' => 0, // Initialize payment2Total to 0
                    );
                } else {
                    $cash[$dateCreated]['payment1Total'] = $payment1->total;
                }
            }

            foreach ($payment2Cash as $payment2) {
                $dateCreated = $payment2->dateCreated;

                if (!isset($cash[$dateCreated])) {
                    $cash[$dateCreated] = array(
                        'dateCreated'   => $dateCreated,
                        'payment1Total' => 0, // Initialize payment1Total to 0
                        'payment2Total' => $payment2->total,
                    );
                } else {
                    $cash[$dateCreated]['payment2Total'] = $payment2->total;
                }
            }

            $transactions->select('DATE(dateCreated) as dateCreated, payment1Date, payment1Cashier, SUM(payment1GCash) as total');
            $transactions->notLike('dateCreated', $salesDate);
            $transactions->like('payment1Date', $salesDate);
            $transactions->like('payment1Cashier', $userID);
            $transactions->where('payment1GCash >', 0);
            $transactions->where('status !=', 0);
            $transactions->groupBy('DATE(dateCreated)');
            $payment1GCash = $transactions->get()->getResult();

            $transactions->select('DATE(dateCreated) as dateCreated, payment2Date, payment2Cashier, SUM(payment2GCash) as total');
            $transactions->notLike('dateCreated', $salesDate);
            $transactions->like('payment2Date', $salesDate);
            $transactions->like('payment2Cashier', $userID);
            $transactions->where('payment2GCash >', 0);
            $transactions->where('status !=', 0);
            $transactions->groupBy('DATE(dateCreated)');
            $payment2GCash = $transactions->get()->getResult();

            $gcash = array();
            // Merge data based on dateCreated
            foreach ($payment1GCash as $payment1) {
                $dateCreated = $payment1->dateCreated;
                if (!isset($gcash[$dateCreated])) {
                    $gcash[$dateCreated] = array(
                        'dateCreated'   => $dateCreated,
                        'payment1Total' => $payment1->total,
                        'payment2Total' => 0, // Initialize payment2Total to 0
                    );
                } else {
                    $gcash[$dateCreated]['payment1Total'] = $payment1->total;
                }
            }

            foreach ($payment2GCash as $payment2) {
                $dateCreated = $payment2->dateCreated;

                if (!isset($gcash[$dateCreated])) {
                    $gcash[$dateCreated] = array(
                        'dateCreated'   => $dateCreated,
                        'payment1Total' => 0, // Initialize payment1Total to 0
                        'payment2Total' => $payment2->total,
                    );
                } else {
                    $gcash[$dateCreated]['payment2Total'] = $payment2->total;
                }
            }

            $result = array(
                'cash'  => $cash,
                'gcash' => $gcash,
            );

            return $result;
        } else {
            return 0;
        }
    }

    public function get_canceled($saleDate, $userID)
    {
        $transactions = new transactions();
        $result       = new stdClass();
        $transactions->select('SUM(totalAmount) as total');
        $transactions->where('status', 0);
        $transactions->like('dateCanceled', $saleDate);
        $transactions->where('canceledBy', $userID);
        $result = $transactions->get()->getRow();

        $transactions->select('totalAmount');
        $transactions->like('dateCanceled', $saleDate);
        $transactions->where('canceledBy', $userID);
        $result->count = $transactions->countAllResults();

        if ($result->count > 0) {
            return $result;
        } else {
            return 0;
        }
    }

    public function getSales($salesDate, $userID)
    {
        $transactions = new transactions();
        $transactions->where('userID', $userID);
        $transactions->like('dateCreated', $salesDate);
        $count = $transactions->countAllResults();

        $result = new stdClass();
        if ($count > 0) {
            $transactions->selectSum('cash');
            $transactions->where('userID', $userID);
            $transactions->where('DATE(dateCreated)', $salesDate);
            $transactions->where('status !=', 0);
            $cash = $transactions->get()->getRow();

            $transactions->selectSum('gCash');
            $transactions->where('userID', $userID);
            $transactions->where('DATE(dateCreated)', $salesDate);
            $transactions->where('status !=', 0);
            $gcash = $transactions->get()->getRow();

            $transactions->selectSum('payment1Cash');
            $transactions->where('payment1Cashier', $userID);
            $transactions->where('DATE(dateCreated)', $salesDate);
            $transactions->where('DATE(payment1Date)', $salesDate);
            $transactions->where('status !=', 0);
            $payment1Cash = $transactions->get()->getRow();

            $transactions->selectSum('payment1GCash');
            $transactions->where('payment1Cashier', $userID);
            $transactions->where('DATE(dateCreated)', $salesDate);
            $transactions->where('DATE(payment1Date)', $salesDate);
            $transactions->where('status !=', 0);
            $payment1Gcash = $transactions->get()->getRow();

            $transactions->selectSum('payment2Cash');
            $transactions->where('payment2Cashier', $userID);
            $transactions->where('DATE(dateCreated)', $salesDate);
            $transactions->where('DATE(payment2Date)', $salesDate);
            $transactions->where('status !=', 0);
            $payment2Cash = $transactions->get()->getRow();

            $transactions->selectSum('payment2GCash');
            $transactions->where('payment2Cashier', $userID);
            $transactions->where('DATE(dateCreated)', $salesDate);
            $transactions->where('DATE(payment2Date)', $salesDate);
            $transactions->where('status !=', 0);
            $payment2Gcash = $transactions->get()->getRow();

            $transactions->selectSum('balance');
            $transactions->where('userID', $userID);
            $transactions->where('DATE(dateCreated)', $salesDate);
            $transactions->where('status !=', 0);
            $result->unpaid = $transactions->get()->getRow()->balance;

            $result->cash  = floatval($cash->cash) + floatval($payment1Cash->payment1Cash) + floatval($payment2Cash->payment2Cash);
            $result->gcash = floatval($gcash->gCash) + floatval($payment1Gcash->payment1GCash) + floatval($payment2Gcash->payment2GCash);
            $result->total = floatval($result->cash) + floatval($result->gcash);

            return $result;
        } else {
            return 0;
        }
    }
}
