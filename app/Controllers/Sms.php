<?php

namespace App\Controllers;

use App\Models\transactions;
use App\Libraries\Htmlhelper;

class Sms extends BaseController
{
  protected $table;
  protected $pfield;
  protected $controller_page;

  public function __construct()
  {
    $this->data['module_title']    = 'SMS';
    $this->data['module_desc']     = 'Send Sms';
    $this->data['controller_page'] = $this->controller_page = site_url('sms');
    $this->data['current_module']  = 'sms';
    $this->data['current_menu']    = '';
    $this->table                   = 'sms';
    $this->pfield                  = 'smsID';
  }

  public function list()
  {
    $data = $this->data;
    $transactions = new transactions();

    $condition_fields = array(
      array(
        'variable'      => 'dateSent',
        'field'         => "",
        'default_value' => "",
        'operator'      => 'like'
      ),
      array(
        'variable'      => 'customer',
        'field'         => $this->table . '.customer',
        'default_value' => "",
        'operator'      => 'like_after'
      ),
      array(
        'variable'      => 'jostatus',
        'field'         => $this->table . '.jostatus',
        'default_value' => "",
        'operator'      => 'like_both'
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
    );

    // sorting fields
    $sorting_fields = array(
      'dateSent' => 'desc',
    );

    //get the controller
    $controller = service('uri')->getSegment(1);

    // Initialize pager library
    if ($this->request->getVar('page'))
      $page = $this->request->getVar('page');
    else
      $page = 1;

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

    // initialize builder
    $builder = $this->db->table($this->table);

    //count total rows
    $builder->select('*');
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

    //select
    $builder->select('*');

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
      $builder->where("(sms.dateSent >= '$startDate' and sms.dateSent <= '$endDate')");
    }

    // get
    // $data['records'] = $records =  $builder->get()->getResult();
    $data['ttl_rows'] = $ttl_rows =  $config['total_rows'] = $builder->countAllResults();

    // set pagination
    $data['pagination'] = $pagination = $this->pagination->makeLinks($page, $limit, $ttl_rows, 'custom_view');

    // select
    $builder = $this->db->table($this->table);
    $builder->select('*');

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
      $builder->where("(sms.dateSent >= '$startDate' and sms.dateSent <= '$endDate')");
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

    // assigning variables
    $data['HtmlHelper'] = new Htmlhelper();
    $data['sortby']     = $sortby;
    $data['sortorder']  = $sortorder;
    $data['limit']      = $limit;
    $data['offset']     = $offset;
    $data['startDate']  = $startDate;
    $data['endDate']    = $endDate;
    $transactions->select('transID, mobile, status, customer, dateCreated');
    $transactions->distinct(); // Add this line to select distinct records
    // $transactions->like('dateCreated', '2024-01-23', 'after'); //customize date
    $transactions->groupBy('mobile, customer');
    $transactions->where('status', 6);
    $transactions->where('isSms', 0);
    $data['readyList'] = $transactions->get()->getResult();

    $transactions->select('transID, mobile, status, customer, dateCreated');
    $transactions->groupBy('mobile, customer');
    $transactions->where('status', 1);
    // $transactions->where('isSms', 0);
    $transactions->where('dateCreated >', '2023-12-18');
    $data['receivelist'] = $transactions->get()->getResult();

    $data['title'] = 'SMS';

    echo view('header', $data);
    echo view('pages/sms/list');
    echo view('footer');
  }

  public function textblastTest()
  {
    $date    = $this->request->getPost('date');
    $status  = $this->request->getPost('status');
    $message = $this->request->getPost('message');
    $mobile  = $this->request->getPost('mobile');

    $builder = $this->db->table('transactions');
    $builder->select('customer, status, dateCreated, transID');
    $builder->like('dateCreated', $date, 'right');
    $builder->where('status', $status);
    $result = $builder->get()->getResult();

    $sesStatus  = '';
    $sesMessage = '';
    if ($result) {
      $count = count($result);
      foreach ($result as $res) {
        $gw_send_smss = $this->gw_send_smss($mobile, $message);

        $data = array(
          'transID'  => $res->transID,
          'customer' => $res->customer,
          'mobile'   => $mobile,
          'status'   => $gw_send_smss->success,
          'jostatus' => $res->status,
          'message'  => $message,
          'response' => $gw_send_smss->message,
          'dateSent' => date('Y-m-d H:i:s'),
        );

        $builder = $this->db->table($this->table);
        $builder->insert($data);

        $result[] = $data;
      }

      $sesStatus  = 'success';
      $sesMessage = 'Message sent to ' . $count . ' respondents';
    } else {
      $sesStatus  = 'danger';
      $sesMessage = 'Nothing to send';
    }

    $this->setMessage($sesStatus, $sesMessage);

    return redirect()->to('sms');
  }

