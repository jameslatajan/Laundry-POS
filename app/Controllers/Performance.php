<?php

namespace App\Controllers;

class Performance extends BaseController
{
    protected $data;
    protected $table;
    protected $pfield;
    protected $controller_page;
    protected $curr_date;

    public function __construct()
    {
        $this->data['module_title'] = 'PERFORMANCE';
        $this->data['module_desc']  = 'Description about transactions';
        $this->data['data']         = $this->header();
        $this->table                = 'transactions';
        $this->pfield               = 'transID';
        $this->controller_page      = $this->data['controller_page'] = site_url('unpaid');
        $this->curr_date            = date('Y-m-d');
        // $this->curr_date            = '2024-01-11';
        // $current_date = '2024-01-11';

    }

    public function getPerformance()
    {
        $current_date = date('Y-m-d', strtotime($this->curr_date));

        $joborders     = $this->db->table('job_orders');
        $users         = $this->db->table('users');
        $builderPoints = $this->db->table('config');

        $builderPoints->select('name, value');
        $builderPoints->where('name', 'Wash Points');
        $washpoints = $builderPoints->get()->getRow();

        $builderPoints->select('name, value');
        $builderPoints->where('name', 'Dry Points');
        $drypoints = $builderPoints->get()->getRow();

        $builderPoints->select('name, value');
        $builderPoints->where('name', 'Fold Points');
        $foldpoints = $builderPoints->get()->getRow();

        $users->select('userID,username, firstName, lastName');
        $users->where('userType !=', 'Admin');
        $users->where('userType !=', 'Staff');
        $usersList = $users->get()->getResult();

        $performance = array();
        foreach ($usersList as $user) {
            $userName = strtoupper($user->firstName);

            $joborders->select('userID');
            $joborders->where('washBy', $user->userID);
            $joborders->like('washDate', $current_date, 'right');
            $wash = $joborders->countAllResults();

            $joborders->select('userID');
            $joborders->where('dryBy', $user->userID);
            $joborders->like('dryDate', $current_date, 'right');
            $dry = $joborders->countAllResults();

            $joborders->select('userID');
            $joborders->where('foldBy', $user->userID);
            $joborders->like('dryDate', $current_date, 'right');
            $fold = $joborders->countAllResults();

            $points = ($wash * $washpoints->value) + ($dry * $drypoints->value) + ($fold * $foldpoints->value);

            // Create an associative array for each user
            $performance[] = array(
                'userName' => $userName,
                'wash'     => $wash,
                'dry'      => $dry,
                'fold'     => $fold,
                'points'   => $points
            );
        }

        // Insertion sort based on 'fold'
        for ($i = 1; $i < count($performance); $i++) {
            $key = $performance[$i];
            $j   = $i - 1;

            // Move elements of $productivity[0..$i-1] that are greater than $key['fold']
            // to one position ahead of their current position
            while ($j >= 0 && $performance[$j]['points'] < $key['points']) {
                $performance[$j + 1] = $performance[$j];
                $j                    = $j - 1;
            }

            // Place $key at its correct position in $productivity
            $performance[$j + 1] = $key;
        }
        // Assign the sorted $productivity array to the 'productivity' key in the $data array


        return $this->response->setJSON($performance);
    }

