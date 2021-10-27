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


class ConvenioController extends Controller
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

        $ticket= new Convenio();
        $ticket->parking_id = Auth::user()->parking_id;
        $ticket->name = $request->name;
        $ticket->min_cars_price = $request->min_cars_price;
        $ticket->hour_cars_price = $request->hour_cars_price;
        $ticket->day_cars_price = $request->day_cars_price;
        $ticket->min_motorcycles_price = $request->min_motorcycles_price;
        $ticket->hour_motorcycles_price = $request->hour_motorcycles_price;
        $ticket->day_motorcycles_price = $request->day_motorcycles_price;
        $ticket->min_van_price = $request->min_van_price;
        $ticket->hour_van_price = $request->hour_van_price;
        $ticket->day_van_price = $request->day_van_price;
        $ticket->save();

        return $ticket->convenio_id;
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
        $ticket = Convenio::find($request->convenio);
        if(!empty($ticket)){
            $ticket->name = $request->name;
            $ticket->min_cars_price = $request->min_cars_price;
            $ticket->hour_cars_price = $request->hour_cars_price;
            $ticket->day_cars_price = $request->day_cars_price;
            $ticket->min_motorcycles_price = $request->min_motorcycles_price;
            $ticket->hour_motorcycles_price = $request->hour_motorcycles_price;
            $ticket->day_motorcycles_price = $request->day_motorcycles_price;
            $ticket->min_van_price = $request->min_van_price;
            $ticket->hour_van_price = $request->hour_van_price;
            $ticket->day_van_price = $request->day_van_price;
            $ticket->save();
        }
        return $ticket->name??'';
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
    public function getConvenios(Request $request)
    {
        $search = $request->get('search')['value'];

        $tickets= Convenio::select(['convenio_id as Id', 'name', 'hour_motorcycles_price', 'day_motorcycles_price', 'min_motorcycles_price', 'hour_cars_price', 'day_cars_price', 'min_cars_price', 'hour_van_price', 'day_van_price', 'min_van_price'])
            ->where('parking_id',Auth::user()->parking_id)
            ->orderBy('convenio_id','desc');
        if ($search) {
                $tickets = $tickets->where('name', 'LIKE', "%$search%");
        }

        return Datatables::of($tickets)
            ->addColumn('action', function ($tickets) {
                $htmlAdmin= \Form::button('Editar', [
                        'class'   => 'btn btn-primary',
                        'onclick' => "openModalModConvenio('$tickets->Id')",
                        'data-toggle' => "tooltip",
                        'data-placement' => "bottom",
                        'title' => "Editar !",

                    ]).
                    \Form::button('Eliminar', [
                        'class'   => 'btn btn-warning',
                        'onclick' => "eliminarConvenio('$tickets->Id')",
                        'data-toggle' => "tooltip",
                        'data-placement' => "bottom",
                        'title' => "Eliminar !",

                    ]);
                return Auth::user()->type == 1?$htmlAdmin:'';
            })
            ->addColumn('moto', function ($tickets) {
                $text = (!empty($tickets->min_motorcycles_price)?format_money($tickets->min_motorcycles_price):'');
                $text .= (!empty($tickets->hour_motorcycles_price)?(!empty($text)?' / ':'').format_money($tickets->hour_motorcycles_price):'');
                $text .= (!empty($tickets->day_motorcycles_price)?(!empty($text)?' / ':'').format_money($tickets->day_motorcycles_price):'');
                return  $text;
            })
            ->addColumn('carro', function ($tickets) {
                $text = (!empty($tickets->min_cars_price)?format_money($tickets->min_cars_price):'');
                $text .= (!empty($tickets->hour_cars_price)?(!empty($text)?' / ':'').format_money($tickets->hour_cars_price):'');
                $text .= (!empty($tickets->day_cars_price)?(!empty($text)?' / ':'').format_money($tickets->day_cars_price):'');
                return  $text;
            })
            ->addColumn('camioneta', function ($tickets) {
                $text = (!empty($tickets->min_van_price)?format_money($tickets->min_van_price):'');
                $text .= (!empty($tickets->hour_van_price)?(!empty($text)?' / ':'').format_money($tickets->hour_van_price):'');
                $text .= (!empty($tickets->day_van_price)?(!empty($text)?' / ':'').format_money($tickets->day_van_price):'');
                return  $text;
            })
            ->make(true);
    }


    public function getConvenio(Request $request)
    {
        $ticket = Convenio::find($request->convenio);
        return $ticket;
    }

    public function deleteConvenio(Request $request)
    {
        $ticket = Convenio::find($request->convenio);
        $ticket->delete();
        return ;
    }
    public function recoveryTicket(Request $request)
    {
        $ticket = Convenio::find($request->convenio);
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
    public function export($range)
    {
        return Excel::download(new TicketsExport($range), 'Reporte_'.$range.'.xlsx');
    }
}
