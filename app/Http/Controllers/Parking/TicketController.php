<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Parking;
use App\Ticket;
use App\Convenio;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Html\HtmlServiceProvider;
use Nexmo\Laravel\Facade\Nexmo;
use App\Notifications\Message;
use App\Exports\TicketsExport;
use Maatwebsite\Excel\Facades\Excel;

use PDF; // at the top of the file


class TicketController extends Controller
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
        if($request->schedule!=3){
            $count = Ticket::where('plate',strtoupper($request->plate))->where('price',null)->where('parking_id',Auth::user()->parking_id)->count();
            if($count)
                return ;
        }
        $ticket= new Ticket();
        $ticket->hour =$now;
        $ticket->plate =strtoupper($request->plate);
        $ticket->status = 1;
        $ticket->type =$request->type;
        $ticket->schedule =$request->schedule;
        if($request->schedule==3){
            $dateRange = explode(" - ", $request->range);
            $ticket->date_end = new \Carbon\Carbon($dateRange[1]);
            $ticket->name = $request->name;
            $ticket->email = $request->email;
            $ticket->phone = $request->movil;
            $ticket->price = $request->price;
            $ticket->hour = new \Carbon\Carbon($dateRange[0]);
        }
        $ticket->parking_id = Auth::user()->parking_id;
        $ticket->partner_id = Auth::user()->partner_id;
        $ticket->drawer = $request->drawer;
        $ticket->convenio_id = $request->convenio;
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
        $iva = $request->isIva ?? 0;
        if(onlyIva())
            $iva=0;
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
        $marginRight = Auth::user()->parking_id == 5?57:45;
        $marginLeft = Auth::user()->parking_id == 5?2:6;
        $size = Auth::user()->parking_id == 5?'8px':'small';
        PDF::SetMargins($marginLeft, 0, $marginRight);
        $parking = Parking::find(Auth::user()->parking_id);
        $html = '<div style="text-align:center; margin-top: -10px !important"><big style="margin-bottom: 1px"><b style="letter-spacing: -1 px;">&nbsp; PARQUEADERO '.$parking->name.'</b></big><br>
                '.($parking->parking_id !=5 && $parking->parking_id !=11 && $parking->parking_id !=9?'<em style="font-size: 7px;margin-top: 2px;margin-bottom: 1px">"Todo lo puedo en Cristo que<br> me fortalece": Fil 4:13 <br></em>':'').'
                <small style="font-size: x-small;margin-top: 1px;margin-bottom: 1px"><b>'.$parking->address.'</b></small>'
            .($parking->parking_id==3?'<small style="text-align:center;font-size: 6px"><br>
    NIT: 1094965452-1 <br>OLIVEROS HERNANDEZ VALENTINA<br> </small><small style="text-align:center;font-size: '.(Auth::user()->parking_id != 5?'8px':'7px').'"><b>SERVICIO: Lun-Sab 7am - 9pm</b><br> <b> TEL: 3017601216</b></small>':'')
            .($parking->parking_id==4?'<small style="text-align:center;font-size: 7px"><br>
    <b>SERVICIO: Lun-Sab 7am - 9pm</b><br>CARLOS E. MIDEROS <br> NIT: 80449231-4 <br> TEL: 9207119<br> CEL: 3013830790</small>':'').
            ($parking->parking_id==11?'<small style="text-align:center;font-size: 7px"><br>
    <b>SERVICIO: Lun-Sab 6am - 9pm Dom-Fest 9am - 6pm</b><br>SOLUCIONES Y LOGÍSTICA SAS <br> NIT: 901305901-1 <br> autonorteparking@gmail.com</small>':'').
            ($parking->parking_id==9?'<small style="text-align:center;font-size: 7px">
    <b></b><br>NIT: 1090504432-0 <br> CEL. 3116512240</small>':'').
            ($parking->parking_id==5?'<small style="text-align:center;font-size: 6px"><br>
    NIT: 89000746-1 <br>HUGO ALEXANDER VARGAS SANCHEZ<br> </small><small style="text-align:center;font-size: 8px"><b>SERVICIO: Lun-Dom 6:30am - 9:30pm</b><br> <b> TEL: 3173799831</b></small>':'').
            ($parking->parking_id==7?'<small style="text-align:center;font-size: 6px"><br>
    NIT: 1041325245-3 <br>JHON DEIVID SANTA PULIDO<br> </small><small style="text-align:center;font-size: 8px"><b>SERVICIO: 24 HORAS</b><br> <b> TEL: 3217463250</b></small>':'');
        if(!isset($ticket->price)) {
            $html .= '<small style="text-align:left;font-size: '.$size.';margin-bottom: 1px;"><b><br>
                 ' . ($ticket->schedule==3 || $parking->parking_id==11? "RECIBO N° " . $ticket->ticket_id . "<br>" : '') .'
                 Fecha ingreso: ' . $hour->format('d/m/Y') . '<br>
                 Hora ingreso: ' . $hour->format('h:ia') . '<br>
                 ' . ($ticket->schedule==3? "   Fecha vencimiento: " . $hour2->format('d/m/Y') . "<br>" : '') .'
                 ' . ($ticket->schedule==3? "<b>".strtoupper($ticket->name) . "</b><br>" : '') .'
                 Tipo: ' . ($ticket->type == 1 ? 'Carro' : ($ticket->type == 3 ? ( isBici()?'Bicicleta':'Camioneta' ) : 'Moto')) . '<br>
                 <small style="text-align:left;font-size:small">Placa: ' . $ticket->plate . '</small><br>
                 ' . (isset($ticket->drawer) ? "Casco: " . $ticket->drawer . "<br>" : '') . '
                 </b></small>
                 '.($parking->parking_id==3?'':'
                 <small style="text-align:left;font-size: 6px;margin-top: 1px"><br>
                 1.El vehiculo se entregara al portador de este recibo<br>
                 2.No aceptamos ordenes escritas o por telefono<br>
                 3.No se responde por cascos dejados en la moto.<br>
                 4.No respondemos por objetos dejados en el carro mientras sus puertas esten aseguradas<br>
                 5.No somos responsables por daños o perdidas causadas en el parqueadero mientras el vehiculo no sea entregado personalmente<br>
                 6.No respondemos por la perdida, deterioro o daños ocurridos por causa de incendio, terremoto o causas similares, motin,conmosion civil, revolucion <br>y otros eventos que impliquen fuerza mayor.
                 </small>').'</div>';
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
                    FACTURA VENTA N° ' . $ticket->ticket_id . '<br>
                 ' . ($ticket->schedule==3?"<b>".strtoupper($ticket->name) . "</b><br>" : '') .'
                 ' . ($ticket->schedule==1? "   Fracciones: " . $horas . "<br>" : '') .'
                 Fecha ingreso: ' . $hour->format('d/m/Y') . '<br>
                 Hora ingreso: ' . $hour->format('h:ia') . '<br>
                 ' . ($ticket->schedule!=3? "   Fecha salida: " . $pay_day->format('d/m/Y') . "<br>" : '') .'
                 ' . ($ticket->schedule!=3? "   Hora salida: " . $pay_day->format('h:ia') . "<br>" : '') .'
                 ' . ($ticket->schedule!=3? "   Duración: " . $interval->format('%d D %h:%i') . "<br>" : '') .'
                 ' . ($ticket->schedule==3? "   Fecha vencimiento: " . $hour2->format('d/m/Y') . "<br>" : '') .'
                 Tipo: ' . ($ticket->type == 1 ? 'Carro' : ($ticket->type == 3 ? ( isBici()?'Bicicleta':'Camioneta' ) : 'Moto')) . '<br>
                 Placa: ' . $ticket->plate . '<br>
                 ' . (isset($ticket->price) && empty($iva)? "   Precio: " . $ticket->price . "<br>" : (isset($ticket->price) && !empty($iva)?'
                Valor servicio: '.intval($ticket->price/1.19).'<br>'.
                    (isset($ticket->extra) ? ($ticket->extra>0?"Incremento: ":"Descuento:" ). abs($ticket->extra).'<br>':'').'
                IVA           : '.(($ticket->price+$ticket->extra)-intval(($ticket->price+$ticket->extra)/1.19)).'<br>
                _____________________<br>
                Total         : '.(($ticket->price+$ticket->extra)):'')) .
                (isset($ticket->extra) && empty($iva)? ($ticket->extra>0?"<br>Incremento: ":"<br>Descuento:" ). abs($ticket->extra) . "<br>Total: " . ($ticket->price+$ticket->extra) . "<br>" : '').
                '</small>
</div>';
        }
        $html .= ($parking->parking_id==11?'<small style="text-align:center;font-size: 7px">
    <b>POLIZA No. 21-02-101009484</b><br>SEGUROS DEL ESTADO</small>':'').'<small style="text-align:left;font-size: 6px"><br>
                 <b>IMPRESO POR JORGBOT.COM 3214553997</b>
                 </small>';
        PDF::writeHTML($html, true, false, true, false, '');
        if(!isset($ticket->price) && (Auth::user()->parking_id!=11)){
        $id_bar = substr('0000000000'.$ticket->ticket_id,-10);
        PDF::write1DBarcode($id_bar, 'C128C', '', '', '', 18, 0.4, $style, 'N');
        }
        $js = 'this.print(true);';
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

    public function precio($tiempo, $tipo, $schedule, $convenio = null)
    {
        $horas = $tiempo->format("%H");
        $horas2 = $tiempo->format("%H");
        $minutos = $tiempo->format("%I");
        $dias = $tiempo->format("%d");
        $minutos2 = $minutos;
        $parking = Parking::find(Auth::user()->parking_id);
        $minutos = ($minutos*1) - ($parking->free_time);
        $horas = (24*$tiempo->format("%d"))+$horas*1 + (($minutos>=0? 1: 0)*1);
        if(!empty($convenio)){
            $convenio = Convenio::find($convenio);
            if(!empty($convenio)){
                if($tipo==1){
                    $parking->min_cars_price = $convenio->min_cars_price ?? $parking->min_cars_price;
                    $parking->hour_cars_price = $convenio->hour_cars_price ?? $parking->hour_cars_price;
                    $parking->day_cars_price = $convenio->day_cars_price ?? $parking->day_cars_price;
                }
                if($tipo==2){
                    $parking->min_motorcycle_price = $convenio->min_motorcycle_price ?? $parking->min_motorcycle_price;
                    $parking->hour_motorcycle_price = $convenio->hour_motorcycle_price ?? $parking->hour_motorcycle_price;
                    $parking->day_motorcycle_price = $convenio->day_motorcycle_price ?? $parking->day_motorcycle_price;
                }
                if($tipo==3){
                    $parking->min_van_price = $convenio->min_van_price ?? $parking->min_van_price;
                    $parking->hour_van_price = $convenio->hour_van_price ?? $parking->hour_van_price;
                    $parking->day_van_price = $convenio->day_van_price ?? $parking->day_van_price;
                }
            }
        }
        $dayPrice = ($tipo==1? $parking->day_cars_price : ($tipo==2?$parking->day_motorcycles_price:$parking->day_van_price));
        if(($parking->parking_id==11) && $schedule==1){
            $minutos2 = (((24*$tiempo->format("%d"))+$horas2*1)*60)+($minutos2*1);
            $priceMin = $minutos2 > 0?($tipo==1? $parking->min_cars_price*$minutos2: ($tipo==2?$parking->min_motorcycles_price*$minutos2:$parking->min_van_price*$minutos2)):0;
            if($schedule==1 && ($priceMin < $dayPrice || $dayPrice == 0))
                return intval(round($priceMin*1/100)*100);
            else
                $schedule=2;
        }
        if($tiempo->format("%I")<=5 && $horas==0 && ($schedule==1 || $schedule==2 || $schedule==4))
            return 0;
        $horas = $horas==0? 1: $horas;
        if(Auth::user()->parking_id == 9 && empty($convenio)){
            $minutos = $minutos2*1 <= 2 && $tipo==1? 0.25 :($minutos2*1 <= 30 ? 0.5 :($minutos2*1 <= 55 && $tipo==1? 1 :1));
            $horas = $horas2 + $minutos;
        }
        if($schedule==1){
            $price= ($tipo==1? $parking->hour_cars_price * $horas: ($tipo==2? $parking->hour_motorcycles_price * $horas: $parking->hour_van_price * $horas ));
            if($price < $dayPrice || $dayPrice == 0 || Auth::user()->parking_id == 3 )
                return intval($price);
            else{
                if(isJornada())
                    $schedule=4;
                else
                    $schedule=2;
            }
        }
        if($schedule==2)
            return ($tipo==1? $parking->day_cars_price: ($tipo==2? $parking->day_motorcycles_price: $parking->day_van_price ))* ($dias+1);
        if($schedule==3)
            return ($tipo==1? $parking->monthly_cars_price: ($tipo==2? $parking->monthly_motorcycles_price: $parking->monthly_van_price ));
        if($schedule==4){
            $dias = (intval($horas/12)+1+($dias*2))/2;
            return ($tipo==1? $parking->day_cars_price: ($tipo==2? $parking->day_motorcycles_price: $parking->day_van_price ))* ($dias);
        }
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
            $ticket->price = $this->precio($interval,$ticket->type, $ticket->schedule, $ticket->convenio_id);
        if($ticketss->count() > 0)
            $ticket->price =0;
        $ticket->partner_id = Auth::user()->partner_id;
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
    public function getTickets(Request $request)
    {
        $search = $request->get('search')['value'];
        $schedule = $request->get('type');
        $type = $request->get('type_car');
        $range = $request->get('range');
        $status = $request->get('status');
        $partner = $request->get('partner');

        $tickets= Ticket::select(['ticket_id as Id', 'plate', 'type', 'schedule', 'partner_id', 'status', 'drawer', 'price','hour','convenio_id'])->where('parking_id',Auth::user()->parking_id)->orderBy('ticket_id','desc');
        if ($search) {
                $tickets = $tickets->where('plate', 'LIKE', "%$search%");
        }
        if (!empty($status))
            $tickets = $tickets->where('status', $status);
        if (!empty($schedule))
            $tickets = $tickets->where('schedule', $schedule);
        if (!empty($type))
            $tickets = $tickets->where('type', $type);
        if (!empty($partner))
            $tickets = $tickets->where('partner_id', $partner);
        if(!(Auth::user()->parking_id == 3 && !empty($search))){
            if (!empty($range)){
                $dateRange = explode(" - ", $range);
                $tickets = $tickets->whereBetween('created_at', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59']);
            }else{
                $tickets = $tickets->whereBetween('created_at', [ new Datetime('today'), new Datetime('tomorrow')]);
            }
        }
        return Datatables::of($tickets)
            ->addColumn('action', function ($tickets) {
                $edit = \Form::button('Editar', [
                    'class'   => 'btn btn-primary',
                    'onclick' => "openModalMod('$tickets->Id')",
                    'data-toggle' => "tooltip",
                    'data-placement' => "bottom",
                    'title' => "Editar !",

                ]);
                $htmlAdmin= $edit.
                    \Form::button('Eliminar', [
                        'class'   => 'btn btn-warning',
                        'onclick' => "eliminarTicket('$tickets->Id')",
                        'data-toggle' => "tooltip",
                        'data-placement' => "bottom",
                        'title' => "Eliminar !",

                    ]);
                if ($tickets->status == 1)
                return \Form::button('Pagar', [
                        'class'   => 'btn btn-info',
                        'onclick' => "$('#modal_ticket_out').modal('show');$('#ticket_id').val('$tickets->Id')",
                        'data-toggle' => "tooltip",
                        'data-placement' => "bottom",
                        'title' => "Pagar !",

                    ]).(Auth::user()->type == 1?$htmlAdmin:(Auth::user()->type == 2 && isconvenio()?$edit:'')).
                    \Form::button('Imprimir', [
                        'class'   => 'btn btn-info',
                        'onclick' => "form_pdf('$tickets->Id')",
                        'data-toggle' => "tooltip",
                        'data-placement' => "bottom",
                        'title' => "Imprimir !",

                    ]);
                else
                    return (Auth::user()->type == 1?$htmlAdmin:'').
                        \Form::button('Imprimir', [
                            'class'   => 'btn btn-info',
                            'onclick' => "form_pdf('$tickets->Id')",
                            'data-toggle' => "tooltip",
                            'data-placement' => "bottom",
                            'title' => "Imprimir !",

                        ]).($tickets->schedule != 3?
                        \Form::button('Recuperar', [
                            'class'   => 'btn btn-info',
                            'onclick' => "recuperarTicket('$tickets->Id')",
                            'data-toggle' => "tooltip",
                            'data-placement' => "bottom",
                            'title' => "Recuperar !",

                        ]):'').(isIva()?\Form::button('Imprimir IVA', [
                            'class'   => 'btn btn-info',
                            'onclick' => "form_pdf_iva('$tickets->Id')",
                            'data-toggle' => "tooltip",
                            'data-placement' => "bottom",
                            'title' => "Imprimir !",

                        ]):'');
            })
            ->addColumn('Tipo', function ($tickets) {
                return  $tickets->type == 1? 'Carro': ($tickets->type == 3 ? ( isBici()?'Bicicleta':'Camioneta' ) : 'Moto');
            })
            ->addColumn('entrada', function ($tickets) {
                $hour =new DateTime("".$tickets->hour);
                return  $hour->format('h:ia');
            })
            ->addColumn('Estado', function ($tickets) {
                return  $tickets->status == 1? 'Pendiente Pago': 'Pagó';
            })
            ->addColumn('Atendio', function ($tickets) {
                $partner = Partner::find($tickets->partner_id);
                return  $partner->name;
            })
            ->editColumn('price', function ($tickets) {
                $now = new Datetime('now');
                $interval = date_diff(new DateTime("".$tickets->hour),$now);
                return isset( $tickets->price)?  $tickets->price:( "*".$this->precio($interval,$tickets->type, $tickets->schedule, $tickets->convenio_id));
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

                        ]).(!empty($tickets->phone)?'<a class="btn btn-success" href="https://web.whatsapp.com/send?phone=57'.$tickets->phone.'&text=Hola%20'.$tickets->name.',parqueadero%20'.$parking->name.'%20le%20saluda%20coordialmente%20y%20le%20informa%20que%20el%20vehiculo%20con%20placa%20'.$tickets->plate.'%20tiene%20pago%20el%20parqueo%20con%20nosotros%20hasta%20la%20fecha:%20'.$tickets->date_end.'" target="_blank">Whatsapp</a>':'');
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
            $tickets = $tickets->whereBetween('created_at', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59']);
        }else{
            $tickets = $tickets->whereBetween('created_at', [ new Datetime('today'), new Datetime('tomorrow')]);
        }
        $status = [];
        $status['total'] = ZERO;
        $status['extra'] = ZERO;
        $status['carros'] = ZERO;
        $status['camioneta'] = ZERO;
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
            if($ticket->type == 3)
                $status['camioneta'] ++;
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
        if($status['camioneta'] > 0)
            $status['motos'] = $status['motos'] .' / '.$status['camioneta'];
        $status['total'] = format_money($status['total']);
        $status['extra'] = format_money($status['extra']);
        return $status;
    }
    public function getTicket(Request $request)
    {
        $ticket = Ticket::find($request->ticket_id);
        return $ticket;
    }
    public function updateTicket(Request $request)
    {
        $ticket = Ticket::find($request->ticket_id);
        $now = new Datetime('now');
        $ticket->plate =$request->plate;
        $ticket->type =$request->type;
        $ticket->schedule =$request->schedule;
        if($request->schedule==3){
            $dateRange = explode(" - ", $request->range);
            $ticket->date_end = new \Carbon\Carbon($dateRange[1]);
            $ticket->name = $request->name;
            $ticket->hour = new \Carbon\Carbon($dateRange[0]);
            $ticket->email = $request->email;
            $ticket->phone = $request->movil;
            $ticket->price = $request->price;
        }
        $ticket->convenio_id = $request->convenio;
        $ticket->partner_id = Auth::user()->partner_id;
        $ticket->extra = $request->extra;
        $ticket->drawer = $request->drawer;
        $ticket->save();
        return ;
    }
    public function deleteTicket(Request $request)
    {
        $ticket = Ticket::find($request->ticket_id);
        $ticket->partner_id = Auth::user()->partner_id;
        $ticket->save();
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
            $ticket->phone = $tickets->phone;
            $ticket->price = $tickets->price;
        }
        $ticket->parking_id = Auth::user()->parking_id;
        $ticket->partner_id = Auth::user()->partner_id;
        $ticket->drawer = $tickets->drawer;
        $ticket->save();

        return ;
    }

    public function export($range){

        if(Auth::user()->type == 1)
            return Excel::download(new TicketsExport($range), 'Reporte_'.$range.'.xlsx');
        else
            $this->reportUser($range);
    }

    public function reportUser($range)
    {
        $tickets= Ticket::select(['plate', 'type', 'extra', 'schedule', 'price', 'name', 'status', 'date_end'])->where('parking_id',Auth::user()->parking_id)->where('status','<>',"3")->orderBy('ticket_id','desc');
        $tickets = $tickets->where('status', 2);
        $tickets = $tickets->where('partner_id', Auth::user()->partner_id);
        $dateRange = explode(" - ", $range);
        $tickets = $tickets->whereBetween('created_at', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59']);

        $status = [];
        $iva = $request->isIva ?? 0;
        if(onlyIva())
            $iva=0;
        $status['total'] = ZERO;
        $status['extra'] = ZERO;
        $status['carros'] = ZERO;
        $status['camioneta'] = ZERO;
        $status['motos'] = ZERO;

        $tickets=$tickets->get();
        foreach ($tickets as $ticket){
            
            $status['total'] += $ticket->price;
            $status['extra'] += $ticket->extra;
            if($ticket->type == 1)
                $status['carros'] ++;
            if($ticket->type == 2)
                $status['motos'] ++;
            if($ticket->type == 3)
                $status['camioneta'] ++;
        }
        PDF::SetTitle('Reporte PDF');
        PDF::AddPage('P', 'A6');
        $marginRight = Auth::user()->parking_id == 5?57:45;
        $marginLeft = Auth::user()->parking_id == 5?2:6;
        PDF::SetMargins($marginLeft, 0, $marginRight);

        $html = '<table style="width:100%">
        <tr>
        <th>'.$dateRange[0].'</th>
        <th>'.$dateRange[1].'</th> 
        <th>&nbsp;</th>
      </tr>
      <tr>
        <td colspan="1"><b>USER</b></td>
        <td><b>'.Auth::user()->name.'</b></td> 
      </tr>
      <tr>
        <td colspan="1"><b>Carros</b></td>
        <td><b>'.$status['carros'].'</b></td> 
      </tr>
      <tr>
        <td colspan="1"><b>Motos</b></td>
        <td><b>'.$status['motos'].'</b></td> 
      </tr>
     
      <hr>

      <tr>
        <td colspan="1"><b>Saldo</b></td>
        <td><b>$'.$status['total'].'</b></td>
      </tr>
      <hr>
  </table>';
        PDF::writeHTML($html, true, false, true, false, '');
        $js = 'print(true);';
        PDF::IncludeJS($js);
        PDF::Output('ticket.pdf');
    }
}
