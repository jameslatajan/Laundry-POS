<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

use App\Models\transactions;
use App\Models\users;
use App\Models\sale_headers;

use App\Libraries\Htmlhelper;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers: 
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    protected $session;
    protected $db;
    protected $current_user;
    protected $data;

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        date_default_timezone_set('Asia/Manila');

        // Preload any models, libraries, etc, here.
        $this->session      = \Config\Services::session();
        $this->db           = \Config\Database::connect();
        $this->pagination   = \Config\Services::pager();

        $this->current_user = session()->get('loggedInUser');

        $this->data['HtmlHelper'] = new Htmlhelper();

        if ($this->current_user) {
            $_SESSION['page']    = 1;
            $_SESSION['perPage'] = 8;

            $user = new users();
            $user->select('firstName');
            $user->select('lastName');
            $user->select('username');
            $user->select('userType');
            $user->select('isDsr');
            $user->select('status');
            $user->select('empID');
            $user->select('userID');
            $user->where('userID',  $this->current_user);
            $currUser = $user->first();

            $this->data['user'] = $currUser;
            $this->data['data'] = $this->header();
        }
    }

    public function header()
    {
        $user         = new users();
        $transactions = new transactions();
        $header       = new sale_headers();

        $user->select('userID');
        $user->select('firstName');
        $user->select('lastName');
        $user->select('username');
        $user->select('userType');
        $data['user'] = $user->where('userID', session()->get('loggedInUser'))->first();

        $data['record'] = array();

        $transactions->select('dateCreated');
        $transactions->select('customer');
        $transactions->select('mobile');
        $transactions->select('totalAmount');
        $transactions->like('dateCreated', date("Y-m-d"));
        $transactions->where('status !=', 0);
        $transactions->where('userID',  session()->get('loggedInUser'));
        $transactions->orderBy('dateCreated', 'DESC');
        $data['record'] = $transactions->findAll();

        $data['transAmountPaid'] = array();
        // $data['transAmountPaid'] = $this->headerSales(date("Y-m-d")); // hide kay mu kaging

        $header->selectSum('totalAmount');
        $header->like('dateCreated', date("Y-m-d"));
        $header->where('userID',  session()->get('loggedInUser'));
        $data['headerAmountPaid'] = $header->findAll();

        return $data;
    }

    public function headerSales($dateCreated)
    {
        $transactions = new transactions();
        $transactions->select('transID');
        $transactions->like('dateCreated', $dateCreated);
        $count = $transactions->countAllResults();
        if ($count) {
            $transactions->selectSum('cash');
            $transactions->where('DATE(dateCreated)', $dateCreated);
            $transactions->where('status !=', 0);
            $transactions->where('userID',  session()->get('loggedInUser'));
            $result['cash'] = $transactions->findAll();

            $transactions->selectSum('gCash');
            $transactions->where('DATE(dateCreated)', $dateCreated);
            $transactions->where('status !=', 0);
            $transactions->where('userID',  session()->get('loggedInUser'));
            $result['gCash'] = $transactions->findAll();

            $transactions->selectSum('payment1Cash');
            $transactions->where('DATE(dateCreated)', $dateCreated);
            $transactions->where('DATE(payment1Date)', $dateCreated);
            $transactions->where('status !=', 0);
            $transactions->where('payment1Cashier',  session()->get('loggedInUser'));
            $result['payment1Cash'] = $transactions->findAll();

            $transactions->selectSum('payment1GCash');
            $transactions->where('DATE(dateCreated)', $dateCreated);
            $transactions->where('DATE(payment1Date)', $dateCreated);
            $transactions->where('status !=', 0);
            $transactions->where('payment1Cashier',  session()->get('loggedInUser'));
            $result['payment1GCash'] = $transactions->findAll();

            $transactions->selectSum('payment2Cash');
            $transactions->where('DATE(dateCreated)', $dateCreated);
            $transactions->where('DATE(payment2Date)', $dateCreated);
            $transactions->where('status !=', 0);
            $transactions->where('payment2Cashier',  session()->get('loggedInUser'));
            $result['payment2Cash'] = $transactions->findAll();

            $transactions->selectSum('payment2GCash');
            $transactions->where('DATE(dateCreated)', $dateCreated);
            $transactions->where('DATE(payment2Date)', $dateCreated);
            $transactions->where('status !=', 0);
            $transactions->where('payment2Cashier',  session()->get('loggedInUser'));
            $result['payment2GCash'] = $transactions->findAll();

            $transactions->selectSum('balance');
            $transactions->where('DATE(dateCreated)', $dateCreated);
            $transactions->where('status !=', 0);
            $transactions->where('userID',  session()->get('loggedInUser'));
            $result['balance'] = $transactions->findAll();

            $transactions->selectSum('totalAmount');
            $transactions->where('DATE(dateCreated)', $dateCreated);
            $transactions->where('status !=', 0);
            $transactions->where('userID',  session()->get('loggedInUser'));
            $result['totalAmount'] = $transactions->findAll();

            $transactions->selectSum('amountPaid');
            $transactions->where('DATE(dateCreated)', $dateCreated);
            $transactions->where('status !=', 0);
            $transactions->where('userID',  session()->get('loggedInUser'));
            $result['amountPaid'] = $transactions->findAll();
            return  $result;
        } else {
            return  false;
        }
    }

    public function setMessage($type, $message)
    {
        $this->session->setFlashdata('message_type', $type);
        $this->session->setFlashdata('message', $message);
    }

    public function sms_status()
    {
        $builder = $this->db->table('config');
        $builder->select('name');
        $builder->select('value');
        $builder->where('name', 'Sms Status');
        $rec = $builder->get()->getRow();

        return $rec->value;
    }
}