  public function textblast()
  {
    $dateCreated = $this->request->getPost('dateCreated');
    $status      = $this->request->getPost('status');
    $message     = $this->request->getPost('message');

    $builder = $this->db->table('transactions');
    $builder->select('customer, mobile, status, dateCreated, transID');
    $builder->like('dateCreated', $dateCreated, 'right');
    $builder->where('status', $status);
    $result = $builder->get()->getResult();

    $sesStatus  = '';
    $sesMessage = '';
    if ($result) {
      $count = count($result);
      foreach ($result as $res) {
        $gw_send_smss = $this->gw_send_smss($res->mobile, $message);

        $data = array(
          'transID'  => $res->transID,
          'customer' => $res->customer,
          'mobile'   => $res->mobile,
          'status'   => $gw_send_smss->success,
          'jostatus' => $res->status,
          'message'  => $message,
          'response' => $gw_send_smss->message,
          'dateSent' => date('Y-m-d H:i:s'),
        );

        $builder = $this->db->table($this->table);
        $builder->insert($data);

        $result[] = $data;
      }

      $sesStatus  = 'success';
      $sesMessage = 'Message sent to ' . $count . ' respondents';
    } else {
      $sesStatus  = 'danger';
      $sesMessage = 'Message failed. Nothing to sent';
    }

    $this->setMessage($sesStatus, $sesMessage);

    return redirect()->to('sms');
  }

  public function getData($status, $dateCreated)
  {
    $builder = $this->db->table('transactions');
    $builder->select('customer, mobile, status, dateCreated, transID');
    $builder->like('dateCreated', $dateCreated, 'right');
    $builder->where('status', $status);
    $result = $builder->get()->getResult();

    return $this->response->setJSON($result);
  }

  public function saveReady()
  {
    $transactions = new transactions();
    $transIDs = $this->request->getPost('checkAllReady');

    $builder = $this->db->table($this->table);

    // // start unpaid
    // $transactions->select('transID, mobile, status, customer, dateCreated');
    // $transactions->where('balance >',  0);
    // $transactions->where('isSms',  1);
    // $unpaids = $transactions->get()->getResult();


    // //list of unpaids
    // foreach ($unpaids as $un) {
    //   $message      = "Good day " . $un->customer . ". Your laundry is now ready to claim. Pls bring your claim slip. Thank you.";
    //   $gw_send_smss = $this->gw_send_smss($un->mobile, $message);

    //   $data = array(
    //     'transID'  => $un->transID,
    //     'customer' => $un->customer,
    //     'mobile'   => $un->mobile,
    //     'status'   => $gw_send_smss->success,
    //     'jostatus' => $un->status,
    //     'message'  => $message,
    //     'response' => $gw_send_smss->message,
    //     'dateSent' => date('Y-m-d H:i:s'),
    //   );

    //   $builder->insert($data);
    // }
    // //end unpaid

    //start ready
    $sentCount = 0;
    foreach ($transIDs as $transID) {
      $transactions->select('transID, mobile, status, customer, dateCreated');
      $transactions->where('transID',  $transID);
      $datalist = $transactions->get()->getRow();

      $message      = "Good day " . $datalist->customer . ". Your laundry is now ready to claim. Pls bring your claim slip. Thank you.";
      $gw_send_smss = $this->gw_send_smss($datalist->mobile, $message);

      $data = array(
        'transID'  => $datalist->transID,
        'customer' => $datalist->customer,
        'mobile'   => $datalist->mobile,
        'status'   => $gw_send_smss->success,
        'jostatus' => $datalist->status,
        'message'  => $message,
        'response' => $gw_send_smss->message,
        'dateSent' => date('Y-m-d H:i:s'),
      );

      if ($builder->insert($data)) {
        if ($gw_send_smss->success == 'success') {
          $transactions->set('isSms', 1);
          $transactions->where('transID', $transID);
          $transactions->update();
          $sentCount++;
        }
      }
    }
    //end ready

    if ($sentCount) {
      $sesStatus  = 'success';
      $sesMessage = 'Message sent to ' . $sentCount . ' respondents';
    } else {
      $sesStatus  = 'danger';
      $sesMessage = 'Nothing to send';
    }

    $this->setMessage($sesStatus, $sesMessage);

    return redirect()->to('sms');
  }

