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

use PDF; // at the top of the file


class IncomeController extends Controller
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
        //return 2;
        $id_transaction =$request->transaction;
        $gasto =$request->gasto??'';
        if(empty($request->transaction)){
            $transaction = new Transaction();
            $transaction->precio = 0;
            $transaction->parking_id = Auth::user()->parking_id;
            $transaction->partner_id = Auth::user()->partner_id;
            $transaction->description = strtoupper($request->descripcion??'');
            if(empty($gasto)) {
                $transaction->tipo = 1;
            }
            else
                $transaction->tipo = 2;

            $transaction->save();
            $id_transaction = $transaction->id_transaction;
        }
        $transaction = Transaction::find($id_transaction);
        if($request->product == ''){
            $transaction->customer_id = $request->customer;
            $transaction->save();
            $return['transaction_id'] = $id_transaction;
            $return['precio'] = format_money($transaction->precio);
            return $return;
        }
        $product = Product::find($request->product);
        $income = new Income();
        $income->cantidad =$request->cantidad?? -1;
        $income->product_id =$request->product;
        $income->transaction_id =$id_transaction;
        $income->parking_id = Auth::user()->parking_id;
        if(empty($gasto))
            $income->precio = $product->precio*1*$income->cantidad;
        else
            $income->precio = 0;

        $income->save();

        if($product->cantidad != '-1'){
            if(empty($gasto)){
                $product->cantidad = $product->cantidad - $income->cantidad;
            }else{
                $product->cantidad = $product->cantidad + $income->cantidad;
            }
            $product->save();
        }
        $transaction = Transaction::find($id_transaction);
        $transaction->description = strtoupper($request->descripcion??'');
        $transaction->customer_id = $request->customer;
        if(empty($gasto)) {
            $transaction->tipo = 1;
        }
        else
            $transaction->tipo = 2;

        if(empty($gasto))
            $transaction->precio = $transaction->precio +$income->precio;
        else
            $transaction->precio = $request->precio;

        $transaction->save();
        $return['transaction_id'] = $id_transaction;
        $return['precio'] = format_money($transaction->precio);

        return $return;
    }
    public function storeA(Request $request)
    {
        //return 2;
        $id_transaction =$request->transaction;
        $fecha =$request->fecha;
        if(empty($request->transaction)){
            $transaction = new Transaction();
            $transaction->precio = 0;
            $transaction->parking_id = Auth::user()->parking_id;
            $transaction->partner_id = Auth::user()->partner_id;
            $transaction->description = strtoupper($request->descripcion??'');
            $transaction->tipo = 1;
            $transaction->save();
            $id_transaction = $transaction->id_transaction;
        }
        $transaction = Transaction::find($id_transaction);
        if($request->product == ''){
            $transaction->created_at = Carbon::parse($fecha??'');
            $transaction->description = $request->descripcion??'';
            $transaction->save();
            $return['transaction_id'] = $id_transaction;
            $return['precio'] = format_money($transaction->precio);
            return $return;
        }
        $product = Product::find($request->product);
        $income = new Income();
        $income->cantidad =$request->cantidad?? 0;
        $income->product_id =$request->product;
        $income->transaction_id =$id_transaction;
        $income->parking_id = Auth::user()->parking_id;
        $income->precio = $request->precio;

        $income->save();
        if(Auth::user()->parking_id == 8)
            $product->precio = round((($product->cantidad*($product->precio*1)) + ($income->cantidad*($income->precio*1))) /(( $product->cantidad+$income->cantidad !=0? (int) $product->cantidad+$income->cantidad:1)),2);
        else
            $product->precio = intval((($product->cantidad*($product->precio*1)) + ($income->cantidad*($income->precio*1))) /(( $product->cantidad+$income->cantidad !=0? (int) $product->cantidad+$income->cantidad:1)));

        $product->cantidad = ($product->cantidad*1) + ($income->cantidad*1);
        $product->save();

        $transaction->created_at = Carbon::parse($fecha??'');
        $transaction->description = $request->descripcion??'';
        $transaction->precio = $transaction->precio*1 +($income->precio*1*$income->cantidad);
        $transaction->save();
        $return['transaction_id'] = $id_transaction;
        $return['precio'] = format_money($transaction->precio);

        return $return;
    }
    public function storeAcueducto(Request $request)
    {
        //return 2;
        $id_transaction =$request->transaction;
        $fecha =$request->fecha;
        $tipo =$request->tipo;
        if(empty($request->transaction)){
            $transaction = new Transaction();
            $transaction->precio = 0;
            $transaction->parking_id = Auth::user()->parking_id;
            $transaction->partner_id = Auth::user()->partner_id;
            $transaction->description = strtoupper($request->descripcion??'');
            $transaction->tipo = $tipo;
            $transaction->save();
            $id_transaction = $transaction->id_transaction;
        }
        $transaction = Transaction::find($id_transaction);
        if($request->product == ''){
            $transaction->created_at = Carbon::parse($fecha??'');
            $transaction->description = $request->descripcion??'';
            $transaction->tipo = $tipo??'';
            $transaction->save();
            $return['transaction_id'] = $id_transaction;
            $return['precio'] = format_money($transaction->precio);
            return $return;
        }
        $product = Product::find($request->product);
        $income = new Income();
        $income->cantidad =$request->cantidad?? -1;
        $income->product_id =$request->product;
        $income->transaction_id =$id_transaction;
        $income->parking_id = Auth::user()->parking_id;
        $income->precio = round($request->cantidad*$product->precio,2);

        $income->save();

        $product->cantidad = ($product->cantidad*1) - ($income->cantidad*1);
        $product->save();

        $transaction->created_at = Carbon::parse($fecha??'');
        $transaction->description = $request->descripcion??'';
        $transaction->tipo = $tipo??'';
        $transaction->precio = $transaction->precio*1 +($income->precio);
        $transaction->save();
        $return['transaction_id'] = $id_transaction;
        $return['precio'] = format_money($transaction->precio);

        return $return;
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
    public function getIncomes(Request $request)
    {
        $search = $request->get('search')['value'];
        $transaction = $request->get('transaction');
        $tipo = $request->get('tipo')??'';

        $tickets= Income::select(['id_income as Id', 'precio', 'product_id', 'cantidad','description'])->where('parking_id',Auth::user()->parking_id)->where('transaction_id', $transaction)->orderBy('id_income','desc');

        return Datatables::of($tickets)
            ->addColumn('action', function ($tickets) use($tipo){
                    return $tipo==2?\Form::button('Eliminar', [
                        'class'   => 'btn btn-warning',
                        'onclick' => "eliminarIncome2('$tickets->Id')",
                        'data-toggle' => "tooltip",
                        'data-placement' => "bottom",
                        'title' => "Eliminar !",

                    ]):\Form::button('Eliminar', [
                        'class'   => 'btn btn-warning',
                        'onclick' => "eliminarIncome('$tickets->Id')",
                        'data-toggle' => "tooltip",
                        'data-placement' => "bottom",
                        'title' => "Eliminar !",

                    ]);
            })
            ->editColumn('precio', function ($tickets) {
                return format_money($tickets->precio);
            })
            ->editColumn('product_id', function ($tickets) {
                $product = Product::find($tickets->product_id,['name']);
                return $product->name??'';
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
    public function deleteIncome(Request $request)
    {
        $tipo =$request->tipo ??'';
        $income = Income::find($request->income);
        $transaction = Transaction::find($income->transaction_id);
        if($transaction->tipo == 1){
            if(!empty($tipo)){
                $transaction->precio = $transaction->precio -($income->precio * $income->cantidad);
            }else
                $transaction->precio = $transaction->precio -$income->precio;
        }
        if($tipo == 2){
            $transaction->precio = $transaction->precio -$income->precio;
        }
        $product = Product::find($income->product_id);
        if($product->cantidad != '-1'){
            if($tipo==2){
                $product->cantidad = $product->cantidad + $income->cantidad;
            }else{
                if($transaction->tipo == 2)
                    $product->cantidad = $product->cantidad - $income->cantidad;
                if($transaction->tipo == 1){
                    if(!empty($tipo)){
                        if(Auth::user()->parking_id == 8)
                            $product->precio= round((($product->cantidad*($product->precio*1)) - ($income->cantidad*($income->precio*1))) /(( $product->cantidad-$income->cantidad !=0? (int) $product->cantidad-$income->cantidad:1)),2);
                        else
                            $product->precio= intval((($product->cantidad*($product->precio*1)) - ($income->cantidad*($income->precio*1))) /(( $product->cantidad-$income->cantidad !=0? (int) $product->cantidad-$income->cantidad:1)));
                        $product->cantidad = $product->cantidad-$income->cantidad;
                    }else
                        $product->cantidad = $product->cantidad + $income->cantidad;
                }
            }
            $product->save();
        }
        $income->delete();
        $transaction->save();
        $return['precio'] = format_money($transaction->precio);
        return $return;
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
}
