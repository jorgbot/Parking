<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::post('/cobrar', 'TicketController@update')->name('cobrar')->middleware('auth');
Route::post('/customers', 'CustomerController@store')->name('customers')->middleware('auth');
Route::post('/customers_mod', 'CustomerController@update')->name('customers_mod')->middleware('auth');
Route::post('/prestamos', 'PrestamoController@store')->name('prestamos')->middleware('auth');
Route::post('/productos', 'ProductController@store')->name('productos')->middleware('auth');
Route::post('/transaction', 'TransactionController@store')->name('transaction')->middleware('auth');
Route::post('/incomes', 'IncomeController@store')->name('incomes')->middleware('auth');
Route::post('/incomes_a', 'IncomeController@storeA')->name('incomes_a')->middleware('auth');
Route::post('/incomes_acueducto', 'IncomeController@storeAcueducto')->name('incomes_acueducto')->middleware('auth');
Route::post('/abonos', 'AbonoController@store')->name('abonos')->middleware('auth');
Route::post('/pdf', 'TicketController@pdf')->name('pdf')->middleware('auth');
Route::post('/pdf_transaction', 'TransactionController@pdf')->name('pdf_transaction')->middleware('auth');
Route::post('/pdf_report', 'TransactionController@pdfReport')->name('pdf_report')->middleware('auth');
Route::post('/pdf_acueducto', 'TransactionController@pdfAcueducto')->name('pdf_acueducto')->middleware('auth');
Route::post('/actualizar', 'TicketController@updateTicket')->name('actualizar')->middleware('auth');
Route::post('/actualizar_prestamo', 'PrestamoController@updatePrestamo')->name('actualizar_prestamo')->middleware('auth');
Route::post('/actualizar_producto', 'ProductController@updateProduct')->name('actualizar_producto')->middleware('auth');
Route::post('/actualizar_transaction', 'TransactionController@updateTransaction')->name('actualizar_transaction')->middleware('auth');
Route::post('/eliminar', 'TicketController@deleteTicket')->name('eliminar')->middleware('auth');
Route::post('/eliminar_abono', 'AbonoController@deleteAbono')->name('eliminar_abono')->middleware('auth');
Route::post('/eliminar_product', 'ProductController@deleteProduct')->name('eliminar_product')->middleware('auth');
Route::post('/pagar_venta', 'TransactionController@pagarVenta')->name('pagar_venta')->middleware('auth');
Route::post('/eliminar_income', 'IncomeController@deleteIncome')->name('eliminar_income')->middleware('auth');
Route::post('/eliminar_transaction', 'TransactionController@deleteTransaction')->name('eliminar_transaction')->middleware('auth');
Route::post('/eliminar_prestamo', 'PrestamoController@deletePrestamo')->name('eliminar_prestamo')->middleware('auth');
Route::post('/recuperar', 'TicketController@recoveryTicket')->name('recuperar')->middleware('auth');
Route::post('/renovar', 'TicketController@renovarTicket')->name('renovar')->middleware('auth');
Route::post('/get_ticket', 'TicketController@getTicket')->name('get_ticket')->middleware('auth');
Route::post('/get_prestamo', 'PrestamoController@getPrestamo')->name('get_prestamo')->middleware('auth');
Route::post('/get_cliente', 'CustomerController@getCustomer')->name('get_cliente')->middleware('auth');
Route::post('/get_customers', 'CustomerController@getSelect')->name('get_customers')->middleware('auth');
Route::post('/get_producto', 'ProductController@getProduct')->name('get_producto')->middleware('auth');
Route::post('/get_transaction', 'TransactionController@getTransaction')->name('get_transaction')->middleware('auth');
Route::post('/update_cuenta', 'PartnerController@update')->name('update_cuenta')->middleware('auth');
Route::post('/update_parking', 'ParkingController@update')->name('update_parking')->middleware('auth');
Route::post('/get_status', 'TicketController@getStatus')->name('get_status')->middleware('auth');
Route::post('/get_status_transaction', 'TransactionController@getStatus')->name('get_status_transaction')->middleware('auth');
Route::post('/get_status_acueducto', 'TransactionController@getStatusAcueducto')->name('get_status_acueducto')->middleware('auth');
Route::post('/get_status_prestamo', 'PrestamoController@getStatus')->name('get_status_prestamo')->middleware('auth');
Route::get('/get_tickets', 'TicketController@getTickets')->name('get_tickets')->middleware('auth');
Route::get('/get_prestamos', 'PrestamoController@getPrestamos')->name('get_prestamos')->middleware('auth');
Route::get('/get_productos', 'ProductController@getProducts')->name('get_productos')->middleware('auth');
Route::get('/get_transactions', 'TransactionController@getTransactions')->name('get_transactions')->middleware('auth');
Route::get('/get_incomes', 'IncomeController@getIncomes')->name('get_incomes')->middleware('auth');
Route::get('/get_movimientos', 'ProductController@getMovimientos')->name('get_movimientos')->middleware('auth');
Route::post('/get_products', 'ProductController@getSelect')->name('get_products')->middleware('auth');
Route::get('/get_abonos', 'AbonoController@getAbonos')->name('get_abonos')->middleware('auth');
Route::get('/get_months', 'TicketController@getMonths')->name('get_months')->middleware('auth');
Route::get('/get_convenios', 'ConvenioController@getConvenios')->name('get_convenios')->middleware('auth');
Route::post('/convenios', 'ConvenioController@store')->name('convenios')->middleware('auth');
Route::post('/get_convenio', 'ConvenioController@getConvenio')->name('get_convenio')->middleware('auth');
Route::post('/actualizar_convenio', 'ConvenioController@update')->name('actualizar_convenio')->middleware('auth');
Route::post('/eliminar_convenio', 'ConvenioController@deleteConvenio')->name('eliminar_convenio')->middleware('auth');
Route::get('/export_products', 'ProductController@exportProducts')->name('export_products')->middleware('auth');
Route::get('/export_tickets/{range}', 'TicketController@export')->name('export_tickets')->middleware('auth');
Route::resource('tickets', 'TicketController')->middleware('auth');