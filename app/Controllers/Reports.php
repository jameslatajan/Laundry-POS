<?php

namespace App\Controllers;

use App\Models\Detailed_dsr;
use App\Models\expenses;
use App\Models\sales;
use App\Models\transactions;
use App\Models\users;
use stdClass;

class Reports extends BaseController
{
    protected $data;
    protected $module;
    protected $table;
    protected $pfield;
    protected $controller_page;

    public function __construct()
    {
        $this->data['module_title']   = 'REPORTS';
        $this->data['module_desc']    = 'Description about transactions';
        $this->data['current_module'] = 'reports';
        $this->data['current_menu']   = '';
    }

    public function dsrAdmin()
    {
        $data = $this->data;

        $condition_fields = array(
            array(
                'variable'      => 'sales_date',
                'field'         => $this->table . '.sales_date',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'userID',
                'field'         => $this->table . '.userID',
                'default_value' => "",
                'operator'      => 'like_after'
            ),
        );

        //get the controller
        $controller = service('uri')->getSegment(1);

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
                break;
            case 2:
                foreach ($condition_fields as $key) {
                    ${$key['variable']} = $this->session->get($controller . '_' . $key['variable']);
                }
                break;
            default:
                foreach ($condition_fields as $key) {
                    ${$key['variable']} = $key['default_value'];
                }
        }
        //end source of filtering

        // set session variables
        foreach ($condition_fields as $key) {
            $this->session->set($controller . '_' . $key['variable'], ${$key['variable']});
        }

        //get the controller
        $controller = service('uri')->getSegment(1);
        $this->session->set($controller . '_sales_date', $sales_date);

        $data['cashier_id']   = "";
        $data['sales_date']   = "";

        $rec     = array();
        $cashier = array();
        if ($sales_date && $userID) {
            $builder = $this->db->table('detailed_dsr')
                ->select('*')
                ->where('userID', $userID)
                ->like('sales_date', $sales_date);
            $rec = $builder->get()->getRow();

            $builder2 = $this->db->table('users')
                ->select('username, firstName')
                ->where('userID', $userID);
            $cashier = $builder2->get()->getRow();
        }

        $builder3 = $this->db->table('users')
            ->select('userID, username, firstName')
            ->orderBy('username', 'asc');
        $cashiers = $builder3->get()->getResult();


        $data['dsr_admin']    = 'active';
        $data['rec']          = $rec;
        $data['curr_cashier'] = $cashier;
        $data['cashiers']     = $cashiers;
        $data['cashier_id']   = $userID;
        $data['sales_date']   = $sales_date;

        $data['title'] = 'DSR Admin';

