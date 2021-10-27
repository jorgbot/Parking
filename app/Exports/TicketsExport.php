<?php

namespace App\Exports;

use App\Ticket;
use App\Convenio;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\Auth;
use App\Models\Partner;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketsExport implements FromCollection
{

    protected $range;

    public function __construct($range = null)
    {
        $this->range = $range;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $collection = collect([["Fecha", "Hora Entrada", "Hora Salida", "Placa", "Tipo Vehiculo", "Valor cancelado","Extra","Convenio", "Usuario", "Estado"]]);

		$tickets= Ticket::select(['created_at', 'hour', 'pay_day', 'plate', 'type', 'price', 'extra', 'convenio_id', 'partner_id', 'status'])->where('parking_id',Auth::user()->parking_id)->orderBy('ticket_id','desc');
		$dateRange = explode(" - ", $this->range);
        $tickets = $tickets->whereBetween('created_at', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])->get();

        $motos = 0;
        $carros = 0;
        $bicicletas = 0;
        $precio = 0;
        $extra = 0;
        foreach ($tickets as $ticket){
        	$precio += $ticket->price;
        	$extra += $ticket->extra;
            $ticket->price = format_money($ticket->price);
            $ticket->extra = format_money($ticket->extra);
            $partner = Partner::find($ticket->partner_id);
            $ticket->partner_id =  $partner ?$partner->name:'';
            $ticket->status = $ticket->status == 1? 'Pendiente Pago': 'Pagó';
            $hour =new DateTime("".$ticket->hour);
            $ticket->hour = $hour->format('h:ia');
            $hour =new DateTime("".$ticket->pay_day);
            $ticket->pay_day = $hour->format('h:ia');
            if($ticket->type == 1)
            	$carros++;
            if($ticket->type == 2)
            	$motos++;
            if($ticket->type == 3)
            	$bicicletas++;
            $ticket->type = $ticket->type == 1? 'Carro': ($ticket->type == 3 ? ( isBici()?'Bicicleta':'Camioneta' ) : 'Moto');
            if(!empty($ticket->convenio_id)){
                $convenio = Convenio::find($ticket->convenio_id);
                $ticket->convenio_id = $convenio ?$convenio->name:'';
            }
            $collection->push($ticket);
        }
        $auxCollection = collect([["TOTALES","RANGO DE FECHAS",$this->range]]);
        
        $auxCollection->push(collect([["CARROS","MOTOS",( isBici()?'BICICLETAS':'CAMIONETAS' ),"TOTAL","EXTRA"]]));
        $auxCollection->push(collect([[$carros,$motos,$bicicletas,format_money($precio),$extra]]));
        $auxCollection->push(collect([[""]]));
        $auxCollection->push( $collection);
        $collection = collect([[""]]);

		$tickets= Ticket::onlyTrashed()->select(['created_at', 'hour', 'pay_day', 'plate', 'type', 'price', 'extra', 'partner_id', 'status', 'deleted_at'])->where('parking_id',Auth::user()->parking_id)->orderBy('ticket_id','desc')->whereBetween('created_at', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])->get();
        foreach ($tickets as $ticket){
        	$precio += $ticket->price;
        	$extra += $ticket->extra;
            $ticket->price = format_money($ticket->price);
            $ticket->extra = format_money($ticket->extra);
            $partner = Partner::find($ticket->partner_id);
            $ticket->partner_id =  $partner ?$partner->name:'';
            $ticket->status = 'Eliminado';
            $hour =new DateTime("".$ticket->hour);
            $ticket->hour = $hour->format('h:ia');
            $hour =new DateTime("".$ticket->pay_day);
            $ticket->pay_day = $hour->format('h:ia');
            $ticket->type = $ticket->type == 1? 'Carro': ($ticket->type == 3 ? ( isBici()?'Bicicleta':'Camioneta' ) : 'Moto');
            $collection->push($ticket);
        }
		$auxCollection->push( $collection);

        return $auxCollection;
    }
}
