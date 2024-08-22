<?php

namespace App\Controllers;

use App\Libraries\Htmlhelper;

require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SalesC extends BaseController
{
    protected $table;
    protected $pfield;
    protected $controller_page;

    public function __construct()
    {
        $this->data['module_title']    = 'SALES';
        $this->data['module_desc']     = 'Description about transactions';
        $this->data['controller_page'] = $this->controller_page = site_url('sales');
        $this->data['current_module']  = 'sales';
        $this->data['current_menu']    = '';
        $this->table                   = 'sales';
        $this->pfield                  = 'salesID';
    }

    public function list()
    {
        $data = $this->data;

        $condition_fields = array(
            array(
                'variable'      => 'description',
                'field'         => $this->table . '.description',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'itemCost',
                'field'         => $this->table . '.itemCost',
                'default_value' => "",
                'operator'      => 'like_after'
            ),
            array(
                'variable'      => 'amount',
                'field'         => $this->table . '.amount',
                'default_value' => "",
                'operator'      => 'like_after'
            ),
            array(
                'variable'      => 'valeBy',
                'field'         => $this->table . '.valeBy',
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
                'variable'      => 'dateCreated',
                'field'         => $this->table . '.dateCreated',
                'default_value' => "",
                'operator'      => 'like_after'
            ),
            array(
                'variable'      => 'salesDate',
                'field'         => $this->table . '.salesDate',
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
        $builder->select('sales.*');
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
        $builder->select('sales.*');
        $builder->select('users.username');
        $builder->select('vale.username as valeName');
        $builder->join('users', 'users.userID = sales.userID', 'left');
        $builder->join('users as vale', 'vale.userID = sales.valeBy', 'left');

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
            $builder->where("(sales.dateCreated >= '$startDate' and sales.dateCreated <= '$endDate')");
        }

        // get
        // $data['records'] = $records =  $builder->get()->getResult();
        $data['ttl_rows'] = $ttl_rows =  $builder->countAllResults();

        // set pagination
        $data['pagination'] = $pagination = $this->pagination->makeLinks($page, $limit, $ttl_rows, 'custom_view');

        // initilaize select
        $builder->select('sales.*');
        $builder->select('users.username');
        $builder->select('vale.username as valeName');
        $builder->join('users', 'users.userID = sales.userID', 'left');
        $builder->join('users as vale', 'vale.userID = sales.valeBy', 'left');

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
            $builder->where("(sales.dateCreated >= '$startDate' and sales.dateCreated <= '$endDate')");
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
        // echo $this->db->getLastQuery();
        // die;

        // var_dump($records);
        // die;

        $bUsers = $this->db->table('users');
        $bUsers->select('userID, username');
        $bUsers->orderBy('username', 'asc');
        $users = $bUsers->get()->getResult();
        $data['users']  = $users;

        $bUsers->select('userID, username, userType');
        $bUsers->where('userID', $this->current_user);
        $current_user = $bUsers->get()->getRow();
        $data['current_user'] = $current_user;

        $bitems = $this->db->table('items');
        $bitems->select('itemID, description');
        $items = $bitems->get()->getResult();
        $data['items']  = $items;

        // assigning variables
        $data['HtmlHelper'] = new Htmlhelper();
        $data['sortby']     = $sortby;
        $data['sortorder']  = $sortorder;
        $data['limit']      = $limit;
        $data['offset']     = $offset;
        $data['startDate']  = $startDate;
        $data['endDate']    = $endDate;

        echo view('header', $data);
        echo view('pages/inventory/sales');
        echo view('footer');
    }

    public function save()
    {
        $valeBy      = $this->request->getPost('createValeBy');
        $salesDate   = $this->request->getPost('createSalesDate');
        $itemID      = $this->request->getPost('createDescription');
        $price       = $this->request->getPost('createPrice');
        $quantity    = $this->request->getPost('createQuantity');
        $amount      = $this->request->getPost('createAmount');
        $referenceNo = $this->request->getPost('createReferenceNo');

        $builder = $this->db->table('items');
        $builder->select('itemID, description, cost');
        $builder->where('itemID', $itemID);
        $item = $builder->get()->getRow();

        $date = "";
        if (!$salesDate) {
            $date = date('Y-m-d H:i:s');
        } else {
            $date = $salesDate . ' ' . date('H:i:s');
        }

        $paymentMethod = "";
        $salesdate     = "";
        if ($referenceNo) {
            $paymentMethod = 'Gcash';
            $salesdate     =    $date;
        } else if ($valeBy) {
            $paymentMethod = 'None';
            $salesdate     = "";
        } else {
            $paymentMethod = 'Cash';
            $salesdate     = $date;
        }


        $data = array(
            'description'   => $item->description,
            'dateCreated'   => date('Y-m-d H:i:s'),
            'valeBy'        => $valeBy,
            'salesDate'     => $salesdate,
            'itemID'        => $itemID,
            'price'         => $price,
            'qty'           => $quantity,
            'amount'        => $amount,
            'paymentMethod' => $paymentMethod,
            'referenceNo'   => $referenceNo,
            'itemCost'      => $item->cost,
            'userID'        => $this->current_user,
            'cashier'       => $this->current_user,
        );

        $builder1 = $this->db->table('sales');
        $builder1->insert($data);
        $salesID = $this->db->insertID();

        $builder2 = $this->db->table('stockcards');
        $builder2->select('endBal, itemID');
        $builder2->where('itemID', $itemID);
        $builder2->orderBy('stockID', 'desc');
        $builder2->limit(1);
        $getEndBalStock = $builder2->get()->getRow();

        $endbal = 0;
        if ($getEndBalStock) {
            $endbal =  $getEndBalStock->endBal;
        }

        $newbal = $endbal - $quantity;

        $stockData = array(
            'date'       => date('Y-m-d H:i:s'),
            'itemID'     => $itemID,
            'begBal'     => $getEndBalStock->endBal,
            'endBal'     => $newbal,
            'credit'     => $quantity,
            'refNo'      => $salesID,
            'remarks'    => 'SALES',
            'insertedBy' => $this->current_user,
        );

        $builder2->insert($stockData);

        return redirect()->to($this->controller_page);
    }

    public function getsales($salesID)
    {
        $builder = $this->db->table('sales');
        $builder->select('sales.*');
        $builder->select('users.username');
        $builder->where('salesID', $salesID);
        $builder->join('users', 'users.userID = sales.valeBy', 'left');
        $result = $builder->get()->getRow();

        return $this->response->setJSON($result);
    }

    public function getitem($itemID)
    {
        $builder = $this->db->table('items');
        $builder->where('itemID', $itemID);
        $result = $builder->get()->getRow();

        return $this->response->setJSON($result);
    }

    public function savevale()
    {
        $salesID     = $this->request->getPost('paySalesID');
        $referenceNo = $this->request->getPost('referenceNo');

        $paymentMethod = "Cash";
        if ($referenceNo) {
            $paymentMethod = "Gcash";
        }

        $data = array(
            'salesDate'     => date('Y-m-d H:i:s'),
            'paymentMethod' => $paymentMethod,
            'referenceNo'   => $referenceNo,
        );

        $builder1 = $this->db->table('sales');
        $builder1->set($data);
        $builder1->where('salesID', $salesID);
        $builder1->update();

        return redirect()->to($this->controller_page);
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

        // initilaize select
        $builder->select('sales.*');
        $builder->select('users.username');
        $builder->select('vale.username as valeName');
        $builder->join('users', 'users.userID = sales.userID', 'left');
        $builder->join('users as vale', 'vale.userID = sales.valeBy', 'left');

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
            $builder->where("(sales.dateCreated >= '$startDate' and sales.dateCreated <= '$endDate')");
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

        $title = 'SALES';

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
            $sheet->setCellValue('A' . $startCol, 'DATE CREATED');
            $sheet->setCellValue('b' . $startCol, 'ITEM');
            $sheet->setCellValue('c' . $startCol, 'COST');
            $sheet->setCellValue('d' . $startCol, 'QUANTITY');
            $sheet->setCellValue('e' . $startCol, 'PRICE');
            $sheet->setCellValue('f' . $startCol, 'AMOUNT');
            $sheet->setCellValue('g' . $startCol, 'VALE BY');
            $sheet->setCellValue('h' . $startCol, 'PAYMENT METHOD');
            $sheet->setCellValue('i' . $startCol, 'SALES DATE');
            $sheet->setCellValue('j' . $startCol, 'CASHIER');

            foreach ($records as $rec) {
                $sheet->setCellValue('A' . $rowCount, date('d/m/Y',  strtotime($rec->dateCreated)));
                $sheet->setCellValue('B' . $rowCount, $rec->description);
                $sheet->setCellValue('C' . $rowCount, $rec->itemCost);
                $sheet->setCellValue('D' . $rowCount, $rec->qty);
                $sheet->setCellValue('E' . $rowCount, $rec->price);
                $sheet->setCellValue('F' . $rowCount, $rec->amount);
                $sheet->setCellValue('G' . $rowCount, $rec->valeName);
                $sheet->setCellValue('H' . $rowCount, $rec->paymentMethod);
                $sheet->setCellValue('I' . $rowCount, date('d/m/Y',  strtotime($rec->dateCreated)));
                $sheet->setCellValue('j' . $rowCount, $rec->username);
                $rowCount++;
            }
            $sheet->setCellValue('e' . $rowCount, 'Total');
            $sheet->setCellValue('f' . $rowCount, '=SUM(f' . $rowStartCount . ':' . 'f' . $rowCount . ')');

            $sheet->getStyle('A' . $startCol . ':' . 'j' . $rowCount)->applyFromArray($insideBorderStyle);
            $sheet->getStyle('A' . $startCol . ':' . 'j' . $rowCount)->applyFromArray($outsideBorderStyle);
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
