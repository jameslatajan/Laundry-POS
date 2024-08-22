<?php

namespace App\Controllers;

use App\Libraries\Htmlhelper;

require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class Inventory extends BaseController
{
    protected $table;
    protected $pfield;
    protected $controller_page;

    public function __construct()
    {
        $this->data['module_title']    = 'INVENTORY';
        $this->data['module_desc']     = 'Description about transactions';
        $this->data['controller_page'] = $this->controller_page = site_url('inventory');
        $this->data['current_module']  = 'inventory';
        $this->data['current_menu']    = '';
        $this->table                   = 'items';
        $this->pfield                  = 'itemID';
    }

    public function inventory()
    {
        $data = $this->data;

        $condition_fields = array(
            array(
                'variable'      => 'description',
                'field'         => $this->table . '.description',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
        );

        // sorting fields
        $sorting_fields = array(
            'description' => 'desc',
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

                $sortby    = $this->request->getPost('sortby');
                $sortorder = $this->request->getPost('sortorder');
                break;
            case 2:
                foreach ($condition_fields as $key) {
                    ${$key['variable']} = $this->session->get($controller . '_' . $key['variable']);
                }

                $sortby    = $this->session->get($controller . '_sortby');
                $sortorder = $this->session->get($controller . '_sortorder');
                break;
            default:
                foreach ($condition_fields as $key) {
                    ${$key['variable']} = $key['default_value'];
                }

                $sortby    = "";
                $sortorder = "";
        }
        //end source of filtering

        $builder = $this->db->table($this->table); // initialize table

        //count total rows
        $builder->select('itemID');
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

        $this->session->set($controller . '_sortby', $sortby);
        $this->session->set($controller . '_sortorder', $sortorder);
        $this->session->set($controller . '_limit', $limit);

        // assign data variables for views
        foreach ($condition_fields as $key) {
            $data[$key['variable']] = ${$key['variable']};
        }

        //select
        $builder->select('itemID');
        $builder->select('description');
        $builder->select('price');
        $builder->select('cost');
        $builder->select('qty');

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

        // get
        // $data['records'] = $records =  $builder->get()->getResult();
        $data['ttl_rows'] = $ttl_rows =  $config['total_rows'] = $builder->countAllResults();

        // set pagination
        $data['pagination'] = $pagination = $this->pagination->makeLinks($page, $limit, $ttl_rows, 'custom_view');

        // initilaize select
        $builder->select('itemID');
        $builder->select('description');
        $builder->select('price');
        $builder->select('cost');
        $builder->select('qty');

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

        echo view('header', $data);
        echo view('pages/inventory/inventory');
        echo view('footer');
    }

    public function get_stockcard()
    {
        $startDate = $this->request->getPost('startDate');
        $endDate   = $this->request->getPost('endDate');
        $itemID    = $this->request->getPost('itemID');

        $builder1 = $this->db->table('items');
        $builder1->select('description');
        $builder1->where('itemID', $itemID);
        $rec = $builder1->get()->getRow();

        $builder = $this->db->table('stockcards');
        $builder->select('*');
        $builder->where('date >', $startDate . ' 00:00:00');
        $builder->where('date <', $endDate . ' 23:59:59');
        $builder->where('itemID', $itemID);
        $builder->orderBy('date', 'desc');
        $records = $builder->get()->getResult();

        $html = "";

        $html .= "
        <div class='row'>
        <div class='col-md-12'>
            <h3>ITEM NAME: {$rec->description}</h3>
            <table class='table table-hover table-collapsable table-transactions table-sm' id='stockTable'>
              <thead>
                <tr>
                  <th>SALES DATE</th>
                  <th>BEG BAL</th>
                  <th>DEBIT(+)</th>
                  <th>CREDIT(-)</th>
                  <th>END BAL</th>
                  <th>REF No.</th>
                  <th>REMARKS</th>
                </tr>
              </thead>
              <tbody id='stockcardtbody'>
            ";

        if ($records) {

            foreach ($records as $rec) {
                $salesDate = date('m/d/Y', strtotime($rec->date));


                $html .= "<tr>
                    <td style='font-size:15px'>$salesDate</td>
                    <td style='font-size:15px'>$rec->begBal</td>
                    <td style='font-size:15px'>$rec->debit</td>
                    <td style='font-size:15px'>$rec->credit</td>
                    <td style='font-size:15px'>$rec->endBal</td>
                    <td style='font-size:15px'>$rec->refNo</td>
                    <td style='font-size:15px'>$rec->remarks</td>
                </tr>";
            }
        } else {
            $html .= "<tr>
            <td style='font-size:15px; text-align:center' colspan='7'>No Data Found</td>
             </tr>";
        }

        $html .= "</tbody>
                </table>
        </div>";

        return $this->response->setJSON($html);
    }

    public function updateitem()
    {
        $itemID = $this->request->getPost('itemIDeditItem');
        $cost   = $this->request->getPost('costEditItem');
        $price  = $this->request->getPost('priceEditItem');


        $data = array(
            'cost'  => $cost,
            'price' => $price
        );

        $builder = $this->db->table('items');
        $builder->set($data);
        $builder->where('itemID', $itemID);
        $builder->update();

        return redirect()->to($this->controller_page);
    }

    public function updatestock()
    {
        $itemID = $this->request->getPost('itemIDeditStock');
        $qty    = $this->request->getPost('qty');

        $builder = $this->db->table('stockcards');
        $builder->select('endBal, itemID');
        $builder->where('itemID', $itemID);
        $builder->orderBy('stockID', 'desc');
        $builder->limit(1);
        $getEndBalStock = $builder->get()->getRow();

        $endbal = 0;
        if ($getEndBalStock) {
            $endbal =  $getEndBalStock->endBal;
        }

        $newbal = $endbal + $qty;

        $stockdata = array(
            'date'       => date('Y-m-d H:i:s'),
            'itemID'     => $itemID,
            'begBal'     => $endbal,
            'endBal'     => $newbal,
            'debit'      => $qty,
            'remarks'    => 'ADD INVENTORY',
            'insertedBy' => $this->current_user,
        );

        if (!$builder->insert($stockdata)) {
            // Get the inserted ID using the database connection instance
            // $ID = $this->db->insertID();

            $error = $this->db->error();
            echo "Error: Unable to insert data into the database. Code: {$error['code']}, Message: {$error['message']}";
        }

        $builder2 = $this->db->table('items');
        $builder2->select('qty');
        $builder2->where('itemID', $itemID);
        $getItem = $builder2->get()->getRow();

        $newItemQty = $qty + $getItem->qty;

        $itemData = array(
            'qty' =>     $newItemQty,
        );

        $builder2->set($itemData);
        $builder2->where('itemID', $itemID);

        if (!$builder2->update()) {
            echo "Error: Unable to insert data into the database. Code: {$error['code']}, Message: {$error['message']}";
        }

        return redirect()->to($this->controller_page);
    }

    public function saveitem()
    {
        $description = $this->request->getPost('saveItemName');
        $cost        = $this->request->getPost('saveCost');
        $price       = $this->request->getPost('savePrice');
        $qty         = $this->request->getPost('saveQty');

        $data = array(
            'description' => $description,
            'cost'        => $cost,
            'price'       => $price,
            'qty'         => $qty
        );

        $builder = $this->db->table('items');

        if (!$builder->insert($data)) {
            $error = $this->db->error();
            echo "Error: Unable to insert data into the database. Code: {$error['code']}, Message: {$error['message']}";
        } else {
            echo $ID = $this->db->insertID();
            $builder->where('itemID', $ID);
            $rec = $builder->get()->getRow();

            $stockdata = array(
                'date'       => date('Y-m-d H:i:s'),
                'itemID'     => $ID,
                'begBal'     => 0,
                'endBal'     => $rec->qty,
                'debit'      => $rec->qty,
                'remarks'    => 'NEW ITEM',
                'insertedBy' => $this->current_user,
            );

            $builder2 = $this->db->table('stockcards');

            if (!$builder2->insert($stockdata)) {
                $error = $this->db->error();
                echo "Error: Unable to insert data into the database. Code: {$error['code']}, Message: {$error['message']}";
            }
        }

        return redirect()->to($this->controller_page);
    }

    public function printlist()
    {
        $data = $this->data;

        $condition_fields = array(
            array(
                'variable'      => 'description',
                'field'         => $this->table . '.description',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
        );

        // sorting fields
        $sorting_fields = array(
            'description' => 'desc',
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
        $data['HtmlHelper'] = new Htmlhelper();
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
                'variable'      => 'description',
                'field'         => $this->table . '.description',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
        );

        // sorting fields
        $sorting_fields = array(
            'description' => 'desc',
        );

        //get the controller
        $controller = service('uri')->getSegment(1);

        foreach ($condition_fields as $key) {
            ${$key['variable']} = $this->session->get($controller . '_' . $key['variable']);
        }

        $limit     = $this->session->get($controller . '_limit');
        $offset    = $this->session->get($controller . '_offset');
        $sortby    = $this->session->get($controller . '_sortby');
        $sortorder = $this->session->get($controller . '_sortorder');

        // select
        $builder = $this->db->table($this->table);

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

        $title = 'INVENTORY';

        $filename    =   $title . '-' . date('mdYHi') . '.xlsx';
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A2', 'LABACHINE LAUNDRY LOUNGE');
        $sheet->setCellValue('A3',  $title);
        $sheet->mergeCells("A2:D2");
        $sheet->mergeCells("A3:D3");

        $sheet->getColumnDimension('A')->setWidth(12);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(10);

        $sheet->mergeCells('A2:D2');
        $sheet->mergeCells('A3:D3');

        $sheet->getStyle('A2:D2')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A3:D3')->getAlignment()->setHorizontal('center');

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
            $sheet->setCellValue('A' . $startCol, 'DESCRIPTION');
            $sheet->setCellValue('b' . $startCol, 'PRICE');
            $sheet->setCellValue('c' . $startCol, 'COST');
            $sheet->setCellValue('d' . $startCol, 'QTY ON HAND');

            foreach ($records as $rec) {
                $sheet->setCellValue('A' . $rowCount, $rec->description);
                $sheet->setCellValue('B' . $rowCount, $rec->price);
                $sheet->setCellValue('C' . $rowCount, $rec->cost);
                $sheet->setCellValue('D' . $rowCount, $rec->qty);
                $rowCount++;
            }
            $sheet->getStyle('A' . $startCol . ':' . 'd' . $rowCount)->applyFromArray($insideBorderStyle);
            $sheet->getStyle('A' . $startCol . ':' . 'd' . $rowCount)->applyFromArray($outsideBorderStyle);
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
