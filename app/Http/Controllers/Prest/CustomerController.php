<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Parking;
use App\Ticket;
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


class CustomerController extends Controller
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
        $ticket= new Customer();
        $ticket->nombre =strtoupper($request->name);
        $ticket->telefono =$request->movil;
        $ticket->cedula =$request->cedula;
        $ticket->observacion =$request->observacion;
        $ticket->id_partner =Auth::user()->partner_id;
        $ticket->id_parking =Auth::user()->parking_id;
        $ticket->save();

        /*Nexmo::message()->send([
            'to'   => '573207329971',
            'from' => '573207329971',
            'text' => 'te amo care nalga camila.'
        ]);*/
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
        $ticket = Customer::find($request->id);
        $ticket->nombre =strtoupper($request->name);
        $ticket->telefono =$request->movil;
        $ticket->cedula =$request->cedula;
        $ticket->observacion =$request->observacion;
        $ticket->save();
        return [$ticket->cedula,$ticket->nombre];
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
    public function getCustomer(Request $request)
    {
        $ticket = Customer::find($request->cliente_id);
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
        $ticket->partner_id = Auth::user()->partner_id;
        $ticket->extra = $request->extra;
        $ticket->drawer = $request->drawer;
        $ticket->save();
        return ;
    }
    public function deleteTicket(Request $request)
    {
        $ticket = Ticket::find($request->ticket_id);
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
        $customers = Customer::where('id_parking' ,Auth::user()->parking_id)->get();
        $select="<option value=''>Seleccionar</option>";
        foreach ($customers as $customer){
            $select .='<option data-toggle="tooltip" title="'.$customer->observacion.'"value="'.$customer->id_customer.'">'.$customer->nombre.'</option>';
        }
        return $select;
    }
}
