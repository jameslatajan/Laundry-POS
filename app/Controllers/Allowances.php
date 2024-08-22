<?php

namespace App\Controllers;

require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class Allowances extends BaseController
{
    protected $table;
    protected $pfield;
    protected $controller_page;

    public function __construct()
    {
        $this->data['module_title']   = 'Allowances';
        $this->data['module_desc']    = 'Description about expenses';
        $this->controller_page        = $this->data['controller_page'] = site_url('allowances');
        $this->data['current_module'] = 'allowances';
        $this->data['current_menu']   = '';
        $this->table                  = 'expenses';
        $this->pfield                 = 'expID';
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
        $builder->where('allowance', 1);
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
        $builder->where('allowance', 1);

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
        $builder->where('allowance', 1);

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
        $data['sortby']     = $sortby;
        $data['sortorder']  = $sortorder;
        $data['limit']      = $limit;
        $data['offset']     = $offset;
        $data['startDate']  = $startDate;
        $data['endDate']    = $endDate;

        $data['user']  = $this->db->table('users')->where('userID', $this->current_user)->get()->getRow();

        $users_builder =  $this->db->table('users');
        $users_builder->select('firstName');
        $users_builder->select('lastName');
        $users_builder->select('userID');
        $users_builder->select('username');
        $users_builder->where('userType', 'Cashier');
        $users_builder->orderBy('firstName', 'asc');
        $data['users'] = $users = $users_builder->get()->getResult();

        $data['title'] = 'Allowances';

        echo view('header', $data);
        echo view('pages/allowances/show');
        echo view('footer');
    }

    public function save()
    {
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

        $fields['expDate']     = $date;
        $fields['allowance']   = 1;
        $fields['dateCreated'] = date('Y-m-d H:i:s');
        $fields['createdBy']   = $this->current_user;

        $builder = $this->db->table($this->table);
        if ($builder->insert($fields)) {
            return redirect()->to('allowances');
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
        $builder->where('allowance', 1);

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
        $data['sortby']     = $sortby;
        $data['sortorder']  = $sortorder;
        $data['limit']      = $limit;
        $data['offset']     = $offset;

        echo view('pages/allowances/print', $data);
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
        $builder->select($this->table . '.expID');
        $builder->select($this->table . '.particular');
        $builder->select($this->table . '.dateCreated');
        $builder->select($this->table . '.amount');
        $builder->select('users.username, userID, empID');
        $builder->join('users', "CONCAT(users.firstName, ' ', users.lastName) = " . $this->table . '.particular', 'left');
        $builder->where('allowance', 1);
        $builder->where('amount <=', 100);

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

        // if ($limit) {
        //     if ($offset) {
        //         $builder->limit($limit, $offset);
        //     } else {
        //         $builder->limit($limit);
        //     }
        // }

        // assigning variables
        $data['sortby']     = $sortby;
        $data['sortorder']  = $sortorder;
        // $data['limit']      = $limit;
        // $data['offset']     = $offset;

        // get
        $records =  $builder->get()->getResult();

        // var_dump($records);
        // die;

        $title = 'ALLOWANCES';

        $filename    = $title . '-' . date('mdYHi') . '.xls';
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();


        // $sheet->setCellValue('A2', 'LABACHINE LAUNDRY LOUNGE');
        // $sheet->setCellValue('A3', $title);
        // $sheet->mergeCells("A2:C2");
        // $sheet->mergeCells("A3:C3");

        // $sheet->mergeCells('A2:C2');
        // $sheet->mergeCells('A3:C3');

        // $sheet->getStyle('A2:C2')->getAlignment()->setHorizontal('center');
        // $sheet->getStyle('A3:C3')->getAlignment()->setHorizontal('center');

        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);

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

        $startCol      = 1;
        $rowStartCount = 2;
        $rowCount      = 2;

        if ($records) {
            $sheet->setCellValue('A' . $startCol, 'EMPLOYEE ID');
            $sheet->setCellValue('B' . $startCol, 'ALLOWANCE');
            $sheet->setCellValue('c' . $startCol, 'DATE CREATED');

            foreach ($records as $rec) {
                $sheet->setCellValue('a' . $rowCount, $rec->empID);
                $sheet->setCellValue('b' . $rowCount, $rec->amount);
                $sheet->setCellValue('c' . $rowCount, date('Y-m-d',  strtotime($rec->dateCreated)));
                $rowCount++;
            }
            // $sheet->setCellValue('a' . $rowCount, 'Total');
            // $sheet->setCellValue('b' . $rowCount, '=SUM(b' . $rowStartCount . ':' . 'b' . $rowCount . ')');

            $sheet->getStyle('A' . $startCol . ':' . 'c' . ($rowCount - 1))->applyFromArray($insideBorderStyle);
            $sheet->getStyle('A' . $startCol . ':' . 'c' . ($rowCount - 1))->applyFromArray($outsideBorderStyle);
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
