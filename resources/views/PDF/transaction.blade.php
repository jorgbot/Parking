@extends('layouts.minimal')

@section('styles')

    <style>{!! file_get_contents('css/pages/PDF/font-pdf.css') !!}</style>
    <style>{!! file_get_contents('css/pages/PDF/export.css') !!}</style>
@endsection

@section('content')
    <div class="body">

        <div class="main">
            <div class="mHead">
                <div class="header">
                    <div class="td"><img src="images/logo-pdf.png" width="151" height="50" alt=""></div>
                    <div class="td"><h3>{!! ucwords(tt('pages/pdf.partners')) !!}</h3></div>
                </div>

                <div class="hData">
                    <div class="labGroup">
                        <label>{{ tt('pages/pdf.partner') }}</label>
                        <p>{{ $partner }}</p>
                    </div>
                    <div class="labGroup">
                        <label>{{ tt('pages/pdf.date_created') }}</label>
                        <p>{{ $date_bill }}</p>
                    </div>
                    <div class="labGroup">
                        <label>{{ tt('pages/pdf.total') }}</label>
                        <p class="price">{{ format_money($transaction->value) }}</p>
                    </div>
                </div>
            </div>

            <div class="areaMiddle">
                <table width="100%" cellspacing="0" border="0">
                    <thead>
                    <tr>
                        <th colspan="2" width="120">{{ tt('pages/pdf.detail') }}</th>
                        <th class="text-center" width="100">{{ tt('pages/pdf.cant') }}</th>
                        <th class="text-right">{{ tt('pages/pdf.value') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php( $entries =$income->filter(function ($value, $action) {return $value->action ==='new';}))
                    <tr>
                        <td width="15"><div class="status new"></div></td>
                        <td width="190">{{ tt('pages/pdf.new') }}</td>
                        <td class="text-center">{{ $entries->count() }}</td>
                        <td class="text-right r_price">{{ format_money($entries->sum('value') *PORCENT_100 / ($porcent =$income[0]->percent )) }}</td>
                    </tr>
                    @php( $entries =$income->filter(function ($value, $action) {return $value->action ==='recovered';}))
                    <tr>
                        <td width="15"><div class="status recovery"></div></td>
                        <td width="190">{{ tt('pages/pdf.recovered') }}</td>
                        <td class="text-center">{{ $entries->count() }}</td>
                        <td class="text-right r_price">{{ format_money($entries->sum('value') *PORCENT_100 / $porcent) }}</td>
                    </tr>
                    @php( $entries =$income->filter(function ($value, $action) {return $value->action ==='renewed';}))
                    <tr>
                        <td width="15"><div class="status renew"></div></td>
                        <td width="190">{{ tt('pages/pdf.renewed') }}</td>
                        <td class="text-center">{{ $entries->count() }}</td>
                        <td class="text-right r_price">{{ format_money($entries->sum('value') *PORCENT_100 / $porcent) }}</td>
                    </tr>
                    @php( $entries =$income->filter(function ($value, $action) use($type_partner){return ($value->commissionable == 0  && $type_partner =='internal');}))
                    <tr>
                        <td width="15"><div class="status no_pay"></div></td>
                        <td width="190">{{ tt('pages/pdf.commissionable_not') }}</td>
                        <td class="text-center">{{ $entries->count() }}</td>
                        <td class="text-right r_price">{{ format_money($entries->sum('value') *PORCENT_100 / $porcent) }}</td>
                    </tr>
                    </tbody>
                </table>
                <!---->
                <div class="bdline"></div>
                <!---->
                <table width="100%" cellspacing="0" border="0">
                    <tfoot>
                    <tr>
                        <td width="" class="text-right">Total</td>
                        <td class="text-right">{{ format_money($transaction->value *PORCENT_100 / $porcent) }}</td>
                    </tr>
                    <tr>
                        <td width="" class="text-right"><strong>Total Comisión {{ $porcent }}%</strong></td>
                        <td class="text-right"><strong>{{ format_money($transaction->value) }}</strong></td>
                    </tr>
                    </tfoot>
                </table>
            </div>

            <div class="tableDetalle">
                <table width="99%" cellspacing="0" border="0">
                    <thead>
                    <tr>
                        <td colspan="7">
                            <h4>{{ tt('pages/pdf.commission_detail') }}</h4>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2">{{ tt('pages/pdf.date') }}</th>
                        <th>{{ tt('pages/pdf.bill') }}</th>
                        <th>{{ tt('pages/pdf.company') }}</th>
                        <th>{{ tt('pages/pdf.description') }}</th>
                        <th>{{ tt('pages/pdf.period') }}</th>
                        <th class="text-right pd_right">{{ tt('pages/pdf.value') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($income as $entry)
                    <tr>
                        <td width="3%">
                            {!!  $entry->action == 'new'?'<div class="status new"></div>' : ($entry->action == 'recovered'? '<div class="status recovery"></div>':'<div class="status renew"></div>') !!}
                            {!!  $entry->commissionable == 0 && $type_partner =='internal' ?'<div class="status no_pay"></div>' : '' !!}
                        </td>
                        <td width="15%">{{ $entry->rank }}</td>
                        <td width="10%">{{ $entry->id_factura }}</td>
                        <td width="12%">{{ $entry->id_empresa }}</td>
                        <td width="40%">
                            <div class="empresa">{{ $entry->nombre }}</div>
                            <?php
                                $descripcion = explode("(", $entry->descripcion);
                                $fecha = !empty($descripcion[1])? explode("-", $descripcion[1]):'';
                            ?>
                            <span class="fecha">(<strong>de</strong>: {{ !empty($fecha[0])? $fecha[0]:'' }} - <strong>hasta</strong>: {{ !empty($fecha[1])? $fecha[1]:')' }}</span>
                        </td>
                        <td width="12%">{{ ucwords($entry->periodo) }}</td>
                        <td width="12%" class="text-right pd_right">{{ format_money($entry->value) }}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                <!---->
                <div class="bdline"></div>
                <!---->
            </div>

        </div>

    </div>


@endsection
