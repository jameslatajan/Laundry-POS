<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

$routes->get('/sendSMS', 'CheckStatus::sendSMS');
$routes->get('/gw_send_smss/(:any)/(:any)', 'CheckStatus::gw_send_smss/$1/$2');
$routes->get('/default/(:any)/(:any)', 'CheckStatus::default/$1/$2');
$routes->get('/check/(:any)/(:any)', 'CheckStatus::index/$1/$2');
$routes->post('/check/save', 'CheckStatus::save');
$routes->post('/check/release', 'CheckStatus::release');

$routes->get('/wash/(:any)/(:any)', 'CheckStatus::wash/$1/$2');
$routes->get('/dry/(:any)/(:any)', 'CheckStatus::dry/$1/$2');
$routes->get('/fold/(:any)/(:any)', 'CheckStatus::fold/$1/$2');
$routes->get('/ready/(:any)/(:any)', 'CheckStatus::ready/$1/$2');
$routes->get('/sms/(:any)/(:any)', 'CheckStatus::sms/$1/$2');
$routes->get('/claimSlip/(:any)', 'CheckStatus::claimSlip/$1');
$routes->get('/totalLoads', 'Transaction::totalLoads');

$routes->get('/performance', 'Performance::show');
$routes->get('/performance/getperformance', 'Performance::getPerformance');
$routes->get('/performance/getmetrics', 'Performance::getMetrics');
$routes->get('/performance/getpending', 'Performance::getPendingJo');
$routes->get('/performance/getprocess', 'Performance::getProcessJo');
$routes->get('/performance/getdate', 'Performance::getDate');

$routes->group('', ['filter' => 'NoAuthCheck'], function ($routes) {
    $routes->get('/', 'Home::index');
    $routes->post('/signin', 'Home::signin');
});

