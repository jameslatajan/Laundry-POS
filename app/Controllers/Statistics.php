<?php

namespace App\Controllers;

use App\Models\Detailed_dsr;
use App\Models\transactions;
use App\Models\JobOrders;
use App\Models\users;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class Statistics extends BaseController
{
    protected $data;
    protected $module;
    protected $table;
    protected $pfield;
    protected $controller_page;

    public function __construct()
    {
        $this->data['module_title']    = 'SALES';
        $this->data['module_desc']     = 'Description about transactions';
        $this->data['controller_page'] = $this->controller_page = site_url('sales');
        $this->data['current_module']  = 'statistics';
        $this->data['current_menu']    = '';
        $this->table                   = 'transactions';
        $this->pfield                  = 'transID';
    }

    public function getJobOrder()
    {
        $data = $this->data;

        $condition_fields = array(
            array(
                'variable'      => 'year',
                'field'         => "sales_date",
                'default_value' => date('Y'),
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'month',
                'field'         => 'sales_date',
                'default_value' => date('m'),
                'operator'      => 'like_both'
            ),
        );

        //get the controller
        $controller = service('uri')->getSegment(2);

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

        // set session variables
        foreach ($condition_fields as $key) {
            $this->session->set($controller . '_' . $key['variable'], ${$key['variable']});
        }


        //--------------- start regular
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $date        = $year . '-' . $month;
        $builder     = $this->db->table('transactions');

        $builder->select('customer, dateCreated, tranType');
        $builder->like('dateCreated', $date, 'after');
        $builder->whereIn('tranType', ['Regular', 'DIY Regular']);
        $builder->where('status !=', 0);
        $builder->orderBy('dateCreated', 'ASC');
        $findAllReg = $builder->get()->getResult();

        $builder->select('customer, dateCreated, tranType');
        $builder->like('dateCreated', $date, 'after');
        $builder->whereIn('tranType', ['Regular', 'DIY Regular']);
        $builder->where('status !=', 0);
        $builder->orderBy('dateCreated', 'ASC');
        $noReg = $builder->countAllResults();

        $regular = array();
        foreach ($findAllReg as $dateStr) {
            $day  = intval(date('d',  strtotime($dateStr->dateCreated)));
            // Check if the day exists in the customerCounts array
            if (!isset($regular[$day])) {
                $regular[$day] = 0;  // Initialize the count to 0
            }
            $regular[$day]++;  // Increment the count for the day
        }

        // align days to regular
        $temReg = array();
        for ($i = 1; $i <= $daysInMonth; $i++) {
            if (array_key_exists($i, $regular)) {
                $temReg[$i] = $regular[$i];
            } else {
                $temReg[$i] = 0;
            }
        }

        // convert array to index start 0 days to regular
        $finalReg = array();
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $finalReg[] = $temReg[$i];
        }
        //--------------- end regular


        //--------------- start students
        $builder->select('customer, dateCreated, tranType');
        $builder->like('dateCreated', $date, 'after');
        $builder->whereIn('tranType', ['Student', 'DIY Student']);
        $builder->where('status !=', 0);
        $builder->orderBy('dateCreated', 'ASC');
        $findAllStud = $builder->get()->getResult();

        $builder->select('customer, dateCreated, tranType');
        $builder->like('dateCreated', $date, 'after');
        $builder->whereIn('tranType', ['Student', 'DIY Student']);
        $builder->where('status !=', 0);
        $builder->orderBy('dateCreated', 'ASC');
        $noStud = $builder->countAllResults();

        $student = array();
        foreach ($findAllStud as $dateStr) {
            $day  = intval(date('d',  strtotime($dateStr->dateCreated)));
            // Check if the day exists in the customerCounts array
            if (!isset($student[$day])) {
                $student[$day] = 0;  // Initialize the count to 0
            }
            $student[$day]++;  // Increment the count for the day
        }

        // align days to student
        $tempStud = array();
        for ($i = 1; $i <= $daysInMonth; $i++) {
            if (array_key_exists($i, $student)) {
                $tempStud[$i] = $student[$i];
            } else {
                $tempStud[$i] = 0;
            }
        }

        // convert array to index start 0 days to student
        $finalStud = array();
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $finalStud[] = $tempStud[$i];
        }
        //--------------- end students

        // array of days for labels
        $days = array();
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $days[] = $i;
        }


        $months = array(
            "January", "February", "March", "April",
            "May", "June", "July", "August",
            "September", "October", "November", "December"
        );

        $builder->select('YEAR(dateCreated) as year');
        $builder->distinct();
        $years = $builder->get()->getResult();

        $data['days']    = $days;
        $data['year']    = $year;
        $data['years']   = $years;
        $data['mon']     = $month;
        $data['months']  = $months;
        $data['regular'] = $finalReg;
        $data['noReg']   = $noReg;
        $data['student'] = $finalStud;
        $data['noStud']  = $noStud;
        $data['title']   = 'Job Orders';

        echo view('header', $data);
        echo view('pages/statistics/jobOrders');
        echo view('footer');
    }

    public function totalSales()
    {
        $data = $this->data;

        $condition_fields = array(
            array(
                'variable'      => 'year',
                'field'         => "sales_date",
                'default_value' => date('Y'),
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'month',
                'field'         => 'sales_date',
                'default_value' => date('m'),
                'operator'      => 'like_both'
            ),
        );

        //get the controller
        $controller = service('uri')->getSegment(2);

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

        // set session variables
        foreach ($condition_fields as $key) {
            $this->session->set($controller . '_' . $key['variable'], ${$key['variable']});
        }

        //end source of filtering
        $builder = $this->db->table('transactions');

        $builder->select('YEAR(dateCreated) as year');
        $builder->distinct();
        $years = $builder->get()->getResult();

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $date        = $year . '-' . $month;

        $builder->select('dateCreated, totalAmount');
        $builder->like('dateCreated', $date, 'after');
        $builder->where('status !=', 0);
        $builder->orderBy('dateCreated', 'ASC');
        $findAll = $builder->get()->getResult();

        $builder->select('SUM(totalAmount) as total');
        $builder->like('dateCreated', $date, 'after');
        $builder->where('status !=', 0);
        $totalSum = $builder->get()->getRow();

        $totalAmount = array();
        foreach ($findAll as $dateStr) {
            $day  = intval(date('d',  strtotime($dateStr->dateCreated)));

            // Check if the day exists in the totalAmounts array
            if (!isset($totalAmount[$day])) {
                $totalAmount[$day] = 0;  // Initialize the total amount to 0
            }

            // Sum the amount for the day
            $totalAmount[$day] += $dateStr->totalAmount;
        }

        // align days to sales
        $temRec = array();
        for ($i = 1; $i <= $daysInMonth; $i++) {
            if (array_key_exists($i, $totalAmount)) {
                $temRec[$i] = $totalAmount[$i];
            } else {
                $temRec[$i] = 0;
            }
        }

        // convert array to index start 0 days to sales
        $records = array();
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $records[] = $temRec[$i];
        }

        // array of dasy for labels
        $days = array();
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $days[] = $i;
        }

        $months = array(
            "January", "February", "March", "April",
            "May", "June", "July", "August",
            "September", "October", "November", "December"
        );


        $data['days']        = $days;
        $data['totalSum']    = number_format($totalSum->total, 2);
        $data['mon']         = $month;
        $data['year']        = $year;
        $data['years']       = $years;
        $data['months']      = $months;
        $data['totalAmount'] = $records;
        $data['title']       = 'Total Sales';

        echo view('header', $data);
        echo view('pages/statistics/totalSales');
        echo view('footer');
    }

    public function varianceReport()
    {
        $detailed_dsr  = new Detailed_dsr();

        $data['data']        = $this->header();

        $condition_fields = array(
            array(
                'variable'      => 'year',
                'field'         => "sales_date",
                'default_value' => "",
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'month',
                'field'         => 'sales_date',
                'default_value' => "date('m')",
                'operator'      => 'like_both'
            ),
        );

        //get the controller
        $controller = service('uri')->getSegment(2);

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

        // set session variables
        foreach ($condition_fields as $key) {
            $this->session->set($controller . '_' . $key['variable'], ${$key['variable']});
        }

        $date     = "";
        $variance = array();
        if ($year != "" && $month != "") {
            $date = $year . '-' . $month;

            $detailed_dsr->select('Date(sales_date) as sales_date');
            $detailed_dsr->like("date(sales_date)", $date);
            $detailed_dsr->orderBy('sales_date', 'asc');
            $detailed_dsr->distinct();
            $dsrDates = $detailed_dsr->get()->getResult();


            foreach ($dsrDates as $dsr) {
                $sales_date = $dsr->sales_date;

                $detailed_dsr->select('variance');
                $detailed_dsr->like("sales_date", $dsr->sales_date);
                $detailed_dsr->where("shift", 1);
                $shift1 = $detailed_dsr->get()->getRow();

                $detailed_dsr->select('variance');
                $detailed_dsr->like("sales_date", $dsr->sales_date);
                $detailed_dsr->where("shift", 2);
                $shift2 = $detailed_dsr->get()->getRow();

                if ($shift1) {
                    $shift1 = $shift1->variance;
                }

                if ($shift2) {
                    $shift2 = $shift2->variance;
                }

                $variance[] = array(
                    'sales_date' => $sales_date,
                    'shift1'     => $shift1,
                    'shift2'     => $shift2,
                );
            }
        }

        $months = array(
            "January", "February", "March", "April",
            "May", "June", "July", "August",
            "September", "October", "November", "December"
        );

        $detailed_dsr->select('YEAR(sales_date) as year');
        $detailed_dsr->distinct();
        $years = $detailed_dsr->get()->getResult();

        //modify code here
        $data['months']   = $months;
        $data['mon']      = $month;
        $data['years']    = $years;
        $data['year']     = $year;
        $data['variance'] = $variance;

        echo view('header', $data);
        echo view('pages/statistics/varianceReport');
        echo view('footer');
    }

    public function productivityReport()
    {
        $data = $this->data;

        $users         = new users();
        $joborders     = new JobOrders();

        $data['data']        = $this->header();

        $condition_fields = array(
            array(
                'variable'      => 'startDate',
                'field'         => "dateCreated",
                'default_value' => "",
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'endDate',
                'field'         => 'dateCreated',
                'default_value' => "",
                'operator'      => 'like_both'
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

        // set session variables
        foreach ($condition_fields as $key) {
            $this->session->set($controller . '_' . $key['variable'], ${$key['variable']});
        }

        $users->select('userID, firstName, lastName, username');
        $users->orderBy('firstName', 'asc');
        $users->where('userType !=', 'Admin');
        $users->where('userType !=', 'Staff');
        $usersList = $users->get()->getResult();

        $productivity = array();
        if ($startDate && $endDate) {
            foreach ($usersList as $user) {
                $userName = strtoupper($user->firstName);

                $joborders->select('userID');
                $joborders->where('washBy', $user->userID);
                $joborders->where('washDate >', $startDate);
                $joborders->where('washDate <', $endDate);
                $wash = $joborders->countAllResults();

                $joborders->select('userID');
                $joborders->where('dryBy', $user->userID);
                $joborders->where('dryDate >', $startDate);
                $joborders->where('dryDate <', $endDate);
                $dry = $joborders->countAllResults();

                $joborders->select('userID');
                $joborders->where('foldBy', $user->userID);
                $joborders->where('foldDate >', $startDate);
                $joborders->where('foldDate <', $endDate);
                $fold = $joborders->countAllResults();

                // Create an associative array for each user
                $productivity[] = array(
                    'userName' => $userName,
                    'wash'     => $wash,
                    'dry'      => $dry,
                    'fold'     => $fold
                );
            }
        }


        // Insertion sort based on 'fold'
        for ($i = 1; $i < count($productivity); $i++) {
            $key = $productivity[$i];
            $j   = $i - 1;

            // Move elements of $productivity[0..$i-1] that are greater than $key['fold']
            // to one position ahead of their current position
            while ($j >= 0 && $productivity[$j]['fold'] < $key['fold']) {
                $productivity[$j + 1] = $productivity[$j];
                $j                    = $j - 1;
            }

            // Place $key at its correct position in $productivity
            $productivity[$j + 1] = $key;
        }
        // Assign the sorted $productivity array to the 'productivity' key in the $data array
        $data['productivity'] = $productivity;
        $data['startDate']    = $startDate;
        $data['endDate']      = $endDate;
        $data['title']        = 'Productivity';

        echo view('header', $data);
        echo view('pages/statistics/productivityReport');
        echo view('footer');
    }

    public function statisticsReport()
    {
        $transaction  = new transactions();

        $data = $this->data;

        $condition_fields = array(
            array(
                'variable'      => 'year',
                'field'         => "sales_date",
                'default_value' => "",
                'operator'      => 'like_both'
            ),
        );

        //get the controller
        $controller = service('uri')->getSegment(2);

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

        // set session variables
        foreach ($condition_fields as $key) {
            $this->session->set($controller . '_' . $key['variable'], ${$key['variable']});
        }



        $records = array();
        if ($year) {
            // i want to get the number 
            $transaction->select('tranType');
            $transaction->select('dateCreated');
            $transaction->select('totalAmount');
            $transaction->select('status');
            $transaction->where('status !=', 0);
            $transaction->like('dateCreated', $year);
            $transaction->orderBy('dateCreated');
            $record = $transaction->get()->getResult();

            $months = array(
                'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'December'
            );

            $summary = array();
            foreach ($months as $month) {
                $summary[$month] = array(
                    'customers'    => 0,
                    'regular_jo'   => 0,
                    'student_jo'   => 0,
                    'express_jo'   => 0,
                    'total_jo'     => 0,
                    'total_loads'  => 0,
                    'total_amount' => 0,
                );
            }

            foreach ($record as $rec) {
                $month = date('F', strtotime($rec->dateCreated));
                if (isset($summary[$month])) {
                    // Increment the corresponding transaction type count and total count
                    if ($rec->tranType == 'Regular' || $rec->tranType == 'DIY Regular') {
                        $summary[$month]['regular_jo']++;
                    } else if ($rec->tranType == 'Student' || $rec->tranType == 'DIY Student') {
                        $summary[$month]['student_jo']++;
                    } else {
                        $summary[$month]['express_jo']++;
                    }

                    $summary[$month]['total_amount'] += $rec->totalAmount;
                }
            }

            foreach ($months as $month) {
                $tmon = date('m', strtotime($month));
                $transaction->selectSum('totalLoads');
                $transaction->where('status !=', 0);
                $transaction->like('dateCreated', $year . '-' . $tmon, 'after');
                $no = $transaction->get()->getRow();

                if (isset($summary[$month])) {
                    $summary[$month]['total_loads'] = $no->totalLoads;
                }
            }

            foreach ($months as $month) {
                $tmon = date('m', strtotime($month));
                $transaction->select('totalLoads');
                $transaction->where('status !=', 0);
                $transaction->like('dateCreated', $year . '-' . $tmon, 'after');
                $no = $transaction->countAllResults();

                if (isset($summary[$month])) {
                    $summary[$month]['total_jo'] = $no;
                }
            }

            foreach ($months as $month) {
                $tmon = date('m', strtotime($month));
                $transaction->select('customer');
                $transaction->where('status !=', 0);
                $transaction->like('dateCreated', $year . '-' . $tmon, 'after');
                $transaction->distinct();
                $no = $transaction->countAllResults();

                if (isset($summary[$month])) {
                    $summary[$month]['customers'] = $no;
                }
            }

            $records = $summary;
        }

        $transaction->select('YEAR(dateCreated) as year');
        $transaction->distinct();
        $years = $transaction->get()->getResult();

        $data['records'] = $records;
        $data['years']   = $years;
        $data['year']    = $year;
        $data['title']   = 'Monthly Sales';

        echo view('header', $data);
        echo view('pages/statistics/statisticsReport');
        echo view('footer');
    }

    public function statisticsReport2()
    {
        $transaction  = new transactions();

        $data = $this->data;

        $condition_fields = array(
            array(
                'variable'      => 'year',
                'field'         => "sales_date",
                'default_value' => "",
                'operator'      => 'like_both'
            ),
        );

        //get the controller
        $controller = service('uri')->getSegment(2);

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

        // set session variables
        foreach ($condition_fields as $key) {
            $this->session->set($controller . '_' . $key['variable'], ${$key['variable']});
        }
    }

    public function performanceReport()
    {
        $data = $this->data;

        $joborders     = new JobOrders();
        $transaction   = new transactions();

        $data['data']        = $this->header();

        $condition_fields = array(
            array(
                'variable'      => 'date',
                'field'         => "sales_date",
                'default_value' => "0000-00-00",
                'operator'      => 'like_both'
            ),
        );

        //get the controller
        $controller = service('uri')->getSegment(2);

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

        // set session variables
        // foreach ($condition_fields as $key) {
        //     $this->session->set($controller . '_' . $key['variable'], ${$key['variable']});
        // }

        $data['date'] = $date;

        $records  = array();  // Initialize the array to store counts
        $timelist = array();
        if ($date != '0000-00-00') {
            for ($hour = 0; $hour <= 23; $hour++) {
                $formattedHour = ($hour % 12 == 0) ? 12 : $hour % 12;
                $period        = ($hour < 12) ? 'AM' : 'PM';
                $time          = sprintf('%02d %s', $formattedHour, $period);

                $transaction->select('SUM(totalLoads) as totalLoads');
                $transaction->like('dateCreated', $date . " " . str_pad($hour, 2, '0', STR_PAD_LEFT), 'after');  // Convert date string to MySQL date format and use WHERE clause to filter by date
                $recieve           = $transaction->get()->getRow();                                              // Count the records for the current hour

                $joborders->select('washDate');
                $joborders->like('washDate', $date . " " . str_pad($hour, 2, '0', STR_PAD_LEFT), 'after');  // Convert date string to MySQL date format and use WHERE clause to filter by date
                $wash            = $joborders->countAllResults();                                           // Count the records for the current hour

                $joborders->select('dryDate');
                $joborders->like('dryDate', $date . " " . str_pad($hour, 2, '0', STR_PAD_LEFT), 'after');  // Convert date string to MySQL date format and use WHERE clause to filter by date
                $dry             = $joborders->countAllResults();                                          // Count the records for the current hour

                $joborders->select('foldDate');
                $joborders->like('foldDate', $date . " " . str_pad($hour, 2, '0', STR_PAD_LEFT), 'after');
                $fold = $joborders->countAllResults();  // Count the records for the current hour

                $transaction->select('SUM(totalLoads) as totalLoads');
                $transaction->like('dateReleased', $date . " " . str_pad($hour, 2, '0', STR_PAD_LEFT), 'after');
                $release = $transaction->get()->getRow();  // Count the records for the current hour

                $totalRecieve = 0;
                $totalRelease = 0;
                if ($recieve->totalLoads) {
                    $totalRecieve = $recieve->totalLoads;
                }

                if ($release->totalLoads) {
                    $totalRelease = $release->totalLoads;
                }

                $records[] = array(
                    'time'    => $time,
                    'recieve' => $totalRecieve,
                    'wash'    => $wash,
                    'dry'     => $dry,
                    'fold'    => $fold,
                    'release' => $totalRelease,
                );
            }

            $timelist = [];
            for ($hour = 0; $hour <= 23; $hour++) {
                $formattedHour = ($hour % 12 == 0) ? 12 : $hour % 12;
                $period        = ($hour < 12) ? 'AM' : 'PM';
                $timelist[]    = sprintf('%02d %s', $formattedHour, $period);
            }
        }

        $data['records']  = $records;
        $data['timelist'] = $timelist;
        $data['title']    = 'Daily Performance';

        echo view('header', $data);
        echo view('pages/statistics/performanceReport');
        echo view('footer');
    }

    public function performacestat()
    {
        $data = $this->data;

        $condition_fields = array(
            array(
                'variable'      => 'year',
                'field'         => "sales_date",
                'default_value' => "",
                'operator'      => 'like_both'
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

        // set session variables
        // foreach ($condition_fields as $key) {
        //     $this->session->set($controller . '_' . $key['variable'], ${$key['variable']});
        // }


        $personel     = array();
        if ($year) {
            // personel
            $builder = $this->db->table('users');
            $builder->select('username');
            $builder->select('userID');
            $builder->select('firstName');
            $builder->where('userType !=', 'Admin');
            $builder->where('userType !=', 'Staff');
            $builder->orderBy('username', 'asc');
            $userlist = $builder->get()->getResult();

            for ($month = 1; $month <= 12; $month++) {
                $date = date('Y-m', strtotime($year . '-' .  $month));
                // $firstDayOfMonth = sprintf('%04d-%02d-01', $year, $month);

                foreach ($userlist as $user) {
                    $builder = $this->db->table('job_orders');
                    $builder->select('washDate');
                    $builder->where('washBy', $user->userID);
                    $builder->like('washDate', $date, 'after');
                    $wash = $builder->countAllResults();

                    $builder = $this->db->table('job_orders');
                    $builder->select('foldDate');
                    $builder->where('foldBy', $user->userID);
                    $builder->like('foldDate', $date, 'after');
                    $fold = $builder->countAllResults();

                    $builder = $this->db->table('job_orders');
                    $builder->select('dryDate');
                    $builder->where('dryBy', $user->userID);
                    $builder->like('dryDate', $date, 'after');
                    $dry = $builder->countAllResults();

                    $personel[strtoupper($user->username)][] = array(
                        'year'  => $year,
                        'month' => $month,
                        'wash'  => $wash,
                        'dry'   => $dry,
                        'fold'  => $fold,
                    );
                }
            }
        }

        $data['year']     = $year;
        $data['personel'] = $personel;
        $data['title']    = 'Performance Stat';

        echo view('header', $data);
        echo view('pages/statistics/performancestat');
        echo view('footer');
    }

    public function expressOrders()
    {
        $year = isset($_GET['year']) ? $_GET['year'] : "";

        $records = array();

        $data['data']        = $this->header();

        if ($year != "") {
            $monthNames = [
                'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'December'
            ];

            for ($month = 1; $month <= 12; $month++) {
                $format = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);

                $builder = $this->db->table('transactions');

                // Express Regular
                $regular = $builder->like('dateCreated', $format, 'after')
                    ->where('tranType', 'Express Regular')
                    ->countAllResults();

                $regularAmt = $builder->select('SUM(amountPaid) as total')
                    ->like('dateCreated', $format, 'after')
                    ->where('tranType', 'Express Regular')
                    ->get()->getRow();

                // Express Student
                $student = $builder->like('dateCreated', $format, 'after')
                    ->where('tranType', 'Express Student')
                    ->countAllResults();

                $studentAmt = $builder->select('SUM(amountPaid) as total')
                    ->like('dateCreated', $format, 'after')
                    ->where('tranType', 'Express Student')
                    ->get()->getRow();

                // Store in records array
                $records[$format] = [
                    'month'   => $monthNames[$month - 1],
                    'student' => $student,
                    'regular' => $regular,
                    'total'   => $studentAmt->total + $regularAmt->total
                ];
            }
        }

        $data['records'] = $records;
        $data['year']    = $year;

        echo view('header', $data);
        echo view('pages/statistics/expressOrders');
        echo view('footer');
    }

    public function dsrSummary()
    {
        $data = $this->data;

        $condition_fields = array(
            array(
                'variable'      => 'year',
                'field'         => "sales_date",
                'default_value' => "",
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'month',
                'field'         => 'sales_date',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
        );

        //get the controller
        $controller = service('uri')->getSegment(2);

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

        // set session variables
        foreach ($condition_fields as $key) {
            $this->session->set($controller . '_' . $key['variable'], ${$key['variable']});
        }


        $months = array(
            "January", "February", "March", "April",
            "May", "June", "July", "August",
            "September", "October", "November", "December"
        );

        //end source of filtering
        $builder = $this->db->table('detailed_dsr');
        $builder->select('YEAR(sales_date) as year');
        $builder->distinct();
        $years = $builder->get()->getResult();

        $sales_date = $year . '-' . $month;

        $records = array();
        if ($year && $month) {
            $builder->select('detailed_dsr.*');
            $builder->select('users.username, users.userID');
            $builder->join('users', 'users.userID = detailed_dsr.userID');
            $builder->like('sales_date', $sales_date, 'after');
            $records = $builder->get()->getResult();
        }

        $data['year']       = $year;
        $data['years']      = $years;
        $data['mon']        = $month;
        $data['months']     = $months;
        $data['records']    = $records;

        $data['title'] = 'DSR Summary';

        echo view('header', $data);
        echo view('pages/statistics/dsrSummary');
        echo view('footer');
    }

    public function exportlist()
    {
        $data['data'] = $this->header();

        $condition_fields = array(
            array(
                'variable'      => 'year',
                'field'         => "sales_date",
                'default_value' => "",
                'operator'      => 'like_both'
            ),
            array(
                'variable'      => 'month',
                'field'         => 'sales_date',
                'default_value' => "",
                'operator'      => 'like_both'
            ),
        );

        //get the controller
        $controller = service('uri')->getSegment(2);

        foreach ($condition_fields as $key) {
            ${$key['variable']} = $this->session->get($controller . '_' . $key['variable']);
        }
        //end source of filtering
        $sales_date = $year . '-' . $month;

        $records = array();
        $builder = $this->db->table('detailed_dsr');
        if ($year && $month) {
            $builder->select('detailed_dsr.*');
            $builder->select('users.username, users.userID');
            $builder->join('users', 'users.userID = detailed_dsr.userID');
            $builder->like('sales_date', $sales_date, 'after');
            $records = $builder->get()->getResult();
        }

        $title = 'DSR-SUMMARY';

        $filename    =   $title . '-' . date('mdYHi') . '.xlsx';
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('d2', 'LABACHINE LAUNDRY LOUNGE');
        $sheet->setCellValue('d3',  $title);
        $sheet->mergeCells("d2:O2");
        $sheet->mergeCells("d3:O3");

        $sheet->getColumnDimension('A')->setWidth(12);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(12);

        $sheet->getStyle('d2:O2')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('d3:O3')->getAlignment()->setHorizontal('center');

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
        $rowCount      = 7;

        if ($records) {
            $sheet->setCellValue('A' . $startCol, 'SALES DATE');
            $sheet->mergeCells('A' . $startCol . ':' . 'A' . $startCol + 1);
            $sheet->setCellValue('b' . $startCol, 'CASHIER');
            $sheet->mergeCells('b' . $startCol . ':' . 'b' . $startCol + 1);

            $sheet->setCellValue('C' . $startCol, 'SALES');

            $sheet->setCellValue('C' . $startCol + 1, 'CASH');
            $sheet->setCellValue('D' . $startCol + 1, 'GCASH');
            $sheet->setCellValue('E' . $startCol + 1, 'UNPAID');
            $sheet->setCellValue('F' . $startCol + 1, 'TOTAL');
            $sheet->mergeCells('C' . $startCol . ':' . 'F' . $startCol);
            $sheet->getStyle('C' . $startCol)->getAlignment()->setHorizontal('center');

            $sheet->setCellValue('G' . $startCol, 'COLLECTION');

            $sheet->setCellValue('G' . $startCol + 1, 'CASH');
            $sheet->setCellValue('H' . $startCol + 1, 'GCASH');
            $sheet->setCellValue('I' . $startCol + 1, 'TOTAL');
            $sheet->mergeCells('G' . $startCol . ':' . 'I' . $startCol);
            $sheet->getStyle('G' . $startCol)->getAlignment()->setHorizontal('center');

            $sheet->setCellValue('J' . $startCol, 'ITEM');

            $sheet->setCellValue('J' . $startCol + 1, 'CASH');
            $sheet->setCellValue('K' . $startCol + 1, 'GCASH');
            $sheet->setCellValue('L' . $startCol + 1, 'TOTAL');
            $sheet->mergeCells('J' . $startCol . ':' . 'L' . $startCol);
            $sheet->getStyle('J' . $startCol)->getAlignment()->setHorizontal('center');

            $sheet->setCellValue('M' . $startCol, 'TOTAL CASH');
            $sheet->mergeCells('M' . $startCol . ':' . 'M' . $startCol + 1);
            $sheet->setCellValue('N' . $startCol, 'TOTAL GCASH');
            $sheet->mergeCells('N' . $startCol . ':' . 'N' . $startCol + 1);
            $sheet->setCellValue('O' . $startCol, 'EXPENSES');
            $sheet->mergeCells('O' . $startCol . ':' . 'O' . $startCol + 1);
            $sheet->setCellValue('P' . $startCol, 'REMITTANCE');
            $sheet->mergeCells('P' . $startCol . ':' . 'P' . $startCol + 1);
            $sheet->setCellValue('Q' . $startCol, 'VARIANCE');
            $sheet->mergeCells('Q' . $startCol . ':' . 'Q' . $startCol + 1);
            $sheet->setCellValue('R' . $startCol, 'SETTLED AMOUNT');
            $sheet->mergeCells('R' . $startCol . ':' . 'R' . $startCol + 1);
            $sheet->setCellValue('S' . $startCol, 'DATE SETTLED');
            $sheet->mergeCells('S' . $startCol . ':' . 'S' . $startCol + 1);

            foreach ($records as $rec) {
                $dateSettled = "";
                if ($rec->dateSettled != '0000-00-00 00:00:00') {
                    $dateSettled = date('d/m/Y',  strtotime($rec->dateSettled));
                }

                $sheet->setCellValue('a' . $rowCount, date('d/m/Y',  strtotime($rec->sales_date)));
                $sheet->setCellValue('b' . $rowCount, $rec->username);
                $sheet->setCellValue('C' . $rowCount, $rec->ds_cash);
                $sheet->setCellValue('D' . $rowCount, $rec->ds_gcash);
                $sheet->setCellValue('E' . $rowCount, $rec->ds_unpaid);
                $sheet->setCellValue('F' . $rowCount, $rec->ds_total);
                $sheet->setCellValue('G' . $rowCount, $rec->col_cash);
                $sheet->setCellValue('H' . $rowCount, $rec->col_gcash);
                $sheet->setCellValue('I' . $rowCount, $rec->col_total);
                $sheet->setCellValue('j' . $rowCount, $rec->item_cash);
                $sheet->setCellValue('k' . $rowCount, $rec->item_gcash);
                $sheet->setCellValue('l' . $rowCount, $rec->item_total);
                $sheet->setCellValue('M' . $rowCount, $rec->total_cash);
                $sheet->setCellValue('n' . $rowCount, $rec->total_gcash);
                $sheet->setCellValue('o' . $rowCount, $rec->total_expenses);
                $sheet->setCellValue('q' . $rowCount, $rec->variance);
                $sheet->setCellValue('r' . $rowCount, $rec->varSettledAmt);
                $sheet->setCellValue('s' . $rowCount, $dateSettled);
                $rowCount++;
            }

            $sheet->getStyle('A' . $startCol . ':' . 's' . $rowCount)->applyFromArray($insideBorderStyle);
            $sheet->getStyle('A' . $startCol . ':' . 's' . $rowCount)->applyFromArray($outsideBorderStyle);
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