  public function resend()
  {
    $transactions = new transactions();

    $smsID     = $this->request->getPost('myID');
    $myTransID = $this->request->getPost('myTransID');
    $mobile    = $this->request->getPost('mymobile');
    $customer  = $this->request->getPost('mycustomer');

    $message      = "Good day " .  $customer . ". Your laundry is now ready to claim. Pls bring your claim slip. Thank you.";
    $gw_send_smss = $this->gw_send_smss($mobile,  $message);

    $data = array(
      'mobile'   => $mobile,
      'status'   => $gw_send_smss->success,
      'jostatus' => $gw_send_smss->success,
      'message'  => $message,
      'response' => $gw_send_smss->message,
      'dateSent' => date('Y-m-d H:i:s'),
    );

    $builder = $this->db->table($this->table);
    $builder->set($data);
    $builder->where($this->pfield, $smsID);

    $sentCount = 0;
    if ($builder->update($data)) {
      if ($gw_send_smss->success == 'success') {
        $transactions->set('isSms', 1);
        $transactions->where('transID', $myTransID);
        $transactions->update();
        $sentCount++;
      }
    }

    if ($sentCount) {
      $sesStatus  = 'success';
      $sesMessage = 'Message sent to ' . $sentCount . ' respondents';
    } else {
      $sesStatus  = 'danger';
      $sesMessage = 'Nothing to send';
    }

    $this->setMessage($sesStatus, $sesMessage);

    return redirect()->to('sms');
  }

  public function gw_send_smss($sms_to, $sms_msg)
  {
    // Get the current time
    $current_time = date('H:i');

    // Get Target time
    $start_time = strtotime('05:00');
    $end_time   = strtotime('21:00');

    $current_timestamp = strtotime($current_time);

    $api_user = "APIYUJI9KGFE5";
    $api_pass = "APIYUJI9KGFE5YUJI9";

    $response          = new \stdClass;
    $response->success = 'failed';
    $response->message = "";

    if ($this->sms_status()) {
      // Compare the current time to the target time
      if ($current_timestamp >= $start_time && $current_timestamp <= $end_time) {
        if (substr($sms_to, 0, 2) == '09' && strlen($sms_to) == 11 && $sms_to != '09090000000') {
          $query_string = "api.aspx?apiusername=" . $api_user . "&apipassword=" . $api_pass;
          $query_string .= "&senderid=" . rawurlencode('LABACHINE') . "&mobileno=" . rawurlencode($sms_to);
          $query_string .= "&message=" . rawurlencode(stripslashes($sms_msg)) . "&languagetype=1";
          $url = "http://gateway.onewaysms.ph:10001/" . $query_string;

          $fileContent = @file_get_contents($url);
          // $fd = 1; //testing
          // Check if the file contents were successfully loaded
          if ($fileContent) {
            // Now you can use $fileContent as needed
            // $fd = @implode('', file($url));
            // if ($fd > 0) {
            //   $response->success = true;
            //   $response->message = "Message sent";
            // } else {
            //   $response->message = "Please refer to API on Error : " . $fd;
            // }

            $response->success = 'success';
            $response->message = "Message sent";
          } else {
            $response->message = "No contact with gateway";
          }
        } else {
          // Invalid mobile number
          $response->message = "Invalid mobile number";
        }
      } else {
        $response->message = "Cut off sending SMS";
      }
    } else {
      $response->message = "Sending SMS is off";
    }

    return $response;
  }
}
