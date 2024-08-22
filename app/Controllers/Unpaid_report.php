<?php

namespace App\Controllers;

use Mpdf\Mpdf;
use PDO;

class Unpaid_report extends BaseController
{
    protected $data;
    protected $table;
    protected $pfield;
    protected $controller_page;

    public function __construct()
    {
        $this->data['module_title']   = 'UNPAID REPORT';
        $this->data['module_desc']    = 'Description about transactions';
        $this->data['current_module'] = 'unpaid_report';
        $this->data['current_menu']   = '';
        $this->table                  = 'transactions';
        $this->pfield                 = 'transID';
        $this->controller_page        = $this->data['controller_page'] = site_url('unpaid_report');
    }

    public function show()
    {
        $data = $this->data;

        $condition_fields = array(
            array(
                'variable'      => 'year',
                'field'         => 'dateCreated',
                'default_value' => date('Y'),
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'month',
                'field'         => 'dateCreated',
                'default_value' =>  date('m'),
                'operator'      => 'like_both'
            ),
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

        // assign data variables for views
        foreach ($condition_fields as $key) {
            $data[$key['variable']] = ${$key['variable']};
        }

        // select
        $date         = $year . '-' . $month;
        $data['date'] = $date;

        $builder      = $this->db->table($this->table);
        $builder->select('DATE(transactions.dateCreated) as dateCreated');
        $builder->like('dateCreated', $date, 'after');
        $builder->where('balance >', 0);
        $builder->where('status !=', 0);
        $builder->where('status !=', 7);
        $builder->distinct('dateCreated');
        $builder->orderBy('dateCreated', 'asc');
        $getOnlyTheDates = $builder->get()->getResult();

        $builder      = $this->db->table($this->table);
        $builder->select('DATE(transactions.dateCreated) as dateCreated');
        $builder->select('transactions.customer');
        $builder->select('transactions.mobile');
        $builder->select('transactions.balance');
        $builder->select('transactions.transID');
        $builder->select('transactions.qrCode');
        $builder->select('transactions.dateCreated');
        $builder->like('dateCreated', $date, 'after');
        $builder->where('balance >', 0);
        $builder->where('status !=', 0);
        $builder->where('status !=', 7);
        $builder->orderBy('dateCreated', 'asc');
        $records =  $builder->get()->getResult();

        $variance_result = array();
        if ($records) {
            foreach ($records as $rec) {
                $builder = $this->db->table('detailed_dsr');
                $builder->select('variance, sales_date, shift');
                $builder->like('sales_date', date('Y-m-d', strtotime($rec->dateCreated)));
                $builder->orderBy('sales_date');
                $variance = $builder->get()->getResult();

                if ($variance) {
                    foreach ($variance as $varia) {
                        $variance_result[$varia->sales_date] = $varia->variance;
                    }
                }
            }
        }

        // var_dump($variance_result);
        // die;

        // get
        $data['variance_result']  = $variance_result;
        $data['records']          = $records;
        $data['getOnlyThesDates'] = $getOnlyTheDates;
        $data['totalJo']          = count($records);

        $months = array(
            "January", "February", "March", "April",
            "May", "June", "July", "August",
            "September", "October", "November", "December"
        );

        $builder      = $this->db->table($this->table);
        $builder->select('YEAR(dateCreated) as year');
        $builder->distinct();
        $years = $builder->get()->getResult();

        $data['years']  = $years;
        $data['months'] = $months;

        $data['title']  = 'Unpaid Report';

        echo view('header', $data);
        echo view('pages/unpaid_report/show');
        echo view('footer');
    }

    public function printForm()
    {
        $data = $this->data;

        $condition_fields = array(
            array(
                'variable'      => 'year',
                'field'         => 'dateCreated',
                'default_value' => date('Y'),
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'month',
                'field'         => 'dateCreated',
                'default_value' =>  date('m'),
                'operator'      => 'like_both'
            ),
        );

        //get the controller
        $controller = service('uri')->getSegment(1);

        foreach ($condition_fields as $key) {
            ${$key['variable']} = $this->session->get($controller . '_' . $key['variable']);
        }

        // select
        $date         = $year . '-' . $month;
        $data['date'] = $date;

        $builder      = $this->db->table($this->table);
        $builder->select('DATE(transactions.dateCreated) as dateCreated');
        $builder->like('dateCreated', $date, 'after');
        $builder->where('balance >', 0);
        $builder->where('status !=', 0);
        $builder->where('status !=', 7);
        $builder->distinct('dateCreated');
        $builder->orderBy('dateCreated', 'asc');
        $getOnlyTheDates = $builder->get()->getResult();

        $builder      = $this->db->table($this->table);
        $builder->select('DATE(transactions.dateCreated) as dateCreated');
        $builder->select('transactions.customer');
        $builder->select('transactions.mobile');
        $builder->select('transactions.balance');
        $builder->select('transactions.transID');
        $builder->select('transactions.qrCode');
        $builder->select('transactions.dateCreated');
        $builder->like('dateCreated', $date, 'after');
        $builder->where('balance >', 0);
        $builder->where('status !=', 0);
        $builder->where('status !=', 7);
        $builder->orderBy('dateCreated', 'asc');
        $records =  $builder->get()->getResult();

        $variance_result = array();
        if ($records) {
            foreach ($records as $rec) {
                $builder = $this->db->table('detailed_dsr');
                $builder->select('variance, sales_date, shift');
                $builder->like('sales_date', date('Y-m-d', strtotime($rec->dateCreated)));
                $builder->orderBy('sales_date');
                $variance = $builder->get()->getResult();

                if ($variance) {
                    foreach ($variance as $varia) {
                        $variance_result[$varia->sales_date] = $varia->variance;
                    }
                }
            }
        }

        // var_dump($variance_result);
        // die;

        // get
        $data['variance_result']  = $variance_result;
        $data['records']          = $records;
        $data['getOnlyThesDates'] = $getOnlyTheDates;
        $data['totalJo']          = count($records);

        $months = array(
            "January", "February", "March", "April",
            "May", "June", "July", "August",
            "September", "October", "November", "December"
        );

        $builder      = $this->db->table($this->table);
        $builder->select('YEAR(dateCreated) as year');
        $builder->distinct();
        $years = $builder->get()->getResult();

        $data['years']      = $years;
        $data['months']     = $months;

        $mpdf = new Mpdf([
            'mode'              => 'utf-8',
            'format'            => 'A4',
            'margin_top'        => 30,
            'margin_bottom'     => 30,
            'pdf_version'       => 1.7,
            'default_font_size' => 12,
            'orientation'       => 'L',
            'default_font'      => 'arial'
        ]);

        $title       = 'Unpaid Report';
        $description = '';

        $data['title']           = $title;
        $data['description']     = $description;
        $data['company_name']    = 'Laundry Shop Laundry Lounge';
        $data['company_address'] = $title;

        $html = view('pages/unpaid_report/pdf', $data);

        $mpdf->SetHTMLHeader('
        <table class="heading-table">
            <tbody>
                <tr>
                    <td class="heading-left">
                        <img class="heading-img" src="' . base_url() . 'assets/images/logo-final.png" alt="">
                    </td>
                    <td class="heading-center">
                        <p class="heading-company-name">' . $data['company_name'] . '</p>
                        <p class="heading-company-address">' . $data['company_address'] . '</p>
                    </td>
                    <td class="heading-right"></td>
                </tr>
            </tbody>
        </table>
        ');
        $mpdf->SetFooter('{PAGENO}');
        $mpdf->WriteHTML($html);

        $mpdf->Output();
        die;
    }
}
