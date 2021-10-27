<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Parking;
use App\Product;
use App\Income;
use App\Transaction;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Html\HtmlServiceProvider;
use Nexmo\Laravel\Facade\Nexmo;
use App\Notifications\Message;
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;

use PDF; // at the top of the file


class ProductController extends Controller
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
        $now = new Datetime('now');
        $ticket= new Product();
        $ticket->name =strtoupper($request->name);
        $ticket->description =strtoupper($request->description??'');
        $ticket->minimo =$request->minimo??0;
        $ticket->cantidad =$request->cantidad?? -1;
        $ticket->precio =$request->precio;
        $ticket->parking_id = Auth::user()->parking_id;
        $ticket->save();

        /*Nexmo::message()->send([
            'to'   => '573207329971',
            'from' => '573207329971',
            'text' => 'te amo care nalga camila.'
        ]);*/
        return $ticket->ticket_id;
    }
    public function pdf(Request $request)
    {
        $id = $request->id_pdf;
        $ticket= Ticket::find($id);
        $hour =new DateTime("".$ticket->hour);
        $hour2 =new DateTime("".$ticket->date_end);
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
        PDF::SetTitle('Ticket');
        PDF::AddPage('P', 'A6');
        PDF::SetMargins(6, 0, 45);
        $parking = Parking::find(Auth::user()->parking_id);
        $html = '<div style="text-align:center; margin-top: -10px !important"><big style="margin-bottom: 1px"><b style="letter-spacing: -1 px;">&nbsp; PARQUEADERO '.$parking->name.'</b></big><br>
                '.($parking->parking_id !=5?'<em style="font-size: 7px;margin-top: 2px;margin-bottom: 1px">"Todo lo puedo en Cristo que<br> me fortalece": Fil 4:13 <br></em>':'').'
                <small style="font-size: x-small;margin-top: 1px;margin-bottom: 1px"><b>'.$parking->address.'</b></small>'
            .($parking->parking_id==3?'<small style="text-align:center;font-size: 6px"><br>
    NIT: 1094965452-1 <br>OLIVEROS HERNANDEZ VALENTINA<br> </small><small style="text-align:center;font-size: 8px"><b>SERVICIO: Lun-Sab 7am - 9pm</b><br> <b> TEL: 3104276986</b></small>':'')
            .($parking->parking_id==11?'<small style="text-align:center;font-size: 7px"><br>
    <b>SERVICIO: Lun-Sab 7am - 9pm</b><br>CARLOS E. MIDEROS <br> NIT: 80449231-4 <br> TEL: 9207119<br> CEL: 3013830790</small>':'').
            ($parking->parking_id==5?'<small style="text-align:center;font-size: 6px"><br>
    NIT: 89000746-1 <br>HUGO ALEXANDER VARGAS SANCHEZ<br> </small><small style="text-align:center;font-size: 8px"><b>SERVICIO: Lun-Dom 6:30am - 9:30pm</b><br> <b> TEL: 3173799831</b></small>':'');
        if(!isset($ticket->price)) {
            $html .= '<small style="text-align:left;font-size: small;margin-bottom: 1px;"><b><br>
                 ' . ($ticket->schedule==3? "FACTURA DE VENTA N° " . $ticket->ticket_id . "<br>" : '') .'
                 Fecha ingreso: ' . $hour->format('d/m/Y') . '<br>
                 Hora ingreso: ' . $hour->format('h:ia') . '<br>
                 ' . ($ticket->schedule==3? "   Fecha vencimiento: " . $hour2->format('d/m/Y') . "<br>" : '') .'
                 ' . ($ticket->schedule==3? "<b>".strtoupper($ticket->name) . "</b><br>" : '') .'
                 Tipo: ' . ($ticket->type == 1 ? 'Carro' : ($ticket->type == 3 ? ( isBici()?'Bicicleta':'Camioneta' ) : 'Moto')) . '<br>
                 Placa: ' . $ticket->plate . '<br>
                 ' . (isset($ticket->drawer) ? "Locker: " . $ticket->drawer . "<br>" : '') . '
                 </b></small>
                 <small style="text-align:left;font-size: 6px;margin-top: 1px"><br>
                 1.El vehiculo se entregara al portador de este recibo<br>
                 2.No aceptamos ordenes escritas o por telefono<br>
                 3.Despues de retirado el vehiculo no respondemos por daños, faltas o averias. Revise el vehiculo a la salida.<br>
                 4.No respondemos por objetos dejados en el carro mientras sus puertas esten aseguradas<br>
                 5.No somos responsables por daños o perdidas causadas en el parqueadero mientras el vehiculo no sea entregado personalmente<br>
                 6.No respondemos por la perdida, deterioro o daños ocurridos por causa de incendio, terremoto o causas similares, motin,conmosion civil, revolucion <br>y otros eventos que impliquen fuerza mayor.
                 </small></div>';
        }else{
            $pay_day = new DateTime("".$ticket->pay_day);
            $interval = date_diff($hour,$pay_day);
            $horas = $interval->format("%H");
            $minutos = $interval->format("%I");
            if($minutos<=5 && $horas==0 && $ticket->schedule==1){
                $horas= 0;
            }else{
                $parking = Parking::find(Auth::user()->parking_id);
                $minutos = ($minutos*1) - ($parking->free_time);
                $horas = (24*$interval->format("%d"))+$horas*1 + (($minutos>=0? 1: 0)*1);
                $horas = $horas==0? 1: $horas;
            }
            $html .= '<small style="text-align:left;font-size: small"><br>
                    FACTURA DE VENTA N° ' . $ticket->ticket_id . '<br>
                 ' . ($ticket->schedule==3?"<b>".strtoupper($ticket->name) . "</b><br>" : '') .'
                 ' . ($ticket->schedule==1? "   Fracciones: " . $horas . "<br>" : '') .'
                   Fecha ingreso: ' . $hour->format('d/m/Y') . '<br>
                 Hora ingreso: ' . $hour->format('h:ia') . '<br>
                 ' . ($ticket->schedule!=3? "   Fecha salida: " . $pay_day->format('d/m/Y') . "<br>" : '') .'
                 ' . ($ticket->schedule!=3? "   Hora salida: " . $pay_day->format('h:ia') . "<br>" : '') .'
                 ' . ($ticket->schedule==3? "   Fecha vencimiento: " . $hour2->format('d/m/Y') . "<br>" : '') .'
                 Tipo: ' . ($ticket->type == 1 ? 'Carro' : ($ticket->type == 3 ? ( isBici()?'Bicicleta':'Camioneta' ) : 'Moto')) . '<br>
                 Placa: ' . $ticket->plate . '<br>
                 ' . (isset($ticket->price) ? "   Precio: " . $ticket->price . "<br>" : '') .
                (isset($ticket->extra) ? ($ticket->extra>0?"Incremento: ":"Descuento:" ). abs($ticket->extra) . "<br>Total: " . ($ticket->price+$ticket->extra) . "<br>" : '').
                '</small>
</div>';
        }
        $html .= '<small style="text-align:left;font-size: 6px"><br>
                 <b>IMPRESO POR TEMM SOFT 3207329971</b>
                 </small>';
        PDF::writeHTML($html, true, false, true, false, '');
        if(!isset($ticket->price)){
        $id_bar = substr('0000000000'.$ticket->ticket_id,-10);
        PDF::write1DBarcode($id_bar, 'C128C', '', '', '', 18, 0.4, $style, 'N');
        }
        $js = 'print(true);';
        PDF::IncludeJS($js);
        PDF::Output('ticket.pdf');

// set javascript
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
    public function getProducts(Request $request)
    {
        $search = $request->get('search')['value'];

        $tickets= Product::select(['id_product as Id', 'name', 'precio', 'minimo', 'cantidad','description'])->where('parking_id',Auth::user()->parking_id)->orderBy('name','asc');
        if ($search) {
                $tickets = $tickets->where('name', 'LIKE', "%$search%");
        }
        return Datatables::of($tickets)
            ->addColumn('action', function ($tickets) {
                $htmlAdmin= (Auth::user()->type != 6?\Form::button('Editar', [
                        'class'   => 'btn btn-primary',
                        'onclick' => "openModalMod('$tickets->Id')",
                        'data-toggle' => "tooltip",
                        'data-placement' => "bottom",
                        'title' => "Editar !",

                    ]).
                    \Form::button('Eliminar', [
                        'class'   => 'btn btn-warning',
                        'onclick' => "eliminarProduct('$tickets->Id')",
                        'data-toggle' => "tooltip",
                        'data-placement' => "bottom",
                        'title' => "Eliminar !",

                    ]):'').(Auth::user()->type == 5 || Auth::user()->type == 6 || Auth::user()->type == 4?
                        \Form::button('Movimientos', [
                        'class'   => 'btn btn-primary',
                        'onclick' => "openMovimientos('$tickets->Id')",
                        'data-toggle' => "tooltip",
                        'data-placement' => "bottom",
                        'title' => "Movimientos !",

                    ]):'');
                    return $htmlAdmin;
            })
            ->addColumn('valor', function ($tickets) {
                if($tickets->cantidad == '-1')
                    return '$ 0';
                else
                    return format_money($tickets->cantidad * $tickets->precio);
            })
            ->editColumn('precio', function ($tickets) {
                return format_money($tickets->precio);
            })
            ->editColumn('cantidad', function ($tickets) {
                if(Auth::user()->type == 5 || Auth::user()->type == 6){
                    return $tickets->cantidad." ". $tickets->description;
                }
                return $tickets->cantidad;
            })
            ->editColumn('minimo', function ($tickets) {
                if(Auth::user()->type == 5 || Auth::user()->type == 6){
                    if($tickets->minimo==1)
                        return 'Alta';
                    if($tickets->minimo==2)
                        return 'Media';
                    return 'Baja';
                }

                return $tickets->minimo;
            })
            ->make(true);
    }

    public function getMonths(Request $request)
    {
        $parking = Parking::find(Auth::user()->parking_id);
        $search = $request->get('search')['value'];
        $schedule = 3;

        $tickets= Ticket::select(['ticket_id as Id', 'plate', 'type', 'name', 'date_end', 'partner_id', 'status', 'price','email','phone'])->where('parking_id',Auth::user()->parking_id)->where('status','<>',"3")->orderBy('ticket_id','desc');
        if ($search) {
            $search = strtoupper($search);
            $tickets = $tickets->where('plate', 'LIKE', "%$search%");
        }
        if (!empty($schedule))
            $tickets = $tickets->where('schedule', $schedule);

        return Datatables::of($tickets)
            ->addColumn('action', function ($tickets) use($parking){
                if (Auth::user()->type == 1)
                    return ($tickets->status == 1? \Form::button('Pagar', [
                            'class'   => 'btn btn-info',
                            'onclick' => "$('#modal_ticket_out').modal('show');$('#ticket_id').val('$tickets->Id')",
                            'data-toggle' => "tooltip",
                            'data-placement' => "bottom",
                            'title' => "Pagar !",

                        ]) : "").\Form::button('Editar', [
                        'class'   => 'btn btn-primary',
                        'onclick' => "openModalMod('$tickets->Id')",
                        'data-toggle' => "tooltip",
                        'data-placement' => "bottom",
                        'title' => "Editar !",

                    ]).
                        \Form::button('Eliminar', [
                            'class'   => 'btn btn-warning',
                            'onclick' => "eliminarTicket('$tickets->Id')",
                            'data-toggle' => "tooltip",
                            'data-placement' => "bottom",
                            'title' => "Eliminar !",

                        ]).
                        \Form::button('Imprimir', [
                            'class'   => 'btn btn-info',
                            'onclick' => "form_pdf('$tickets->Id')",
                            'data-toggle' => "tooltip",
                            'data-placement' => "bottom",
                            'title' => "Imprimir !",

                        ]).
                        \Form::button('Renovar', [
                            'class'   => 'btn btn-info',
                            'onclick' => "renovarTicket('$tickets->Id')",
                            'data-toggle' => "tooltip",
                            'data-placement' => "bottom",
                            'title' => "Renovar !",

                        ]).(!empty($tickets->phone)?'<a class="btn btn-success" href="https://api.whatsapp.com/send?phone=57'.$tickets->phone.'&text=Hola%20'.$tickets->name.',parqueadero%20'.$parking->name.'%20le%20saluda%20coordialmente%20y%20le%20informa%20que%20el%20vehiculo%20con%20placa%20'.$tickets->plate.'%20tiene%20pago%20el%20parqueo%20con%20nosotros%20hasta%20la%20fecha:%20'.$tickets->date_end.'" target="_blank">Whatsapp</a>':'');
                else
                    return '';
            })
            ->addColumn('Tipo', function ($tickets) {
                return  $tickets->type == 1? 'Carro': 'Moto';
            })
            ->addColumn('Estado', function ($tickets) {
                $now = date("Y-m-d H:i:s");
                return  $tickets->date_end >= $now? 'Activo': 'Vencido';
            })
            ->addColumn('Atendio', function ($tickets) {
                $partner = Partner::find($tickets->partner_id);
                return  $partner->name;
            })
            ->editColumn('price', function ($tickets) {
                return format_money($tickets->price);
            })
            ->make(true);
    }
    public function getStatus(Request $request)
    {
        $schedule = $request->get('type');
        $type = $request->get('type_car');
        $range = $request->get('range');
        $status = $request->get('status');

        $tickets= Ticket::select(['plate', 'type', 'extra', 'schedule', 'price', 'name', 'status', 'date_end'])->where('parking_id',Auth::user()->parking_id)->where('status','<>',"3")->orderBy('ticket_id','desc');
        if (!empty($schedule))
        $tickets = $tickets->where('schedule', $schedule);
        if (!empty($status))
        $tickets = $tickets->where('status', $status);
        if (!empty($type))
            $tickets = $tickets->where('type', $type);
        if (!empty($range)){
            $dateRange = explode(" - ", $range);
            $tickets = $tickets->whereBetween('created_at', [$dateRange[0], $dateRange[1]]);
        }else{
            $tickets = $tickets->whereBetween('created_at', [ new Datetime('today'), new Datetime('tomorrow')]);
        }
        $status = [];
        $status['total'] = ZERO;
        $status['extra'] = ZERO;
        $status['carros'] = ZERO;
        $status['motos'] = ZERO;
        $status['month_expire'] = 'Mensualidades por vencer:';
        $status['month_expire_num'] = ZERO;
        $tickets=$tickets->get();
        $now = new Datetime('now');
        foreach ($tickets as $ticket){
            $status['total'] += $ticket->price;
            $status['extra'] += $ticket->extra;
            if($ticket->type == 1)
                $status['carros'] ++;
            if($ticket->type == 2)
                $status['motos'] ++;
        }
        $ticketss= Ticket::select(['plate', 'type', 'extra', 'schedule', 'price', 'name', 'date_end'])->where('parking_id',Auth::user()->parking_id)->where('status','<>',"3")->orderBy('ticket_id','desc');
        $ticketss = $ticketss->where('schedule', 3);
        $ticketss=$ticketss->get();
        foreach ($ticketss as $ticket){
            if($ticket->schedule == 3 and !empty($ticket->date_end)){
                $hour2 =new DateTime("".$ticket->date_end);
                $diff=date_diff(new DateTime("".$ticket->date_end), $now);
                $diff=$diff->format("%a");
                if($diff<=2){
                    $status['month_expire'] .= $ticket->name.' ('.$ticket->plate.') Vence '.$hour2->format('d/m/Y');
                    $status['month_expire_num'] ++;
                }
            }
        }
        $status['total'] = format_money($status['total']);
        $status['extra'] = format_money($status['extra']);
        return $status;
    }
    public function getProduct(Request $request)
    {
        $ticket = Product::find($request->product_id);
        return $ticket;
    }
    public function updateProduct(Request $request)
    {
        $ticket = Product::find($request->idProduct);
        $ticket->name =strtoupper($request->name);
        $ticket->description =strtoupper($request->description??'');
        $ticket->minimo =$request->minimo??0;
        $ticket->cantidad =$request->cantidad?? -1;
        $ticket->precio =$request->precio;
        $ticket->save();
        return ;
    }
    public function deleteProduct(Request $request)
    {
        $ticket = Product::find($request->product);
        $ticket->delete();
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
    public function renovarTicket(Request $request)
    {
        $tickets = Ticket::find($request->ticket_id);
        $tickets->status = 3;
        $tickets->save();

        $now = new Datetime('now');
        $ticket= new Ticket();
        $ticket->hour =$now;
        $ticket->plate =strtoupper($tickets->plate);
        $ticket->status = 1;
        $ticket->type =$tickets->type;
        $ticket->schedule =$tickets->schedule;
        if($tickets->schedule==3){
            $date_end = new \Carbon\Carbon($tickets->date_end);
            $ticket->date_end = $date_end->addMonth();
            $ticket->name = strtoupper($tickets->name);
            $ticket->email = $tickets->email;
            $ticket->phone = $tickets->movil;
            $ticket->price = $tickets->price;
        }
        $ticket->parking_id = Auth::user()->parking_id;
        $ticket->partner_id = Auth::user()->partner_id;
        $ticket->drawer = $tickets->drawer;
        $ticket->save();

        return ;
    }
    public function getSelect(){
        $products = Product::where('parking_id',Auth::user()->parking_id)->orderBy('name','asc')->get();
        $select="<option value=''>Seleccionar</option>";
        foreach ($products as $product){
            $select .='<option data-toggle="tooltip" title="'.$product->description.'"value="'.$product->id_product.'">'.$product->name.(!empty($product->cantidad) && $product->cantidad !='-1'?' ('.$product->cantidad.')':'').$product->description.'</option>';
        }
        return $select;
    }
    public function exportProducts(){
        $now = new Datetime('now');
        return Excel::download(new ProductsExport, 'Productos'.$now->format('Y-m-d').'.xlsx');
    }

    public function getMovimientos(Request $request){
        $movimiento = $request->get('product');
        $tickets = Income::select(['id_income as Id', 'description', 'precio', 'cantidad', 'created_at','transaction_id'])
            ->where('parking_id',Auth::user()->parking_id)
            ->where('product_id',$movimiento)
            ->orderBy('id_income','asc');
        return Datatables::of($tickets)
            ->addColumn('total', function ($tickets) {
                return  format_money($tickets->cantidad*1*$tickets->precio);
            })
            ->editColumn('precio', function ($tickets) {
                return format_money($tickets->precio);
            })
            ->editColumn('cantidad', function ($tickets) {
                if(Auth::user()->type == 5 || Auth::user()->type == 6){
                    return $tickets->cantidad." ". $tickets->description;
                }
                return $tickets->cantidad;
            })
            ->editColumn('description', function ($tickets) {
                $transaction = Transaction::find($tickets->transaction_id);
                $hour = $transaction? new DateTime("".$transaction->created_at):'';
                return $transaction? $hour->format('d/m/Y  h:ia').' '.$transaction->description :'';
            })
            ->editColumn('tipo', function ($tickets) {
                $transaction = Transaction::find($tickets->transaction_id);
                if(Auth::user()->type == 4)
                    return $transaction?( $transaction->tipo == 1?'venta': ($transaction->tipo == 2?'Surtido':( $transaction->tipo == 3?'Gasto': '' ) )):'';
                else
                    return $transaction?( $transaction->tipo == 1?'Entrada': ($transaction->tipo == 2?'Reparación':( $transaction->tipo == 3?'Instalación':($transaction->tipo == 4?'Extensión': '') ) )):'';
            })
            ->make(true);
    }
}
