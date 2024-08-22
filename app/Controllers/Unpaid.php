<?php

namespace App\Controllers;

require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class Unpaid extends BaseController
{
    protected $table;
    protected $pfield;
    protected $controller_page;

    public function __construct()
    {
        $this->data['module_title']   = 'UNPAID';
        $this->data['module_desc']    = 'Description about transactions';
        $this->data['current_module'] = 'asdasd';
        $this->data['current_menu']   = '';
        $this->table                  = 'transactions';
        $this->pfield                 = 'transID';
        $this->controller_page        = $this->data['controller_page'] = site_url('unpaid');
    }

    public function list()
    {
        $data = $this->data;

        $condition_fields = array(
            array(
                'variable'      => 'transactions.dateCreated',
                'field'         => "",
                'default_value' => "",
                'operator'      => 'like'
            ),
            array(
                'variable'      => 'customer',
                'field'         =>  'transactions.customer',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'mobile',
                'field'         => 'transactions.mobile',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'amountPaid',
                'field'         =>  'transactions.amountPaid',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'balance',
                'field'         => 'transactions.balance',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'totalAmount',
                'field'         => 'transactions.totalAmount',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'transID',
                'field'         => 'transactions.transID',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
        );

        // sorting fields
        $sorting_fields = array(
            'dateCreated' => 'desc',
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

        $builder = $this->db->table($this->table); //initialize table

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
        $builder->select('transactions.customer');
        $builder->select('transactions.mobile');
        $builder->select('transactions.amountPaid');
        $builder->select('transactions.balance');
        $builder->select('transactions.totalAmount');
        $builder->select('transactions.transID');
        $builder->where('balance >', 0);
        $builder->where('status !=', 0);
        $builder->where('status !=', 7);

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

        // select
        $builder->select('transactions.qrCode');
        $builder->select('transactions.dateCreated');
        $builder->select('transactions.customer');
        $builder->select('transactions.mobile');
        $builder->select('transactions.amountPaid');
        $builder->select('transactions.balance');
        $builder->select('transactions.totalAmount');
        $builder->select('transactions.transID');
        $builder->where('balance >', 0);
        $builder->where('status !=', 0);
        $builder->where('status !=', 7);

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

        // assigning variables
        $data['sortby']     = $sortby;
        $data['sortorder']  = $sortorder;
        $data['limit']      = $limit;
        $data['offset']     = $offset;
        $data['startDate']  = $startDate;
        $data['endDate']    = $endDate;

        $data['title'] = 'Unpaid';
 
        echo view('header', $data);
        echo view('pages/reports/unpaid');
        echo view('footer');
    }

    public function printlist()
    {
        $data = $this->data;

        $condition_fields = array(
            array(
                'variable'      => 'dateCreated',
                'field'         => "transactions.dateCreated",
                'default_value' => "",
                'operator'      => 'like'
            ),
            array(
                'variable'      => 'customer',
                'field'         =>  'transactions.customer',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'mobile',
                'field'         => 'transactions.mobile',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'amountPaid',
                'field'         =>  'transactions.amountPaid',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'balance',
                'field'         => 'transactions.balance',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'totalAmount',
                'field'         => 'transactions.totalAmount',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'transID',
                'field'         => 'transactions.transID',
                'default_value' => "",
                'operator'      => 'like_both'
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
        $builder->select('transactions.customer');
        $builder->select('transactions.mobile');
        $builder->select('transactions.amountPaid');
        $builder->select('transactions.balance');
        $builder->select('transactions.totalAmount');
        $builder->select('transactions.transID');
        $builder->where('balance >', 0);
        $builder->where('status !=', 0);
        $builder->where('status !=', 7);

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

        echo view('pages/reports/unpaid_print', $data);
    }

    public function exportlist()
    {
        $data = $this->data;

        $condition_fields = array(
            array(
                'variable'      => 'dateCreated',
                'field'         => "transactions.dateCreated",
                'default_value' => "",
                'operator'      => 'like'
            ),
            array(
                'variable'      => 'customer',
                'field'         =>  'transactions.customer',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'mobile',
                'field'         => 'transactions.mobile',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'amountPaid',
                'field'         =>  'transactions.amountPaid',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'balance',
                'field'         => 'transactions.balance',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'totalAmount',
                'field'         => 'transactions.totalAmount',
                'default_value' => "",
                'operator'      => 'like_both'
            ),

            array(
                'variable'      => 'transID',
                'field'         => 'transactions.transID',
                'default_value' => "",
                'operator'      => 'like_both'
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
        $builder = $this->db->table('transactions');
        $builder->select('transactions.qrCode');
        $builder->select('transactions.dateCreated');
        $builder->select('transactions.customer');
        $builder->select('transactions.mobile');
        $builder->select('transactions.amountPaid');
        $builder->select('transactions.balance');
        $builder->select('transactions.totalAmount');
        $builder->select('transactions.transID');
        $builder->where('balance >', 0);
        $builder->where('status !=', 0);

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

        $title = 'UNPAID';

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
            $sheet->setCellValue('A' . $startCol, 'DATE');
            $sheet->setCellValue('B' . $startCol, 'SERIES No.');
            $sheet->setCellValue('c' . $startCol, 'CUSTOMER');
            $sheet->setCellValue('d' . $startCol, 'MOBILE');
            $sheet->setCellValue('e' . $startCol, 'AMOUNT PAID');
            $sheet->setCellValue('f' . $startCol, 'BALANCE');
            $sheet->setCellValue('g' . $startCol, 'AMOUNT DUE');

            foreach ($records as $rec) {
                $sheet->setCellValue('A' . $rowCount, date('d/m/Y',  strtotime($rec->dateCreated)));
                $sheet->setCellValue('B' . $rowCount,  str_pad($rec->transID, 4, "0", STR_PAD_LEFT));
                $sheet->setCellValue('c' . $rowCount, $rec->customer);
                $sheet->setCellValue('d' . $rowCount, $rec->mobile);
                $sheet->setCellValue('e' . $rowCount, $rec->amountPaid);
                $sheet->setCellValue('f' . $rowCount, $rec->balance);
                $sheet->setCellValue('g' . $rowCount, $rec->totalAmount);
                $rowCount++;
            }
            $sheet->setCellValue('d' . $rowCount, 'Total');
            $sheet->setCellValue('e' . $rowCount, '=SUM(E' . $rowStartCount . ':' . 'e' . $rowCount . ')');
            $sheet->setCellValue('f' . $rowCount, '=SUM(F' . $rowStartCount . ':' . 'f' . $rowCount . ')');
            $sheet->setCellValue('g' . $rowCount, '=SUM(G' . $rowStartCount . ':' . 'g' . $rowCount . ')');

            $sheet->getStyle('A' . $startCol . ':' . 'g' . $rowCount)->applyFromArray($insideBorderStyle);
            $sheet->getStyle('A' . $startCol . ':' . 'g' . $rowCount)->applyFromArray($outsideBorderStyle);
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