        echo view('header', $data);
        echo view('pages/reports/dsr');
        echo view('footer');
    }

    public function dsrAdminPrint($userID, $sales_date)
    {
        $rec     = array();
        $cashier = array();
        if ($sales_date && $userID) {
            $builder = $this->db->table('detailed_dsr')
                ->select('*')
                ->where('userID', $userID)
                ->like('sales_date', $sales_date);
            $rec = $builder->get()->getRow();

            $builder2 = $this->db->table('users')
                ->select('username')
                ->where('userID', $userID);
            $cashier = $builder2->get()->getRow();
        }

        $builder3 = $this->db->table('users')
            ->select('userID, username')
            ->orderBy('username', 'asc');
        $cashiers = $builder3->get()->getResult();

        $data['dsr_admin']    = 'active';
        $data['data']         = $this->header();
        $data['rec']          = $rec;
        $data['curr_cashier'] = $cashier;
        $data['cashiers']     = $cashiers;
        $data['cashier_id']   = $userID;

        return view('pages/reports/dsr_print', $data);
    }

    public function settle()
    {
        $varSettledAmt = $this->request->getPost('varSettledAmt');
        $dsrID         = $this->request->getPost('dsrID');

        $data = array(
            'dateSettled'   => date('Y-m-d h:i:s'),
            'varSettledAmt' => $varSettledAmt
        );

        $builder = $this->db->table('detailed_dsr');
        $builder->set($data);
        $builder->where('dsrID', $dsrID);
        $builder->update();

        return redirect()->to('dsr_admin');
    }

    public function getDsr($salesDate)
    {
        $dsr = new Detailed_dsr();

        $dsr->select('username, variance, dsrID, sales_date');
        $dsr->join('users', 'users.userID = detailed_dsr.userID');
        $dsr->where('DATE(sales_date)', $salesDate);
        $dsr->where('varSettledAmt <=', 0);
        $dsr->orderBy('sales_date', 'asc');
        $data = $dsr->findAll();

        return $this->response->setJSON($data);
    }

    public function dsrClient()
    {
        $data = $this->data;

        $user  = new users();
        $daily = new Detailed_dsr();

        $userID = session()->get('loggedInUser');

        $daily->where('userID', $userID);
        $daily->like('sales_date', date('Y-m-d'));
        $count = $daily->countAllResults();

        $userBuilder = $this->db->table('users');
        $userBuilder->where('userID', $userID);
        $userData =  $userBuilder->get()->getRow();

        $data['userID']  = $userID;
        $data['cashier'] = $user->where('userID', $userID)->first();
        $data['dsr']     = 'active';
        $data['data']    = $this->header();

        $data['isDsr'] = $count;
        
        $data['title'] = 'Daily Sales Report';

        if ($userData->isDsr) {
            echo view('header', $data);
            echo view('pages/reports/dsr_already_printed');
            echo view('footer');
        } else {
            echo view('header', $data);
            echo view('pages/reports/dsr2');
            echo view('footer');
        }
    }

    public function dsrClientPrint($remittance)
    {
        $daily = new Detailed_dsr();
        $user  = new users();

        $userID     = session()->get('loggedInUser');
        $sales_date = date('Y-m-d H:i:s');
        // $sales_date        = '2023-10-16 10:42';
        $format_sales_date = date('Y-m-d');

        $shift      = 1;
        $sales_time = date('H:i', strtotime($sales_date));
        // end times for the range
        $end_time = "20:00";  // 8 PM
        if ($sales_time >= $end_time) {
            // The sales time is greater than or equal to 1 PM and less than or equal to 9 PM
            // Your code for this case goes here
            $shift = 2;
        }
        // $userID     = 6;
        // $salesDate  = '2023-09-08';
        $cashier              = $user->where('userID', $userID)->get()->getRow();
        $setdsr['sales']      = $sales      = $this->getSales($format_sales_date, $userID);
        $setdsr['collection'] = $collection = $this->getCollection($format_sales_date, $userID);
        $setdsr['item']       = $item       = $this->getItem($format_sales_date, $userID);
        $setdsr['expenses']   = $expenses   = $this->getExpenses($format_sales_date, $userID);
        $setdsr['canceled']   = $canceled   = $this->get_canceled($format_sales_date, $userID);
        $setdsr['unpaid']     = $unpaid     = $this->dsr_unpaid();

        // vars
        $ds_cash   = 0;  //all cash
        $ds_gcash  = 0;  //all gcash
        $ds_unpaid = 0;
        $ds_total  = 0;
        $dsrSms    = 0;
        if ($sales) {
            if ($sales->cash) {
                $ds_cash   = $sales->cash;
            }

            if ($sales->gcash) {
                $ds_gcash  = $sales->gcash;
            }

            if ($sales->unpaid) {
                $ds_unpaid = $sales->unpaid;
            }

            $ds_total  = floatval($sales->cash) + floatval($sales->gcash) + floatval($sales->unpaid);

            $message  = "";
            $message .= "Sale Date: " . date('m/d/Y', strtotime($format_sales_date));
            $message .= "\n";
            $message .= "Cash: " . number_format($sales->cash, 2);
            $message .= "\n";
            $message .= "GCash: " . number_format($sales->gcash, 2);
            $message .= "\n";
            $message .= "Unpaid: " . number_format($sales->unpaid, 2);
            $message .= "\n";
            $message .= "Gross Sales: " . number_format($ds_total, 2);

            $config = $this->db->table('config');

            $config->where('name', 'Primary No.');
            $primary = $config->get()->getRow();

            $config->where('name', 'Secondary No.');
            $secondary = $config->get()->getRow();

            $this->gw_send_smss2($primary->value, $message);
            $this->gw_send_smss2($secondary->value, $message);
        }

        $col_cash  = 0;
        $col_gcash = 0;
        if ($collection) {
            foreach ($collection['cash'] as $date => $details) {
                $temp      = $details['payment1Total'] + $details['payment2Total'];
                $col_cash += floatval($temp);
            }

            foreach ($collection['gcash'] as $date => $details) {
                $temp       = $details['payment1Total'] + $details['payment2Total'];
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

        $total_unpaid = 0;
        if ($unpaid) {
            foreach ($unpaid as $un) {
                $total_unpaid += floatval($un->balance);
            }
        }

        $col_total    = $col_cash + $col_gcash;
        $item_total   = $item_cash + $item_gcash;
        $total_cash   = $ds_cash + $item_cash + $col_cash - $total_expenses;
        $total_gcash  = $ds_gcash + $item_gcash + $col_gcash;
        $variance     = $remittance - $total_cash;
        $date_created = date('Y-m-d H:i:s');
        $setdsr       = array(
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
            'expected_cash'  => $total_cash,
            'remittance'     => $remittance,
            'variance'       => $variance,
            'sales_date'     => $sales_date,
            'date_created'   => $date_created,
            'shift'          => $shift,
            'userID'         => $userID,
        );

        $daily->where('sales_date', $sales_date);
        $daily->where('userID', $userID);
        $count = $daily->countAllResults();

        if ($count > 0) {
            $daily->set($setdsr);
            $daily->where('sales_date', $sales_date);
            $daily->where('userID', $userID);
            $daily->update();
        } else {
            $daily->insert($setdsr);
            $inserted = $daily->getInsertID();
        }

        $this->db->table('users')
            ->set('isDsr', 1)
            ->where('userID', $userID)
            ->update();

        $setdsr['collection'] = $collection;
        $setdsr['expenses']   = $expenses;
        $setdsr['canceled']   = $canceled;
        $setdsr['unpaid']     = $unpaid;
        $setdsr['cashier']    = $cashier->username;

        return view('pages/reports/dsr_print2', $setdsr);
    }

    public function gw_send_smss2($sms_to, $sms_msg)
    {
        $response          = new \stdClass;
        $response->success = false;
        $response->message = "";

        if ($this->sms_status()) {
            // Compare the current time to the target time
            if (substr($sms_to, 0, 2) == '09' && substr($sms_to, 0, 5) != '09000' && strlen($sms_to) == 11 && trim($sms_to) != '09090000000') {
                $api_user = "APIYUJI9KGFE5";
                $api_pass = "APIYUJI9KGFE5YUJI9";

                $query_string  = "api.aspx?apiusername=" . $api_user . "&apipassword=" . $api_pass;
                $query_string .= "&senderid=" . rawurlencode('LABACHINE') . "&mobileno=" . rawurlencode($sms_to);
                $query_string .= "&message=" . rawurlencode(stripslashes($sms_msg)) . "&languagetype=1";
                $url           = "http://gateway.onewaysms.ph:10001/" . $query_string;

                $fileContent = @file_get_contents($url);
                // $fileContent = true; //testing

                // Check if the file contents were successfully loaded
                if ($fileContent) {
                    // Now you can use $fileContent as needed
                    // $fd = @implode('', file($url));
                    // // $fd = 1; //testing

                    // if ($fd > 0) {
                    //     $response->success = true;
                    //     $response->message = "Message sent";
                    // } else {
                    //     $response->message = "Please refer to API on Error : " . $fd;
                    // }

                    $response->success = true;
                    $response->message = "Message sent";
                } else {
                    $response->message = "No contact with gateway";
                }
            } else {
                $response->message = "Invalid mobile number";
            }
        } else {
            $response->message = "Sending SMS is off";
        }


        return $response;
    }

    public function send_sms_globe($phone, $message)
    {
        //globe sms
        $response = new \stdClass;

        $response->success = false;
        $response->message = "";

        if ($this->sms_status()) {
            if (substr($phone, 0, 2) == '09' && substr($phone, 0, 5) != '09000' && strlen($phone) == 11 && substr($phone, 0, 11) != '09090000000') {
                $post_data = array(
                    "app_key"        => "6qtdk15lPG9BoAdP",
                    "app_secret"     => "0RryVFabWNUOUio9qrWghpphXCfnh3u6",
                    "msisdn"         => $phone,
                    "content"        => $message,
                    "shortcode_mask" => "LABACHINE",
                );

                $post_data = json_encode($post_data);
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, "https://api.m360.com.ph/v3/api/broadcast");
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                $data = json_decode(curl_exec($curl));

                if ($data) {
                    $code = $data->code;
                    $name = $data->name;

                    // $code = 201; for testing
                    // $name = 201; for testing

                    if ($code == 201) {
                        $response->success = true;
                        $response->message = "Message sent";
                    } else {
                        $response->message = "Please refer to API on Error: " . $name;
                    }
                } else {
                    $response->message = "No contact with gateway";
                }
            } else {
                $response->message = "Message not sent invalid mobile number";
            }
        } else {
            $response->message = "Sending SMS is off";
        }

        return $response;
    }

    public function dsr_unpaid()
    {
        $transactions = new transactions();
        $transactions->select('transID, customer, dateCreated, mobile, amountPaid, totalAmount, balance');
        $transactions->where('balance >', 0);
        $transactions->where('status !=', 0);
        $transactions->where('status !=', 7);
        $transactions->orderBy('dateCreated', 'desc');
        $data = $transactions->get()->getResult();

        return $data;
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
            $expenses->like('dateCreated', $dateCreated, 'after');
            $expenses->where('createdBy', $userID);
            $expenses->where('status !=', 0);
            $result = $expenses->get()->getResult();
            return $result;
        } else {
            return 0;
        }
    }

    public function getRecord($dateCreated, $userID)
    {
        $transactions = new transactions();
        $transactions->where('userID', $userID);
        $transactions->like('dateCreated', $dateCreated, 'after');
        $count = $transactions->countAllResults();
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getItem($dateCreated, $userID)
    {
        $dateFormat = date('Y-m-d', strtotime($dateCreated));

        $sale_header = new sales();
        $sale_header->where('userID', $userID);
        $sale_header->like('salesDate', $dateFormat, 'after');
        $count = $sale_header->countAllResults();

        $result = new stdClass();
        if ($count > 0) {
            $sale_header->select('SUM(amount) as total');
            $sale_header->like('salesDate', $dateFormat, 'after');
            $sale_header->where('userID', $userID);
            $sale_header->where('paymentMethod', 'Cash');
            $result->cash = $sale_header->get()->getRow();

            $sale_header->select('SUM(amount) as total');
            $sale_header->like('salesDate', $dateFormat, 'after');
            $sale_header->where('userID', $userID);
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

        $result = array();
        $transactions->select('dateCreated, payment1Date, payment1Cashier, SUM(payment1Cash) as total');
        $transactions->where('dateCreated <', $salesDate);
        $transactions->like('payment1Date', $salesDate, 'after');
        $transactions->where('payment1Cashier', $userID);
        $transactions->where('status !=', 0);
        $transactions->groupBy('DATE(dateCreated)');
        $payment1Cash = $transactions->get()->getResult();

        $transactions->select('DATE(dateCreated) as dateCreated, payment2Date, payment2Cashier, SUM(payment2Cash) as total');
        $transactions->where('dateCreated <', $salesDate);
        $transactions->like('payment2Date', $salesDate, 'after');
        $transactions->where('payment2Cashier', $userID);
        $transactions->where('status !=', 0);
        $transactions->groupBy('DATE(dateCreated)');
        $payment2Cash = $transactions->get()->getResult();

        $cash = array();
        // Merge data based on dateCreated
        foreach ($payment1Cash as $payment1) {
            $total = $payment1->total;
            if ($total) {
                $cash[$payment1->dateCreated] = array(
                    'dateCreated'   => $payment1->dateCreated,
                    'payment1Total' => $total,
                    'payment2Total' => 0,                  // Initialize payment2Total to 0
                );
            }
        }

        foreach ($payment2Cash as $payment2) {
            $total = $payment2->total;
            if ($total) {
                $cash[$payment2->dateCreated] = array(
                    'dateCreated'   => $payment2->dateCreated,
                    'payment1Total' => 0,                  // Initialize payment1Total to 0
                    'payment2Total' => $total,
                );
            }
        }

        $transactions->select('DATE(dateCreated) as dateCreated, payment1Date, payment1Cashier, SUM(payment1GCash) as total');
        $transactions->where('dateCreated <', $salesDate);
        $transactions->like('payment1Date', $salesDate, 'after');
        $transactions->where('payment1Cashier', $userID);
        $transactions->where('status !=', 0);
        $transactions->groupBy('DATE(dateCreated)');
        $payment1GCash = $transactions->get()->getResult();

        $transactions->select('DATE(dateCreated) as dateCreated, payment2Date, payment2Cashier, SUM(payment2GCash) as total');
        $transactions->where('dateCreated <', $salesDate);
        $transactions->like('payment2Date', $salesDate, 'after');
        $transactions->where('payment2Cashier', $userID);
        $transactions->where('status !=', 0);
        $transactions->groupBy('DATE(dateCreated)');
        $payment2GCash = $transactions->get()->getResult();

        $gcash = array();
        // Merge data based on dateCreated
        foreach ($payment1GCash as $payment1) {
            $total = $payment1->total;
            if ($total) {
                $gcash[$payment1->dateCreated] = array(
                    'dateCreated'   => $payment1->dateCreated,
                    'payment1Total' =>  $total,
                    'payment2Total' => 0,                  // Initialize payment2Total to 0
                );
            }
        }

        foreach ($payment2GCash as $payment2) {
            $total = $payment2->total;
            if ($total) {
                $gcash[$payment2->dateCreated] = array(
                    'dateCreated'   => $payment2->dateCreated,
                    'payment1Total' => 0,                  // Initialize payment1Total to 0
                    'payment2Total' => $total,
                );
            }
        }

        $result = array(
            'cash'  => $cash,
            'gcash' => $gcash,
        );

        return $result;
    }

    public function getSales($salesDate, $userID)
    {
        $transactions = new transactions();
        $transactions->where('userID', $userID);
        $transactions->like('dateCreated', $salesDate, 'after');
        $count = $transactions->countAllResults();

        $result = new stdClass();
        if ($count > 0) {
            $transactions->selectSum('cash');
            $transactions->where('userID', $userID);
            $transactions->like('dateCreated', $salesDate, 'after');
            $transactions->where('status !=', 0);
            $cash = $transactions->get()->getRow();

            $transactions->selectSum('gCash');
            $transactions->where('userID', $userID);
            $transactions->like('dateCreated', $salesDate, 'after');
            $transactions->where('status !=', 0);
            $gcash = $transactions->get()->getRow();

            $transactions->selectSum('payment1Cash');
            $transactions->where('payment1Cashier', $userID);
            $transactions->like('dateCreated', $salesDate, 'after');
            $transactions->like('payment1Date', $salesDate, 'after');
            $transactions->where('status !=', 0);
            $payment1Cash = $transactions->get()->getRow();

            $transactions->selectSum('payment1GCash');
            $transactions->where('payment1Cashier', $userID);
            $transactions->like('dateCreated', $salesDate, 'after');
            $transactions->like('payment1Date', $salesDate, 'after');
            $transactions->where('status !=', 0);
            $payment1Gcash = $transactions->get()->getRow();

            $transactions->selectSum('payment2Cash');
            $transactions->where('payment2Cashier', $userID);
            $transactions->like('dateCreated', $salesDate, 'after');
            $transactions->like('payment2Date', $salesDate, 'after');
            $transactions->where('status !=', 0);
            $payment2Cash = $transactions->get()->getRow();

            $transactions->selectSum('payment2GCash');
            $transactions->where('payment2Cashier', $userID);
            $transactions->like('dateCreated', $salesDate, 'after');
            $transactions->like('payment2Date', $salesDate, 'after');
            $transactions->where('status !=', 0);
            $payment2Gcash = $transactions->get()->getRow();

            $transactions->selectSum('balance');
            $transactions->where('userID', $userID);
            $transactions->like('dateCreated', $salesDate, 'after');
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

    public function logout()
    {
        $userID = session()->get('loggedInUser');

        $hostName  = gethostname();
        $ipAddress = gethostbyname($hostName);

        $loginlogs = array(
            'host'      => $hostName,
            'hostname'  => $ipAddress,
            'userID'    => $userID,
            'date'      => date('Y-m-d H:i:s'),
            'operation' => 'success',
            'logs'      => 'logout'
        );

        $this->db->table('loginlogs')
            ->insert($loginlogs);

        $this->db->table('users')
            ->set('lastLogout', date('Y-m-d H:i:s'))
            ->where('userID', $userID)
            ->update();

        session()->remove('loggedInUser');

        return redirect()->to(site_url('/'));
    }
}
