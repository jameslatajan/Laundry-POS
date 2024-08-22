<?php

namespace App\Controllers;

use App\Libraries\Htmlhelper;
use PDO;

require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use stdClass;

class ExpensesC extends BaseController
{
    protected $data;
    protected $table;
    protected $pfield;
    protected $controller_page;

    public function __construct()
    {
        $this->data['module_title']   = 'EXPENSES';
        $this->data['module_desc']    = 'Description about expenses';
        $this->data['current_module'] = 'reports';
        $this->data['current_menu']   = '';
        $this->table                  = 'expenses';
        $this->pfield                 = 'expID';
        $this->controller_page        = $this->data['controller_page'] = site_url('expenses');
    }

    public function list()
    {
        $data = $this->data;

        $condition_fields = array(
            array(
                'variable'      => 'particular',
                'field'         => "expenses.particular",
                'default_value' => "",
                'operator'      => 'like'
            ),
            array(
                'variable'      => 'amount',
                'field'         => 'expenses.amount',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'dateCreated',
                'field'         => 'expenses.dateCreated',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'expDate',
                'field'         =>  'expenses.expDate',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'createdBy',
                'field'         =>  'expenses.createdBy',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
        );

        // sorting fields
        $sorting_fields = array(
            'expDate' => 'desc',
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
        $builder->select($this->table . '.*');
        $builder->where('allowance', 0);
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
        $builder->select($this->table . '.*');
        $builder->select('users.username, userID');
        $builder->join('users', 'users.userID = ' . $this->table . '.createdBy', 'left');
        $builder->where('allowance', 0);

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
        $startDate = date('Y-m-d', strtotime($startDate));
        $endDate   = date('Y-m-d', strtotime($endDate));

        if ($startDate != '' && $startDate != '1970-01-01') {
            $builder->where("(expenses.expDate >= '$startDate' and expenses.expDate <= '$endDate')");
        }

        // get
        // $data['records'] = $records =  $builder->get()->getResult();
        $data['ttl_rows'] = $ttl_rows =  $config['total_rows'] = $builder->countAllResults();

        // set pagination
        $data['pagination'] = $pagination = $this->pagination->makeLinks($page, $limit, $ttl_rows, 'custom_view');

        // select
        $builder->select($this->table . '.*');
        $builder->select('users.username, userID');
        $builder->join('users', 'users.userID = ' . $this->table . '.createdBy', 'left');
        $builder->where('allowance', 0);

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
        $startDate = date('Y-m-d', strtotime($startDate));
        $endDate   = date('Y-m-d', strtotime($endDate));

        if ($startDate != '' && $startDate != '1970-01-01') {
            $builder->where("(expenses.expDate >= '$startDate' and expenses.expDate <= '$endDate')");
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
        $data['sortby']    = $sortby;
        $data['sortorder'] = $sortorder;
        $data['limit']     = $limit;
        $data['offset']    = $offset;
        $data['startDate'] = $startDate;
        $data['endDate']   = $endDate;

        $data['user']  = $this->db->table('users')->where('userID', $this->current_user)->get()->getRow();
        $data['users']  = $this->db->table('users')->get()->getResult();

        $data['title']     = 'Expenses';


        echo view('header', $data);
        echo view('pages/reports/expenses');
        echo view('footer');
    }

    public function save()
    {
        $data         = $this->data;
        $table_fields = array(
            'particular',
            'amount',
            'expDate',
        );

        $fields = array();
        foreach ($table_fields as $fld) {
            $fields[$fld] = $this->request->getPost($fld);
        }

        $date = date('Y-m-d H:i:s');
        if (isset($expDate)) {
            $date = $expDate;
        }

        $fields['expDate'] = $date;

        $fields['dateCreated'] = date('Y-m-d H:i:s');
        $fields['createdBy']   = $this->current_user;

        $builder = $this->db->table($this->table);
        if ($builder->insert($fields)) {
            return redirect()->to('expenses');
        } else {
            echo 'error saving';
        }
    }

    public function printlist()
    {
        $data = $this->data;
        $condition_fields = array(
            array(
                'variable'      => 'particular',
                'field'         => "expenses.particular",
                'default_value' => "",
                'operator'      => 'like'
            ),
            array(
                'variable'      => 'amount',
                'field'         => 'expenseds.amount',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'dateCreated',
                'field'         => 'expenses.dateCreated',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'expDate',
                'field'         => 'expenses.expDate',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'createdBy',
                'field'         => 'expenses.createdBy',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
        );

        // sorting fields
        $sorting_fields = array(
            'expDate' => 'desc',
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
        $builder->select($this->table . '.*');
        $builder->select('users.username, userID');
        $builder->join('users', 'users.userID = ' . $this->table . '.createdBy', 'left');
        $builder->where('allowance', 0);

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
        $startDate = date('Y-m-d', strtotime($startDate));
        $endDate   = date('Y-m-d', strtotime($endDate));

        if ($startDate != '' && $startDate != '1970-01-01') {
            $builder->where("(expenses.expDate >= '$startDate' and expenses.expDate <= '$endDate')");
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
        $data['HtmlHelper'] = new Htmlhelper();
        $data['sortby']     = $sortby;
        $data['sortorder']  = $sortorder;
        $data['limit']      = $limit;
        $data['offset']     = $offset;

        echo view('pages/reports/expenses_print', $data);
    }

    public function exportlist()
    {
        $data = $this->data;
        $condition_fields = array(
            array(
                'variable'      => 'particular',
                'field'         => "expenses.particular",
                'default_value' => "",
                'operator'      => 'like'
            ),
            array(
                'variable'      => 'amount',
                'field'         => 'expenseds.amount',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'dateCreated',
                'field'         => 'expenses.dateCreated',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'expDate',
                'field'         => 'expenses.expDate',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'createdBy',
                'field'         => 'expenses.createdBy',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
        );

        // sorting fields
        $sorting_fields = array(
            'expDate' => 'desc',
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
        $builder->select($this->table . '.*');
        $builder->select('users.username, userID');
        $builder->join('users', 'users.userID = ' . $this->table . '.createdBy', 'left');
        $builder->where('allowance', 0);

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
        $startDate = date('Y-m-d', strtotime($startDate));
        $endDate   = date('Y-m-d', strtotime($endDate));

        if ($startDate != '' && $startDate != '1970-01-01') {
            $builder->where("(expenses.expDate >= '$startDate' and expenses.expDate <= '$endDate')");
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

        $title = 'EXPENSES';

        $filename    = $title . '-' . date('mdYHi') . '.xlsx';
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();


        $sheet->setCellValue('A2', 'LABACHINE LAUNDRY LOUNGE');
        $sheet->setCellValue('A3', $title);
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
            $sheet->setCellValue('A' . $startCol, 'EXPENSES DATE');
            $sheet->setCellValue('B' . $startCol, 'DATE CREATED');
            $sheet->setCellValue('c' . $startCol, 'PARTICULAR');
            $sheet->setCellValue('d' . $startCol, 'AMOUNT');
            $sheet->setCellValue('e' . $startCol, 'CASHIER');

            foreach ($records as $rec) {
                $sheet->setCellValue('a' . $rowCount, date('d/m/Y',  strtotime($rec->expDate)));
                $sheet->setCellValue('b' . $rowCount, date('d/m/Y',  strtotime($rec->dateCreated)));
                $sheet->setCellValue('c' . $rowCount, $rec->particular);
                $sheet->setCellValue('d' . $rowCount, $rec->amount);
                $sheet->setCellValue('e' . $rowCount, $rec->username);
                $rowCount++;
            }
            $sheet->setCellValue('c' . $rowCount, 'Total');
            $sheet->setCellValue('d' . $rowCount, '=SUM(d' . $rowStartCount . ':' . 'd' . $rowCount . ')');

            $sheet->getStyle('A' . $startCol . ':' . 'e' . $rowCount)->applyFromArray($insideBorderStyle);
            $sheet->getStyle('A' . $startCol . ':' . 'e' . $rowCount)->applyFromArray($outsideBorderStyle);
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
