<?php

namespace App\Http\Controllers;

use App\Abono;
use App\Customer;
use App\Models\Partner;
use App\Parking;
use App\Ticket;
use App\Prestamo;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Html\HtmlServiceProvider;
use Nexmo\Laravel\Facade\Nexmo;
use App\Notifications\Message;

use PDF; // at the top of the file


class PrestamoController extends Controller
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
        $ticket= new Prestamo();
        $ticket->id_customer =$request->customer;
        $ticket->interes =$request->interes;
        $ticket->tiempo =$request->tiempo;
        $ticket->tipo =$request->tipo;
        $ticket->monto =$request->monto;
        $ticket->cuota =$request->cuota;
        $ticket->actual = ceil($request->cuota*($request->tiempo*$request->tipo)/100)*100;
        $ticket->estado = 1;
        $ticket->created_at = $request->fecha;
        $ticket->id_partner =Auth::user()->partner_id;
        $ticket->save();

        return $ticket->ticket_id;
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
            return ($tipo==1? $parking->hour_cars_price * $horas: $parking->hour_motorcycles_price * $horas );
        if($schedule==2)
            return ($tipo==1? $parking->day_cars_price: $parking->day_motorcycles_price);
        if($schedule==3)
            return ($tipo==1? $parking->monthly_cars_price: $parking->monthly_motorcycles_price);
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
    public function getTickets(Request $request)
    {
        $search = $request->get('search')['value'];
        $schedule = $request->get('type');
        $type = $request->get('type_car');
        $range = $request->get('range');
        $status = $request->get('status');

        $tickets= Ticket::select(['ticket_id as Id', 'plate', 'type', 'schedule', 'partner_id', 'status', 'drawer', 'price','hour'])->where('parking_id',Auth::user()->parking_id)->orderBy('ticket_id','desc');
        if ($search) {
                $tickets = $tickets->where('plate', 'LIKE', "%$search%");
        }
        if (!empty($status))
            $tickets = $tickets->where('status', $status);
        if (!empty($schedule))
            $tickets = $tickets->where('schedule', $schedule);
        if (!empty($type))
            $tickets = $tickets->where('type', $type);
        if (!empty($range)){
            $dateRange = explode(" - ", $range);
            $tickets = $tickets->whereBetween('created_at', [$dateRange[0], $dateRange[1]]);
        }else{
            $tickets = $tickets->whereBetween('created_at', [ new Datetime('today'), new Datetime('tomorrow')]);
        }
        return Datatables::of($tickets)
            ->addColumn('action', function ($tickets) {
                $htmlAdmin= \Form::button('Editar', [
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

                    ]);
                if ($tickets->status == 1)
                return \Form::button('Pagar', [
                        'class'   => 'btn btn-info',
                        'onclick' => "$('#modal_ticket_out').modal('show');$('#ticket_id').val('$tickets->Id')",
                        'data-toggle' => "tooltip",
                        'data-placement' => "bottom",
                        'title' => "Pagar !",

                    ]).(Auth::user()->type == 1?$htmlAdmin:'').
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

                        ]):'');
            })
            ->addColumn('Tipo', function ($tickets) {
                return  $tickets->type == 1? 'Carro': 'Moto';
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
                return isset( $tickets->price)?  $tickets->price:( "*".$this->precio($interval,$tickets->type, $tickets->schedule));
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
            ->make(true);
    }
    public function getStatus(Request $request)
    {
        $schedule = $request->get('type');
        $status = $request->get('status');
        $customer = $request->get('customer');

        $tickets= Prestamo::select(['id_prestamo as Id', 'id_customer', 'interes', 'monto', 'cuota', 'actual', 'tipo', 'tiempo', 'created_at as Fecha','estado'])
            ->where('id_partner',Auth::user()->partner_id)
            ->orderBy('id_prestamo','desc');
        if (!empty($customer))
            $tickets = $tickets->where('id_customer', $customer);
        if (!empty($status))
            $tickets = $tickets->where('estado', $status);
        if (!empty($schedule))
            $tickets = $tickets->where('tipo', $schedule);
        $status = [];
        $status['total'] = ZERO;
        $status['extra'] = ZERO;
        $status['carros'] = ZERO;
        $status['motos'] = ZERO;
        $status['month_expire'] = '';
        $status['month_expire_num'] = ZERO;
        $tickets=$tickets->get();
        $now = new Datetime('now');
        foreach ($tickets as $ticket){
            if($ticket->estado ==2)
                $saldo = 0;
            else{
                $now = new Datetime('now');
                $interval = date_diff(new DateTime("".$ticket->Fecha),$now);
                $meses = ($interval->format("%M")*1)+($interval->format("%d")*1>=5?1:0)+($interval->format("%Y")*12);
                $abonos = collect(Abono::select(['valor'])->where('id_prestamo',$ticket->Id)->get())->sum('valor');
                $saldo = (($ticket->monto*$ticket->interes/100)*($meses==0?1:$meses*1))+($ticket->monto*1)-($abonos*1);
            }

            $status['total'] += ($saldo*1);
            if($ticket->estado == 1)
                $status['carros'] ++;
            if($ticket->estado == 2)
                $status['motos'] ++;
        }
        $tickets= Prestamo::select(['id_prestamo', 'id_customer', 'interes', 'monto', 'cuota', 'actual', 'tipo', 'tiempo', 'created_at','estado'])
            ->where('id_partner',Auth::user()->partner_id)
            ->where('estado','1')
            ->orderBy('id_prestamo','desc')->get();
        foreach ($tickets as $ticket){
            $abonos = Abono::select(['id_abono'])->where('id_prestamo',$ticket->id_prestamo)->count();
            $interval = date_diff(new DateTime("".$ticket->created_at),$now);
            $meses = ($interval->format("%M")*1)+($interval->format("%Y")*12);
            $quincena = $ticket->tipo==2?($interval->format("%d")*1>=15?1:0)+($meses*2):0;
            if($meses > $abonos && $ticket->tipo==1){
                $customer = Customer::find($ticket->id_customer,['nombre']);
                $status['month_expire'] .= $customer->nombre.' ('.($meses-$abonos).') <br>';
                $status['month_expire_num'] ++;
            }
            if($quincena > $abonos && $ticket->tipo==2){
                $customer = Customer::find($ticket->id_customer,['nombre']);
                $status['month_expire'] .= $customer->nombre.' ('.($quincena-$abonos).') <br>';
                $status['month_expire_num'] ++;
            }
        }

        return $status;
    }
    public function getPrestamo(Request $request)
    {
        $ticket = Prestamo::find($request->prestamo_id);
        return $ticket;
    }
    public function updatePrestamo(Request $request)
    {
        $ticket = Prestamo::find($request->prestamo);
        $ticket->id_customer =$request->customer;
        $ticket->interes =$request->interes;
        $ticket->tiempo =$request->tiempo;
        $ticket->tipo =$request->tipo;
        $ticket->monto =$request->monto;
        $ticket->cuota =$request->cuota;
        $ticket->actual = ceil($request->cuota*($request->tiempo*$request->tipo)/100)*100;
        $ticket->estado = 1;
        $ticket->created_at = $request->fecha;
        $ticket->save();
        return ;
    }
    public function deletePrestamo(Request $request)
    {
        $ticket = Prestamo::find($request->prestamo);
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
    public function getPrestamos(Request $request)
    {
        $search = $request->get('search')['value'];
        $schedule = $request->get('type');
        $range = $request->get('range');
        $status = $request->get('status');
        $customer = $request->get('customer');

        $tickets= Prestamo::select(['id_prestamo as Id', 'id_customer', 'interes', 'monto', 'cuota', 'actual', 'tipo', 'tiempo', 'created_at as Fecha','estado'])
            ->where('id_partner',Auth::user()->partner_id)
            ->orderBy('id_prestamo','desc');
        //dd($tickets->toSql());
        if ($search) {
            $tickets = $tickets->where('monto', 'LIKE', "%$search%");
        }
        if (!empty($customer))
            $tickets = $tickets->where('id_customer', $customer);
        if (!empty($status))
            $tickets = $tickets->where('estado', $status);
        if (!empty($schedule))
            $tickets = $tickets->where('tipo', $schedule);
        $saldo = 'hola';
        return Datatables::of($tickets)
            ->addColumn('saldo', function ($tickets) use ($saldo){
                if($tickets->estado ==2)
                    return '$0';
                $now = new Datetime('now');
                $interval = date_diff(new DateTime("".$tickets->Fecha),$now);
                $meses = ($interval->format("%M")*1)+($interval->format("%d")*1>=5?1:0)+($interval->format("%Y")*12);
                $abonos = collect(Abono::select(['valor'])->where('id_prestamo',$tickets->Id)->get())->sum('valor');
                $saldo = '$'.number_format((($tickets->monto*$tickets->interes/100)*($meses==0?1:$meses*1))+($tickets->monto*1)-($abonos*1),0,'','.');
                return $saldo;
            })
            ->addColumn('action', function ($tickets) use ($saldo){
                $customer = Customer::find($tickets->id_customer,['telefono','nombre']);
                    return
                        \Form::button('Editar', [
                            'class'   => 'btn btn-info',
                            'onclick' => "openModalPrestamoMod('$tickets->Id')",
                            'data-toggle' => "tooltip",
                            'data-placement' => "bottom",
                            'title' => "Editar !",

                        ]).
                        \Form::button('Eliminar', [
                            'class'   => 'btn btn-warning',
                            'onclick' => "eliminarPrestamo('$tickets->Id')",
                            'data-toggle' => "tooltip",
                            'data-placement' => "bottom",
                            'title' => "Eliminar !",

                        ]).
                        \Form::button('Listar Abonos', [
                            'class'   => 'btn btn-default',
                            'onclick' => "listarAbonos('$tickets->Id')",
                            'data-toggle' => "tooltip",
                            'data-placement' => "bottom",
                            'title' => "Pagar !",

                        ]).
                        \Form::button('Pagar todo', [
                            'class'   => 'btn btn-danger',
                            'onclick' => "openModalAbono('$tickets->Id',2,$tickets->cuota)",
                            'data-toggle' => "tooltip",
                            'data-placement' => "bottom",
                            'title' => "Pagar !",

                        ]).
                        \Form::button('Editar Cliente', [
                            'class'   => 'btn btn-primary',
                            'onclick' => "openModalClienteMod($tickets->id_customer)",
                            'data-toggle' => "tooltip",
                            'data-placement' => "bottom",
                            'title' => "Editar Cliente",

                        ]).(
                            \Form::button('Abonar', [
                                'class'   => 'btn btn-info',
                                'onclick' => "openModalAbono('$tickets->Id',1,$tickets->cuota)",
                                'data-toggle' => "tooltip",
                                'data-placement' => "bottom",
                                'title' => "Abonar !!!",

                            ])).
                        (!empty($customer->telefono)?'<a class="btn btn-success" href="https://api.whatsapp.com/send?phone=57'.$customer->telefono.'&text=Hola%20'.$customer->name.',feliz%20día%20le%20saludo%20coordialmente%20y%20le%20informo%20que" target="_blank">Whatsapp</a>':'');
            })
            ->editColumn('monto', function ($tickets) {
                return '$'.number_format($tickets->monto,0,'','.');
            })
            ->editColumn('cuota', function ($tickets) {
                return '$'.number_format($tickets->cuota,0,'','.');
            })
            ->editColumn('tipo', function ($tickets) {
                return $tickets->tipo==1?'Mensual':'Quincenal';
            })
            ->editColumn('tiempo', function ($tickets) {
                return $tickets->tiempo*$tickets->tipo;
            })
            ->editColumn('id_customer', function ($tickets) {
                $customer = Customer::find($tickets->id_customer,['nombre']);
                return $customer->nombre;
            })
            ->editColumn('Fecha', function ($tickets) {
                return substr($tickets->Fecha,0,10);
            })
            ->make(true);
    }
}
