<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Parking;
use App\Product;
use App\Income;
use App\Transaction;
use App\Customer;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Html\HtmlServiceProvider;
use Nexmo\Laravel\Facade\Nexmo;
use App\Notifications\Message;

use PDF; // at the top of the file


class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!empty($request->transaction??''))
            $transaction = Transaction::find($request->transaction);
        else
            $transaction = new Transaction();
        $transaction->precio = $request->precio;
        $transaction->tipo = $request->tipo;
        $transaction->description = strtoupper($request->description);
        $transaction->parking_id = Auth::user()->parking_id;
        $transaction->partner_id = Auth::user()->partner_id;
        $transaction->save();

        return ;
    }

    public function updateTransaction(Request $request)
    {
        $ticket = Transaction::find($request->transaction);
        $ticket->description =strtoupper($request->description??'');
        $ticket->tipo =$request->tipo;
        $ticket->precio =$request->precio;
        $ticket->save();
        return ;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    public function precio($tiempo, $tipo, $schedule)
    {
        $horas = $tiempo->format("%H");
        $horas2 = $tiempo->format("%H");
        $minutos = $tiempo->format("%I");
        $minutos2 = $minutos;
        $parking = Parking::find(Auth::user()->parking_id);
        $minutos = ($minutos*1) - ($parking->free_time);
        $horas = (24*$tiempo->format("%d"))+$horas*1 + (($minutos>=0? 1: 0)*1);
        if($parking->parking_id==11){
            $minutos2 = (((24*$tiempo->format("%d"))+$horas2*1)*60)+($minutos2*1)-60;
            $priceMin = $minutos2 > 0?($tipo==1? $parking->min_cars_price*$minutos2: $parking->min_motorcycles_price*$minutos2):0;
            if($schedule==1)
                return ($tipo==1? $parking->hour_cars_price: $parking->hour_motorcycles_price )+$priceMin;
        }
        if($tiempo->format("%I")<=5 && $horas==0 && ($schedule==1 || $schedule==2))
            return 0;
        $horas = $horas==0? 1: $horas;
        if($schedule==1)
            return ($tipo==1? $parking->hour_cars_price * $horas: ($tipo==2? $parking->hour_motorcycles_price * $horas: $parking->hour_van_price * $horas ));
        if($schedule==2)
            return ($tipo==1? $parking->day_cars_price: ($tipo==2? $parking->day_motorcycles_price: $parking->day_van_price ));
        if($schedule==3)
            return ($tipo==1? $parking->monthly_cars_price: ($tipo==2? $parking->monthly_motorcycles_price: $parking->monthly_van_price ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $now = new Datetime('now');
        $ticket = Ticket::find($request->ticket_id);
        if($ticket->status == 2 && !empty($ticket->pay_day)){
            $interval = date_diff(new DateTime("".$ticket->hour),new DateTime("".$ticket->pay_day));
            return [$ticket->price,$interval->format("%H:%I")];
        }
        $interval = date_diff(new DateTime("".$ticket->hour),$now);
        $ticket->status = 2;
        $now2 = date("Y-m-d H:i:s");
        $ticketss= Ticket::select(['plate'])->where('parking_id',Auth::user()->parking_id)->where('status','<>',"3")->where('plate',$ticket->plate)->where('date_end','>=',$now2)->orderBy('ticket_id','desc')->get();

        if($ticket->schedule != 3 || empty($ticket->price))
            $ticket->price = $this->precio($interval,$ticket->type, $ticket->schedule);
        if($ticketss->count() > 0)
            $ticket->price =0;
        $ticket->pay_day =$now;
        $ticket->save();
        return [$ticket->price,$interval->format("%H:%I")];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function getTransactions(Request $request)
    {
        $search = $request->get('search')['value'];
        $transaction = $request->get('transaction');
        $range = $request->get('range');
        $customer = $request->get('customer');
        $customers = $request->get('customers')??null;
        $tipo = $request->get('tipo')?? null;

        $tickets= Transaction::select(['id_transaction as Id', 'precio', 'partner_id','created_at','customer_id','tipo','description','estado'])
            ->where('parking_id',Auth::user()->parking_id)
            ->orderBy('id_transaction','desc');
        if (!empty($range) && empty($customer)) {
            $dateRange = explode(" - ", $range);
            $tickets = $tickets->whereBetween('created_at', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59']);
        }
        if (!empty($customer)){
            $tickets = $tickets->where('customer_id', $customer);
            $tickets = $tickets->whereNull('estado');
        }
        if (!empty($tipo))
            $tickets = $tickets->where('tipo', $tipo);

        return Datatables::of($tickets)
            ->addColumn('action', function ($tickets) use($customers){
                $hour =new DateTime("".$tickets->created_at);
                    return (empty($tickets->estado) && Auth::user()->type != 6 ?\Form::button('Eliminar', [
                        'class'   => 'btn btn-warning',
                        'onclick' => "eliminarTransaction('$tickets->Id')",
                        'data-toggle' => "tooltip",
                        'data-placement' => "bottom",
                        'title' => "Eliminar !",

                    ]):'').($tickets->tipo == 1  && empty($tickets->estado) && Auth::user()->type != 6?
                            \Form::button('Editar', [
                                'class'   => 'btn btn-primary',
                                'onclick' => "openModalVenta('$tickets->Id','".format_money($tickets->precio)."','".($tickets->customer_id ?? '')."','".$tickets->description."','".$hour->format('Y-m-d')."')",
                                'data-toggle' => "tooltip",
                                'data-placement' => "bottom",
                                'title' => "Editar !",

                            ]) :'')
                        .(!empty($tickets->customer_id) && Auth::user()->type != 6?
                        \Form::button('Editar Cliente', [
                            'class'   => 'btn btn-primary',
                            'onclick' => "openModalClienteMod($tickets->customer_id)",
                            'data-toggle' => "tooltip",
                            'data-placement' => "bottom",
                            'title' => "Editar Cliente",

                        ]) :'')
                        .($tickets->tipo != 1 && empty($tickets->estado) && Auth::user()->type != 6?
                        \Form::button('Editar Gasto', [
                            'class'   => 'btn btn-primary',
                            'onclick' => "openModalGasto($tickets->Id,'".$tickets->precio."','".$tickets->description."','".$tickets->tipo."','".$hour->format('Y-m-d')."')",
                            'data-toggle' => "tooltip",
                            'data-placement' => "bottom",
                            'title' => "Editar Gasto",

                        ]) :'').($tickets->tipo == 1  && Auth::user()->type != 6  && Auth::user()->type != 5?
                            \Form::button('Imprimir', [
                                'class'   => 'btn btn-info',
                                'onclick' => "form_pdf('$tickets->Id')",
                                'data-toggle' => "tooltip",
                                'data-placement' => "bottom",
                                'title' => "Imprimir !",

                            ]) :'').($customers == 1 && empty($tickets->estado)?
                            \Form::button('Pagar', [
                                'class'   => 'btn btn-info',
                                'onclick' => "pagarV('$tickets->Id')",
                                'data-toggle' => "tooltip",
                                'data-placement' => "bottom",
                                'title' => "Pagar !",

                            ]) :'');
            })
            ->editColumn('precio', function ($tickets) {
                return format_money($tickets->precio);
            })
            ->editColumn('partner_id', function ($tickets) {
                $partner = Partner::find($tickets->partner_id);
                return  $partner->name;
            })
            ->editColumn('created_at', function ($tickets) {
                $hour =new DateTime("".$tickets->created_at);
                return  $hour->format('d/m/Y  h:ia').' '.$tickets->description ;
            })
            ->make(true);
    }


    public function getStatus(Request $request)
    {
        $schedule = $request->get('type');
        $type = $request->get('type_car');
        $range = $request->get('range');
        $status = $request->get('status');

        $tickets= Transaction::select(['id_transaction as Id', 'precio', 'partner_id','created_at','tipo'])->where('parking_id',Auth::user()->parking_id)->orderBy('id_transaction','desc');
        if (!empty($range)) {
            $dateRange = explode(" - ", $range);
            $tickets = $tickets->whereBetween('created_at', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59']);
        }
        $status = [];
        $status['total'] = ZERO;
        $status['surtido'] = ZERO;
        $status['gastos'] = ZERO;
        $status['recaudado'] = ZERO;
        $status['month_expire'] = 'Mensualidades por vencer:';
        $status['month_expire_num'] = ZERO;

        $tickets=$tickets->get();
        $now = new Datetime('now');
        foreach ($tickets as $ticket){
            if($ticket->tipo == 1){
                $status['total'] += $ticket->precio;
                $status['recaudado'] += $ticket->precio;
            }
            if($ticket->tipo == 2){
                $status['surtido'] += $ticket->precio;
                $status['total'] -= $ticket->precio;
            }
            if($ticket->tipo == 3){
                $status['gastos'] += $ticket->precio;
                $status['total'] -= $ticket->precio;
            }
        }
        $status['total'] = format_money($status['total']);
        $status['surtido'] = format_money($status['surtido']);
        $status['gastos'] = format_money($status['gastos']);
        $status['recaudado'] = format_money($status['recaudado']);
        return $status;
    }
    public function getStatusAcueducto(Request $request)
    {
        $schedule = $request->get('type');
        $type = $request->get('type_car');
        $range = $request->get('range');
        $status = $request->get('status');

        $tickets= Transaction::select(['id_transaction as Id', 'precio', 'partner_id','created_at','tipo'])->where('parking_id',Auth::user()->parking_id)->orderBy('id_transaction','desc');
        if (!empty($range)) {
            $dateRange = explode(" - ", $range);
            $tickets = $tickets->whereBetween('created_at', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59']);
        }
        $status = [];
        $status['entradas'] = ZERO;
        $status['reparaciones'] = ZERO;
        $status['instalaciones'] = ZERO;
        $status['extensiones'] = ZERO;
        $status['inventario'] = ZERO;
        $status['cantidad'] = ZERO;

        $tickets=$tickets->get();
        $now = new Datetime('now');
        foreach ($tickets as $ticket){
            if($ticket->tipo == 1){
                $status['entradas'] += $ticket->precio;
            }
            if($ticket->tipo == 2){
                $status['reparaciones'] += $ticket->precio;
            }
            if($ticket->tipo == 3){
                $status['instalaciones'] += $ticket->precio;
            }
            if($ticket->tipo == 4){
                $status['extensiones'] += $ticket->precio;
            }
        }
        $products = Product::where('parking_id',Auth::user()->parking_id)->orderBy('name','asc')->get();
        foreach ($products as $product){
            $status['inventario'] += ($product->cantidad *$product->precio);
            $status['cantidad'] += $product->cantidad;
        }

        $status['entradas'] = format_money($status['entradas']);
        $status['reparaciones'] = format_money($status['reparaciones']);
        $status['instalaciones'] = format_money($status['instalaciones']);
        $status['extensiones'] = format_money($status['extensiones']);
        $status['inventario'] = format_money($status['inventario']);

        return $status;
    }
    public function getTransaction(Request $request)
    {
        $ticket = Transaction::find($request->id);
        return $ticket;
    }
    public function deleteTransaction(Request $request)
    {
        $tipo =$request->tipo ??'';
        $ticket = Transaction::find($request->transaction);
        $incomes = Income::select(['id_income as Id', 'precio', 'product_id', 'cantidad','description'])->where('parking_id',Auth::user()->parking_id)->where('transaction_id', $request->transaction)->orderBy('id_income','desc')->get();
        foreach ($incomes as $income) {
            $product = Product::find($income->product_id);
            if($product->cantidad != '-1'){
                if($ticket->tipo==1){
                    if(!empty($tipo)){
                        if(Auth::user()->parking_id == 8)
                            $product->precio= ($product->cantidad-$income->cantidad == 0) ? 0: round((($product->cantidad*($product->precio*1)) - ($income->cantidad*($income->precio*1))) /($product->cantidad-$income->cantidad),2);
                        else
                            $product->precio= ($product->cantidad-$income->cantidad == 0) ? 0:intval((($product->cantidad*($product->precio*1)) - ($income->cantidad*($income->precio*1))) /($product->cantidad-$income->cantidad));
                        $product->cantidad = $product->cantidad-$income->cantidad;
                    }
                    else
                        $product->cantidad = $product->cantidad + $income->cantidad;
                }else{
                    if(!empty($tipo)){
                        $product->cantidad = $product->cantidad + $income->cantidad;
                    }else
                        $product->cantidad = $product->cantidad - $income->cantidad;
                }
                $product->save();
                $income->delete();
            }
        }
        $ticket->delete();
        return ;
    }
    public function pagarVenta(Request $request)
    {
        $ticket = Transaction::find($request->transaction);
        $ticket->estado = 1;
        $ticket->save();
        return ;
    }
    public function recoveryTicket(Request $request)
    {
        $ticket = Ticket::find($request->ticket_id);
        $ticket->status = 1;
        $ticket->price =null;
        $ticket->pay_day =null;
        $ticket->save();
        return ;
    }
    public function export(Request $request)
    {
        $id = $request->get('id_transaction');

        $income= Transaction::incomes($id)->get();
        $date_bill = new DateTime($income[0]->fecha_pago);
        $date_bill = $date_bill->format('M. - Y');
        $date = Carbon::now()->toDateString();
        $data     = [
            'date'                 => $date,
            'date_bill'                 => $date_bill,
            'income'               => $income,
            'transaction'          => Transaction::find($id),
            'partner'              => Auth::user()->first_name.' '. Auth::user()->last_name,
            'type_partner'         => Auth::user()->type,
        ];
        return \PDF2::loadView('PDF.transaction', $data)->stream("reporte_$date.pdf");
    }
    public function pdf(Request $request)
    {
        $id = $request->id_pdf;
        $ticket= Transaction::find($id);
        $hour =new DateTime("".$ticket->created_at);
        $incomes = Income::select(['id_income as Id', 'precio', 'product_id', 'cantidad','description'])->where('parking_id',Auth::user()->parking_id)->where('transaction_id', $id)->orderBy('id_income','desc')->get();
        $incomes_text = "";
        $incomes_text2 = "";
        foreach ($incomes as $income){
            $product = Product::find($income->product_id);
            $incomes_text.="<tr>
    <td><small  style='font-size:4px'>".$product->name."</small></td>
    <td>".$income->cantidad."</td> 
    <td>".format_money($income->precio)."</td>
  </tr>";
            $incomes_text2.="<tr>
    <td><small  style='font-size:4px'>".$product->name."</small></td>
    <td>".$income->cantidad."</td> 
  </tr>";
        }
        $customer_html='';
        if(!empty($ticket->customer_id)){
            try{
                $customer = Customer::find($ticket->customer_id);
                $customer_html= 'Cliente: '.$customer->nombre.'<br>NIT: '.$customer->cedula.'<br>';
            }catch (\Exception $e){
                ;
            }
        }

        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 4
        );
        PDF::SetTitle('Venta');
        PDF::AddPage('P', 'A6');
        $marginRight = Auth::user()->parking_id == 5?57:45;
        $marginLeft = Auth::user()->parking_id == 5?2:6;
        $size = Auth::user()->parking_id == 5?'8px':'small';
        PDF::SetMargins($marginLeft, 0, $marginRight);
        $parking = Parking::find(Auth::user()->parking_id);
        $html = '<div style="text-align:center; margin-top: -10px !important"><big style="margin-bottom: 1px"><b style="letter-spacing: -1 px;">&nbsp;'.$parking->name.'</b></big><br>
                '.($parking->parking_id !=5?'<em style="font-size: 7px;margin-top: 2px;margin-bottom: 1px">Slogan <br></em>':'').'
                <small style="font-size: x-small;margin-top: 1px;margin-bottom: 1px"><b>'.$parking->address.'</b></small>'
            .($parking->parking_id==10?'<small style="text-align:center;font-size: 6px"><br>
    NIT:6646393-4  <br>ADOLFO REYES DURAN<br> </small><small style="text-align:center;font-size: 8px"><b>SERVICIO: 8am-5:30pm DOM-DOM</b><br> <b> TEL: 3102504229-7181926</b></small>':'');

        $html .= '<small style="text-align:left;font-size: '.$size.';margin-bottom: 1px;"><b><br>
            FACTURA DE VENTA N°  '. $id . '<br> 
             Fecha ingreso: ' . $hour->format('d/m/Y') . '<br>
             Hora ingreso: ' . $hour->format('h:ia') . '<br>'.
            $customer_html.
            '
             
             
            
             </div>
             <table style="width:100%">
  <tr>
    <th width="50%">Producto</th>
    <th  width="24%">Cant</th> 
    <th>Precio Total</th>
  </tr>
  '.$incomes_text.'
</table>
<br>
<hr>
<b>Precio: ' . format_money($ticket->precio) . '</b><br>
             ';

        $html .= '<small style="text-align:left;font-size: 6px"><br>
                 <b>IMPRESO POR TEMM SOFT 3207329971</b>
                 </small>';
        PDF::writeHTML($html, true, false, true, false, '');
        PDF::AddPage('P', 'A4');
        $html =' FACTURA DE VENTA N°  '. $id . '<br> <table style="width:100%">
  <tr>
    <th width="70%">Producto</th>
    <th  width="30%">Cant</th> 
  </tr>
  '.$incomes_text2.'
</table>';
        //PDF::writeHTML($html, true, false, true, false, '');
        /*if(!isset($ticket->price)){
            $id_bar = substr('0000000000'.$ticket->ticket_id,-10);
            PDF::write1DBarcode($id_bar, 'C128C', '', '', '', 18, 0.4, $style, 'N');
        }*/
        $js = 'print(true);';
        PDF::IncludeJS($js);
        PDF::Output('ticket.pdf');

// set javascript
    }
    public function pdfReport(Request $request)
    {
        $range = $request->date_pdf;
        $base = $request->base?? 500000;
        //dd($range);
        $tickets= Transaction::select(['id_transaction as Id', 'precio', 'partner_id','created_at','tipo','description','customer_id'])->where('parking_id',Auth::user()->parking_id)->orderBy('id_transaction','desc');
        if (!empty($range)) {
            $dateRange = explode(" - ", $range);
            $tickets = $tickets->whereBetween('created_at', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59']);
        }
        $tickets=$tickets->get();
        $now = new Datetime('now');
        $status = [];
        $status['creditos_html'] = "";
        $status['creditos'] = ZERO;
        $status['gastos_html'] = "";
        $status['surtido_html'] = "";
        $status['total'] = $base;
        $status['surtido'] = ZERO;
        $status['gastos'] = ZERO;
        $status['recaudado'] = ZERO;
        foreach ($tickets as $ticket){
            if($ticket->tipo == 1){
                if(!empty($ticket->customer_id)){
                    $customer = Customer::find($ticket->customer_id);
                    $status['creditos_html'].='<tr>
                                            <td colspan="2"><small>'.$customer->nombre.'</small></td> 
                                            <td>'.format_money($ticket->precio).'</td> 
                                          </tr>';
                    $status['creditos'] += $ticket->precio;
                }else{
                    $status['recaudado'] += $ticket->precio;
                    $status['total'] += $ticket->precio;
                }
            }
            if($ticket->tipo == 2){
                $status['surtido'] += $ticket->precio;
                $status['total'] -= $ticket->precio;
                $status['surtido_html'].='<tr>
                                            <td colspan="2"><small>'.$ticket->description.'</small></td> 
                                            <td>'.format_money($ticket->precio).'</td> 
                                          </tr>';
            }
            if($ticket->tipo == 3){
                $status['gastos'] += $ticket->precio;
                $status['total'] -= $ticket->precio;
                $status['gastos_html'].='<tr>
                                            <td colspan="2"><small>'.$ticket->description.'</small></td> 
                                            <td>'.format_money($ticket->precio).'</td> 
                                          </tr>';
            }
        }
        $status['totalSinBase'] = format_money($status['total']*1-$base*1);
        $status['totalDia'] = format_money($status['total']*1-$base*1+$status['creditos']);
        $status['totalVentas'] = format_money($status['recaudado']*1+$status['creditos']);
        $status['total'] = format_money($status['total']);
        $status['surtido'] = format_money($status['surtido']);
        $status['gastos'] = format_money($status['gastos']);
        $status['recaudado'] = format_money($status['recaudado']);

        PDF::SetTitle('Reporte PDF');
        PDF::AddPage('P', 'A6');

        $html = '<table style="width:100%">
        <tr>
        <th>'.$dateRange[0].'</th>
        <th>'.$dateRange[1].'</th> 
        <th>Precio</th>
      </tr>
      <tr>
        <td colspan="2"><b>Gastos</b></td>
        <td><b>'.$status['gastos'].'</b></td> 
      </tr>
      '.$status['gastos_html'].'
      <tr>
        <td colspan="2"><b>Surtido</b></td>
        <td><b>'.$status['surtido'].'</b></td> 
      </tr>
      '.$status['surtido_html'].'
      <tr>
        <td colspan="2"><b>Creditos</b></td>
        <td><b>'.format_money($status['creditos']).'</b></td> 
      </tr>
    '.$status['creditos_html'].'
    <tr>
        <td colspan="2"><b>Ventas en Efectivo</b></td>
        <td><b>'.$status['recaudado'].'</b></td> 
      </tr>
      <tr>
        <td colspan="2"><b>Total Ventas</b></td>
        <td><b>'.$status['totalVentas'].'</b></td> 
      </tr>
      <hr>
      <tr>
        <td colspan="2"><b>Saldo</b></td>
        <td><b>'.$status['totalDia'].'</b></td> 
      </tr>
      <hr>
  </table>
  
  <table style="width:100%">
      
      <tr>
        <td colspan="2"><b> - Creditos</b></td>
        <td><b>'.format_money($status['creditos']).'</b></td> 
      </tr>
      <tr>
        <td colspan="2"><b>Efectivo a retirar</b></td>
        <td><b>'.$status['totalSinBase'].'</b></td> 
      </tr>
      <hr>
      
      <tr>
        <td colspan="2"><b>Total Efectivo en caja</b></td>
        <td><b>'.$status['total'].'</b></td> 
      </tr>
      <tr>
        <td colspan="2"><b>Base</b></td>
        <td><b>'.format_money($base).'</b></td> 
      </tr>
      <hr>
      <tr>
        <td colspan="2"><b>Efectivo a retirar</b></td>
        <td><b>'.$status['totalSinBase'].'</b></td> 
      </tr>
      
  </table>';
  

        PDF::writeHTML($html, true, false, true, false, '');
        $js = 'print(true);';
        PDF::IncludeJS($js);
        PDF::Output('ticket.pdf');
    }

    public function pdfAcueducto(Request $request)
    {
        $fecha = $request->fechaReporte;
        $dateRange = explode("-", $fecha);
        $mes = $dateRange[1];
        $anio = $dateRange[0];
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        //dd($fecha);
        $tickets= Transaction::select(['id_transaction as Id', 'precio', 'partner_id','created_at','tipo','description','customer_id'])->where('parking_id',Auth::user()->parking_id)->orderBy('id_transaction','desc');
        if (!empty($mes)) {
            $tickets = $tickets->whereMonth('created_at',$mes);
        }
        if (!empty($anio)) {
            $tickets = $tickets->whereYear('created_at',$anio  );
        }
        $tickets=$tickets->get();
        $status = [];
        $status['entradas'] = ZERO;
        $status['reparaciones'] = ZERO;
        $status['instalaciones'] = ZERO;
        $status['instalaciones_list'] = "";
        $status['extensiones'] = ZERO;
        $status['salidas'] = ZERO;
        $status['entradas_html'] = '';
        $status['reparaciones_html'] = '';
        $status['instalaciones_html'] = '';
        $status['extensiones_html'] = '';

        $now = new Datetime('now');
        foreach ($tickets as $ticket){
            if($ticket->tipo == 1){
                $status['entradas'] += $ticket->precio;
                $status['entradas_html'] .= '<tr>
                                                    <td colspan="2"><b>'.$ticket->description.' - '.$ticket->created_at.'</b></td>
                                                    <td style="text-align: right">'.format_money($ticket->precio).'</td>  
                                                  </tr>';
                $incomes = Income::select(['id_income as Id', 'precio', 'product_id', 'cantidad','description'])->where('parking_id',Auth::user()->parking_id)->where('transaction_id', $ticket->Id)->orderBy('id_income','desc')->get();
                foreach ($incomes as $income) {
                    $product = Product::find($income->product_id);
                    $status['entradas_html'] .= '<tr>
                                                    <td>  - '.($product->name).'</td>
                                                    <td style="text-align: right">'.$income->cantidad.'</td>  
                                                    <td style="text-align: right">'.format_money($income->precio).'</td>  
                                                  </tr>';
                }
                $status['entradas_html'] .= '<br>';
            }
            if($ticket->tipo == 2){
                $status['reparaciones'] += $ticket->precio;
                $status['salidas'] += $ticket->precio;
                $status['reparaciones_html'] .= '<tr>
                                                    <td colspan="2"><b>'.$ticket->description.' - '.$ticket->created_at.'</b></td>
                                                    <td style="text-align: right">'.format_money($ticket->precio).'</td>  
                                                  </tr>';
                $incomes = Income::select(['id_income as Id', 'precio', 'product_id', 'cantidad','description'])->where('parking_id',Auth::user()->parking_id)->where('transaction_id', $ticket->Id)->orderBy('id_income','desc')->get();
                foreach ($incomes as $income) {
                    $product = Product::find($income->product_id);
                    $status['reparaciones_html'] .= '<tr>
                                                    <td>  - '.($product->name??'').'</td>
                                                    <td style="text-align: right">'.$income->cantidad.'</td>  
                                                    <td style="text-align: right">'.format_money($income->precio).'</td>  
                                                  </tr>';
                }
                $status['reparaciones_html'] .= '<br>';
            }
            if($ticket->tipo == 3){
                $status['instalaciones'] += $ticket->precio;
                $status['instalaciones_list'].='<tr>
                                                    <td colspan="2">  - '.$ticket->description.'</td>
                                                    <td style="text-align: right">'.format_money($ticket->precio).'</td>  
                                                  </tr>';
                $status['salidas'] += $ticket->precio;
                $status['instalaciones_html'] .= '<tr>
                                                    <td colspan="2"><b>'.$ticket->description.' - '.$ticket->created_at.'</b></td>
                                                    <td style="text-align: right">'.format_money($ticket->precio).'</td>  
                                                  </tr>';
                $incomes = Income::select(['id_income as Id', 'precio', 'product_id', 'cantidad','description'])->where('parking_id',Auth::user()->parking_id)->where('transaction_id', $ticket->Id)->orderBy('id_income','desc')->get();
                foreach ($incomes as $income) {
                    $product = Product::find($income->product_id);
                    $status['instalaciones_html'] .= '<tr>
                                                    <td>  - '.($product->name??'').'</td>
                                                    <td style="text-align: right">'.$income->cantidad.'</td>  
                                                    <td style="text-align: right">'.format_money($income->precio).'</td>  
                                                  </tr>';
                }
                $status['instalaciones_html'] .= '<br>';
            }
            if($ticket->tipo == 4){
                $status['extensiones'] += $ticket->precio;
                $status['salidas'] += $ticket->precio;
                $status['extensiones_html'] .= '<tr>
                                                    <td colspan="2"><b>'.$ticket->description.' - '.$ticket->created_at.'</b></td>
                                                    <td style="text-align: right">'.format_money($ticket->precio).'</td>  
                                                  </tr>';
                $incomes = Income::select(['id_income as Id', 'precio', 'product_id', 'cantidad','description'])->where('parking_id',Auth::user()->parking_id)->where('transaction_id', $ticket->Id)->orderBy('id_income','desc')->get();
                foreach ($incomes as $income) {
                    $product = Product::find($income->product_id);
                    $status['extensiones_html'] .= '<tr>
                                                    <td>  - '.($product->name??'').'</td>
                                                    <td style="text-align: right">'.$income->cantidad.'</td>  
                                                    <td style="text-align: right">'.format_money($income->precio).'</td>  
                                                  </tr>';
                }
                $status['extensiones_html'] .= '<br>';
            }
        }
        $status['entradas'] = format_money($status['entradas']);
        $status['salidas'] = format_money($status['salidas']);
        $status['reparaciones'] = format_money($status['reparaciones']);
        $status['instalaciones'] = format_money($status['instalaciones']);
        $status['extensiones'] = format_money($status['extensiones']);

        PDF::SetTitle('Reporte PDF');
        PDF::AddPage('P', 'A4');

        $html = '<div style="margin: 30px;align: center"><table style="width:80%; padding: 7px auto;">
      <tr style="padding-bottom: 14px">
        <th colspan="3" style="text-align: center"><b>MOVIMIENTO DE INVENTARIO</b><br></th>
      </tr>
      <tr>
        <th>Mes :' .$meses[$mes - 1].'</th>
        <th></th>
        <th>Año :'.$anio.'</th> 
      </tr>
      <br>
      <tr>
        <td colspan="2"><b>Entradas</b></td>
        <td style="text-align: right"><b>'.$status['entradas'].'</b></td> 
      </tr>
      <tr>
        <td colspan="2"><b>Salidas</b></td>
        <td style="text-align: right"><b>'.$status['salidas'].'</b></td> 
      </tr>
      <tr>
        <td colspan="2">Instalaciones</td>
        <td style="text-align: right">'.$status['instalaciones'].'</td>  
      </tr>'.
      $status['instalaciones_list'].'
      <tr>
        <td colspan="2">Reparaciones</td>
        <td style="text-align: right">'.$status['reparaciones'].'</td> 
      </tr>
      <tr>
        <td colspan="2">Extensiones</td>
        <td style="text-align: right">'.$status['extensiones'].'</td> 
      </tr>
      <hr>
      <tr style="padding-bottom: 14px">
        <td colspan="3" style="text-align: center"><b>ENTRADAS</b><br></td>
      </tr>'.
            $status['entradas_html'].'
      <hr>
      <tr style="padding-bottom: 14px">
        <td colspan="3" style="text-align: center"><b>INSTALACIONES</b><br></td>
      </tr>'.
            $status['instalaciones_html'].'
      <hr>
      <tr style="padding-bottom: 14px">
        <td colspan="3" style="text-align: center"><b>REPARACIONES</b><br></td>
      </tr>'.
            $status['reparaciones_html'].'
      <hr>
      <tr style="padding-bottom: 14px">
        <td colspan="3" style="text-align: center"><b>EXTENSIONES</b><br></td>
      </tr>'.
            $status['extensiones_html'].'
      <hr>
      
  </table>
  <br>
  <hr>
  <br>
  
  </div>';

        PDF::writeHTML($html, true, false, true, false, '');
        $js = 'print(true);';
        PDF::IncludeJS($js);
        PDF::Output('Reporte'.$mes.'/'.$anio.'.pdf');
    }
}