    public function getMetrics()
    {
        $current_date = date('Y-m-d', strtotime($this->curr_date));
        $transactions = $this->db->table('transactions');
        $joborders    = $this->db->table('job_orders');

        $morningStart   = ' 01:00';
        $morningEnd     = ' 12:00';

        $afternoonStart = ' 12:00';
        $afternoonEnd   = ' 17:00';

        $eveningStart = ' 17:00';
        $eveningEnd   = ' 23:59';

        $metrics      = array();

        //start receive matrics
        $transactions->select('SUM(totalLoads) AS totalLoads');
        $transactions->where('dateCreated >', $current_date . $morningStart);
        $transactions->where('dateCreated <', $current_date . $morningEnd);
        $receiveMorning = $transactions->get()->getRow();

        $transactions->select('SUM(totalLoads) AS totalLoads');
        $transactions->where('dateCreated >', $current_date . $afternoonStart);
        $transactions->where('dateCreated <', $current_date . $afternoonEnd);
        $receiveAfternoon = $transactions->get()->getRow();

        $transactions->select('SUM(totalLoads) AS totalLoads, dateCreated');
        $transactions->where('dateCreated >', $current_date . $eveningStart);
        $transactions->where('dateCreated <', $current_date . $eveningEnd);
        $receiveEvening = $transactions->get()->getRow();

        $receiveTotalMorning   = 0;
        $receiveTotalAfternoon = 0;
        $receiveTotalEvening   = 0;
        if (isset($receiveMorning->totalLoads)) {
            $receiveTotalMorning =  $receiveMorning->totalLoads;
        }
        if (isset($receiveAfternoon->totalLoads)) {
            $receiveTotalAfternoon = $receiveAfternoon->totalLoads;
        }
        if (isset($receiveEvening->totalLoads)) {
            $receiveTotalEvening = $receiveEvening->totalLoads;
        }

        // Assuming $receiveTotalMorning, $receiveTotalAfternoon, and $receiveTotalEvening are your variables
        $metrics['receive']['morning']   = $receiveTotalMorning;
        $metrics['receive']['afternoon'] = $receiveTotalAfternoon;
        $metrics['receive']['evening']   = $receiveTotalEvening;
        //end receive matrics

        //start ready matrics
        // $transactions->select('SUM(totalLoads) AS totalLoads');
        $joborders->where('dateCreated >', $current_date . $morningStart);
        $joborders->where('dateCreated <', $current_date . $morningEnd);
        $joborders->join('transactions', 'transactions.transID = job_orders.transID', 'left');
        $joborders->where('job_orders.status', 6);
        $readyMorning = $joborders->countAllResults();

        // $transactions->select('SUM(totalLoads) AS totalLoads');
        $joborders->where('dateCreated >', $current_date . $afternoonStart);
        $joborders->where('dateCreated <', $current_date . $afternoonEnd);
        $joborders->join('transactions', 'transactions.transID = job_orders.transID', 'left');
        $joborders->where('job_orders.status', 6);
        $readyAfternoon = $joborders->countAllResults();

        // $transactions->select('SUM(totalLoads) AS totalLoads');
        $joborders->where('dateCreated >', $current_date . $eveningStart);
        $joborders->where('dateCreated <', $current_date . $eveningEnd);
        $joborders->join('transactions', 'transactions.transID = job_orders.transID', 'left');
        $joborders->where('job_orders.status', 6);
        $readyEvening = $joborders->countAllResults();

        $readyTotalMorning   = 0;
        $readyTotalAfternoon = 0;
        $readyTotalEvening   = 0;
        if ($readyMorning) {
            $readyTotalMorning =  $readyMorning;
        }
        if ($readyAfternoon) {
            $readyTotalAfternoon = $readyAfternoon;
        }
        if ($readyEvening) {
            $readyTotalEvening = $readyEvening;
        }

        $metrics['done']['morning']   = $readyTotalMorning;
        $metrics['done']['afternoon'] = $readyTotalAfternoon;
        $metrics['done']['evening']   = $readyTotalEvening;
        //end ready matrics

        //start pending matrics
        // $transactions->select('SUM(totalLoads) AS totalLoads');
        $joborders->where('dateCreated >', $current_date . $morningStart);
        $joborders->where('dateCreated <', $current_date . $morningEnd);
        $joborders->join('transactions', 'transactions.transID = job_orders.transID', 'left');
        $joborders->where('job_orders.status', 1);
        $pendingMorning = $joborders->countAllResults();

        // $transactions->select('SUM(totalLoads) AS totalLoads');
        $joborders->where('dateCreated >', $current_date . $afternoonStart);
        $joborders->where('dateCreated <', $current_date . $afternoonEnd);
        $joborders->join('transactions', 'transactions.transID = job_orders.transID', 'left');
        $joborders->where('job_orders.status', 1);
        $pendingAfternoon = $joborders->countAllResults();

        // $transactions->select('SUM(totalLoads) AS totalLoads');
        $joborders->where('dateCreated >', $current_date . $eveningStart);
        $joborders->where('dateCreated <', $current_date . $eveningEnd);
        $joborders->join('transactions', 'transactions.transID = job_orders.transID', 'left');
        $joborders->where('job_orders.status', 1);
        $pendingEvening = $joborders->countAllResults();

        $pendingTotalMorning   = 0;
        $pendingTotalAfternoon = 0;
        $pendingTotalEvening   = 0;
        if ($pendingMorning) {
            $pendingTotalMorning =  $pendingMorning;
        }
        if ($pendingAfternoon) {
            $pendingTotalAfternoon = $pendingAfternoon;
        }
        if ($pendingEvening) {
            $pendingTotalEvening = $pendingEvening;
        }

        $metrics['pending']['morning']   = $pendingTotalMorning;
        $metrics['pending']['afternoon'] = $pendingTotalAfternoon;
        $metrics['pending']['evening']   = $pendingTotalEvening;
        //end pending matrics

        //start processing matrics
        // $transactions->select('SUM(totalLoads) AS totalLoads');
        $joborders->where('transactions.dateCreated >', $current_date . $morningStart);
        $joborders->where('transactions.dateCreated <', $current_date . $morningEnd);
        $joborders->join('transactions', 'transactions.transID = job_orders.transID', 'left');
        $joborders->where('job_orders.status >=', 3);
        $joborders->where('job_orders.status <=', 5);
        $processingMorning = $joborders->countAllResults();

        // $transactions->select('SUM(totalLoads) AS totalLoads');
        $joborders->where('transactions.dateCreated >', $current_date . $afternoonStart);
        $joborders->where('transactions.dateCreated <', $current_date . $afternoonEnd);
        $joborders->join('transactions', 'transactions.transID = job_orders.transID', 'left');
        $joborders->where('job_orders.status >=', 3);
        $joborders->where('job_orders.status <=', 5);
        $processingAfternoon = $joborders->countAllResults();

        // $transactions->select('SUM(totalLoads) AS totalLoads');
        $joborders->where('transactions.dateCreated >', $current_date . $eveningStart);
        $joborders->where('transactions.dateCreated <', $current_date . $eveningEnd);
        $joborders->join('transactions', 'transactions.transID = job_orders.transID', 'left');
        $joborders->where('job_orders.status >=', 3);
        $joborders->where('job_orders.status <=', 5);
        $processingEvening = $joborders->countAllResults();

        $processingTotalMorning   = 0;
        $processingTotalAfternoon = 0;
        $processingTotalEvening   = 0;
        if ($processingMorning) {
            $processingTotalMorning =  $processingMorning;
        }
        if ($processingAfternoon) {
            $processingTotalAfternoon = $processingAfternoon;
        }
        if ($processingEvening) {
            $processingTotalEvening = $processingEvening;
        }

        $metrics['processing']['morning']   = $processingTotalMorning;
        $metrics['processing']['afternoon'] = $processingTotalAfternoon;
        $metrics['processing']['evening']   = $processingTotalEvening;
        //end processing matrics


        //start productivity rate
        $productivityMorning = 0;
        if ($receiveTotalMorning) {
            $productivityMorning = ($readyTotalMorning + $processingTotalMorning) / $receiveTotalMorning * 100;
        }

        $productivityAfternoon = 0;
        if ($receiveTotalAfternoon) {
            $productivityAfternoon =  ($readyTotalAfternoon + $processingTotalAfternoon) / $receiveTotalAfternoon * 100;
        }

        $productivityEvening = 0;
        if ($receiveTotalEvening) {
            $productivityEvening   = ($readyTotalEvening + $processingTotalEvening) / $receiveTotalEvening * 100;
        }

        $metrics['productivity rate']['morning']   = number_format($productivityMorning, 2) . '%';
        $metrics['productivity rate']['afternoon'] = number_format($productivityAfternoon, 2) . '%';
        $metrics['productivity rate']['evening']   = number_format($productivityEvening, 2) . '%';
        //end productivity rate

        return $this->response->setJSON($metrics);
    }