$routes->group('', ['filter' => 'AuthCheck'], function ($routes) {
    $routes->get('/logout', 'Home::logout');

    //dashboard
    $routes->get('/dashboard', 'Home::dashboard');
    $routes->post('/dailysms', 'Home::dailySms');

    //transactions
    $routes->get('/regular', 'Form::form');
    $routes->get('/regular2', 'Form::form2');
    $routes->post('/regular/save', 'Form::save');
    $routes->post('/regular2/save', 'Form::save');

    $routes->get('/diyregular', 'Form::form');
    $routes->post('/diyregular/save', 'Form::save');

    $routes->get('/student', 'Form::form');
    $routes->post('/student/save', 'Form::save');
    $routes->get('/diystudent', 'Form::form');
    $routes->post('/diystudent/save', 'Form::save');

    $routes->get('/expressregular', 'Form::form');
    $routes->get('/expressstudent', 'Form::form');
    $routes->post('/expressregular/save', 'Form::save');
    $routes->post('/expressstudent/save', 'Form::save');
    $routes->get('/getcustomers/(:any)', 'Form::getCustomers/$1');

    // print transaction
    $routes->get('/preview/(:any)', 'Form::preview/$1');
    $routes->get('/print/(:any)', 'Form::print/$1');
    $routes->get('/job_order_print/(:any)', 'Form::job_order_print/$1');

    // Sales routes
    $routes->match(['get', 'post'], '/sales', 'SalesC::list');
    $routes->get('/sales/getsales/(:any)', 'SalesC::getsales/$1');
    $routes->get('/sales/getitem/(:any)', 'SalesC::getitem/$1');
    $routes->post('/sales/save', 'SalesC::save');
    $routes->post('/sales/savevale', 'SalesC::savevale');
    $routes->get('/sales/exportlist', 'SalesC::exportlist');

    // Inventory routes
    $routes->match(['get', 'post'], '/inventory', 'Inventory::inventory');
    $routes->get('/get_items', 'Inventory::get_items');
    $routes->post('/inventory/get_stockcard', 'Inventory::get_stockcard');
    $routes->post('/inventory/updatestock', 'Inventory::updatestock');
    $routes->post('/inventory/updateitem', 'Inventory::updateitem');
    $routes->post('/inventory/saveitem', 'Inventory::saveitem');
    $routes->get('/inventory/exportlist', 'Inventory::exportlist');
    $routes->get('/inventory/stockcard/(:num)', 'Inventory::stockcard/$1');

    // Transactions routes
    $routes->match(['get', 'post'], '/transaction', 'Transaction::list');
    $routes->post('/transaction/release', 'Transaction::release');
    $routes->post('/transaction/cancel', 'Transaction::cancel');
    $routes->post('/transaction/payment1', 'Transaction::payment1');
    $routes->post('/transaction/payment2', 'Transaction::payment2');
    $routes->get('/transaction/view/(:any)', 'Transaction::view/$1');
    $routes->get('/transaction/exportlist', 'Transaction::exportlist');
    $routes->get('/transaction/printlist', 'Transaction::printlist');
    $routes->post('/transaction/resolve', 'Transaction::resolve');

    // DSR routes
    $routes->match(['get', 'post'], '/dsr_admin', 'Reports::dsrAdmin');
    $routes->get('/dsr_admin/print/(:any)/(:any)', 'Reports::dsrAdminPrint/$1/$2');
    $routes->get('/dsr_admin/getDsr/(:any)', 'Reports::getDsr/$1');
    $routes->post('/dsr_admin/settle', 'Reports::settle');
    $routes->get('/dsr_generate', 'Reports::dsrClient');
    $routes->get('/dsr_generate/print/(:num)', 'Reports::dsrClientPrint/$1');
    $routes->get('/dsr_generate/logout', 'Reports::logout');

    //unpaid report
    $routes->match(['get', 'post'], '/unpaid_report', 'Unpaid_report::show');
    $routes->get('/unpaid_report/printform', 'Unpaid_report::printForm');

    // Unpaid routes
    $routes->match(['get', 'post'], '/unpaids', 'Unpaid::list');
    $routes->get('/unpaid/exportlist', 'Unpaid::exportlist');
    $routes->get('/unpaid/printlist', 'Unpaid::printlist');

    // Expenses routes
    $routes->match(['get', 'post'], '/expenses', 'ExpensesC::list');
    $routes->post('/expenses/save', 'ExpensesC::save');
    $routes->get('/expenses/printlist', 'ExpensesC::printlist');
    $routes->get('/expenses/exportlist', 'ExpensesC::exportlist');

    // Allowances routes
    $routes->match(['get', 'post'], '/allowances', 'Allowances::list');
    $routes->post('/allowances/save', 'Allowances::save');
    $routes->get('/allowances/printlist', 'Allowances::printlist');
    $routes->get('/allowances/exportlist', 'Allowances::exportlist');

    // Statistics routes
    $routes->match(['get', 'post'], '/statistics/jobOrder', 'Statistics::getJobOrder');
    $routes->match(['get', 'post'], '/statistics/totalSales', 'Statistics::totalSales');
    $routes->match(['get', 'post'], '/statistics/statreport', 'Statistics::statisticsReport');
    $routes->match(['get', 'post'], '/statistics/productivity', 'Statistics::productivityReport');
    $routes->match(['get', 'post'], '/statistics/performacestat', 'Statistics::performacestat');
    $routes->match(['get', 'post'], '/statistics/dsr_report', 'Statistics::dsr_statistics');
    $routes->match(['get', 'post'], '/statistics/variance', 'Statistics::varianceReport');
    $routes->match(['get', 'post'], '/statistics/performanceReport', 'Statistics::performanceReport');
    $routes->match(['get', 'post'], '/statistics/expressOrders', 'Statistics::expressOrders');
    $routes->match(['get', 'post'], '/statistics/dsrsummary', 'Statistics::dsrSummary');
    $routes->get('/statistics/dsrsummary/exportlist', 'Statistics::exportlist');

    $routes->get('/statistics/getOrders/(:num)/(:num)', 'Statistics::getOrders/$1/$2');
    $routes->get('/statistics/getCustomer/(:num)/(:num)', 'Statistics::getCustomer/$1/$2');

    // SMS routes
    $routes->match(['get', 'post'], '/sms', 'Sms::list');
    $routes->post('/sms/saveReady', 'Sms::saveReady');
    $routes->post('/sms/resend', 'Sms::resend');
    $routes->post('/sms/textblast', 'Sms::textblast');
    $routes->get('/getsms/(:any)/(:any)', 'Sms::getData/$1/$2');
    $routes->post('/sms/textblastTest', 'Sms::textblastTest');

    $routes->post('/getTransID', 'Home::getTransID');
});




/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