    public function getPendingJo()
    {
        $current_date = date('Y-m-d', strtotime($this->curr_date));
        $transactions = $this->db->table('transactions');

        //start pending jo
        $transactions->select('customer, dateCreated, status');
        $transactions->where('status', 1);
        $transactions->like('dateCreated', $current_date, 'right');
        $transactions->limit(10);
        $pendingJo = $transactions->get()->getResult();
        //end pending jo

        $data = array();
        foreach ($pendingJo as $row) {
            $fullname = $row->customer;
            $nameParts = explode(" ", $fullname);
            // Extract the first and second elements of the array
            $firstName = "";
            if (isset($nameParts[1])) {
                $firstName = $nameParts[0] . " " . $nameParts[1];
            } else {
                $firstName = $nameParts[0];
            }

            $data[] = $firstName . ' ' . date('h:i a', strtotime($row->dateCreated));
        }

        return $this->response->setJSON($data);
    }

    public function getProcessJo()
    {
        $current_date = date('Y-m-d', strtotime($this->curr_date));
        $transactions = $this->db->table('transactions');

        $statusList  = array(
            1 => 'R',
            3 => 'W',
            4 => 'D',
            5 => 'F',
        );

        //start processing jo
        $transactions->select('customer, dateCreated, status');
        $transactions->like('dateCreated', $current_date, 'right');
        $transactions->where('status >=', 3);
        $transactions->where('status <=', 5);
        $transactions->limit(10);
        $processingJo = $transactions->get()->getResult();
        //end processing jo

        $data = array();
        foreach ($processingJo as $row) {
            $status = "";
            if (in_array($row->status, array_keys($statusList))) {
                $status =  $statusList[$row->status];
            }

            if ($row->customer) {
                $fullname = $row->customer;
                $nameParts = explode(" ", $fullname);
                // Extract the first and second elements of the array

                if (isset($nameParts[1])) {
                    $firstName = $nameParts[0] . " " . $nameParts[1];
                } else {
                    $firstName = $nameParts[0];
                }
            }

            $data[] = $firstName . ' / ' . $status;
        }

        return $this->response->setJSON($data);
    }

    public function getDate()
    {
        $data['date'] = date('F d, Y', strtotime($this->curr_date));

        return $this->response->setJSON($data);
    }

    public function show()
    {
        return view('pages/performance/show');
    }
}
