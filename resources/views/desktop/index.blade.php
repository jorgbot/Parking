@extends('layouts.app')
@section('content')
    @include('app/nav_panel')
    <div class="container-fluid">
        <div class="panelPartner auto_margin">
            <!---->
            <div class="row" v-show="nav != 'convenios'">
                <div class="col-md-6" style="text-align: center;">
                    <button type="button" onclick="openModalIn()" class="btn btn-primary col-md-10 btn-lg">Ingresar</button>
                </div>
                <div class="col-md-6" style="text-align: center;">
                    <button type="button" onclick="openModalOut()" class="btn btn-default col-md-10 btn-lg">Cobrar</button>
                </div>
            </div>
            <p class="height_10"></p>
            <hr v-show="nav != 'convenios'">
            <!---->
            <p class="height_10" v-show="all"></p>

            <!---->
            <div class="box"  v-show="all">
                <div class="box-title">
                    <h3>
                        <i class="fa fa-search"></i>
                        <h2 class="title_a">Opciones de Busqueda</h2>
                    </h3>
                </div>
                <div class="box-content">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('fecha', 'Fechas', ['class' => 'control-label']) !!}
                                <input class="form-control" id="Tiempo" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                {!! Form::label('Estado', 'Estado', ['class' => 'control-label']) !!}
                                <select id="status" name="status" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="1" selected="selected">Pendiente</option>
                                    <option value="2">Pagó</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                {!! Form::label('tipo', 'Tipo Vehiculo', ['class' => 'control-label']) !!}
                                <select id="type-car" name="type" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="1">Carro</option>
                                    <option value="2">Moto</option>
                                    @php($typeParking = App\Parking::find(\Auth::user()->parking_id)->type)
                                    @if($typeParking == 2)
                                    <option value="3">{{ isBici()?'Bicicleta':'Camioneta' }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                {!! Form::label('tipoT', 'Tipo Tiempo', ['class' => 'control-label']) !!}
                                <select id="type" name="type" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="1">Horas</option>
                                    @if(isJornada())
                                    <option value="4">Jornada</option>
                                    @endif
                                    <option value="2">Dias</option>
                                    <option value="3">Mensualidad</option>
                                </select>
                            </div>
                        </div>
                        @if(\Auth::user()->type == 1)
                        <div class="col-md-2">
                            <div class="form-group">
                                @php($users = App\Models\Partner::where('parking_id',\Auth::user()->parking_id)->get())
                                {!! Form::label('Usuario', 'Usuario', ['class' => 'control-label']) !!}
                                <select id="partnersList" name="partnersList" class="form-control">
                                    <option value="">Todos</option>
                                    @foreach($users as $user)
                                        {!! '<option data-toggle="tooltip" value="'.$user->partner_id.'">'.$user->name.'</option>' !!}
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @else
                            <input class="form-control" id="partnersList" style="display: none;" value=""/>
                        @endif
                        <div class="col-md-2 col-sm-2">
                            <div class="form-group">
                                <label class="control-label">&nbsp;</label>
                                <button class="btn btn-success form-control" id="advanced_search"><i class="fa fa-search"></i> Buscar</button>
                            </div>
                        </div>
                        @if(isReport())
                        <div class="col-md-2 col-sm-2">
                            <div class="form-group">
                                <label class="control-label">&nbsp;</label>
                                <button type="button" onclick="reporte()" class="btn btn-primary form-control">Reporte</button>
                            </div>  
                        </div>
                        @endif
                    </div>
                </div>
            </div>

                <div class="row" v-show="all">
                <div class="col-12" style="overflow:  auto;">
                    <table class="table responsive" id="tickets-table">
                        <thead>
                        <tr>
                            <th class="all">Placa</th>
                            <th class="min-tablet">Tipo</th>
                            <th class="min-tablet">Estado</th>
                            <th class="min-tablet">Precio</th>
                            <th class="min-tablet">Hora Entrada</th>
                            <th class="min-tablet">Atendió</th>
                            <th class="all">acciones</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <h2 class="title_a"  v-show="all" >Estado actual</h2>
            <div class="row" v-show="all">
                <div class="col-lg-3 col-md-6">
                    <div class="widget_box_b">
                        <div class="contt">
                            <div class="fl_layer">
                                <h4 class="title">Recaudadó</h4>
                                <span class="line"></span>
                                <span class="data" id="total"> - </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="widget_box_b">
                        <div class="contt">
                            <div class="fl_layer">
                                <h4 class="title">Motos {{ $typeParking == 2?' / '.(isBici()?'Bicicletas':'Camionetas'):'' }}</h4>
                                <span class="line"></span>
                                <span class="data" id="motos"> - </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="widget_box_b">
                        <div class="contt">
                            <div class="fl_layer">
                                <h4 class="title">Carros</h4>
                                <span class="line"></span>
                                <span class="data total" id="carros"> - </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="widget_box_b bdred">
                        <div class="contt">
                            <div class="fl_layer">
                                <h4 class="title">Mensualidades por vencer</h4>
                                <span class="line"></span>
                                <span class="data red" id="month_expired"> - </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" v-show="month">
                <div class="col-12"  style="overflow:  auto;">
                    <table class="table responsive" id="month-table">
                        <thead>
                        <tr>
                            <th class="all">Placa</th>
                            <th class="min-tablet">Tipo</th>
                            <th class="min-tablet">Estado</th>
                            <th class="min-tablet">Precio</th>
                            <th class="min-tablet">Fecha vencimiento</th>
                            <th class="min-tablet">Nombre</th>
                            <th class="min-tablet">Atendió</th>
                            <th class="all">acciones</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <form id="form_pdf" class="row" method="POST" action="{{ route('pdf') }}" TARGET="_blank" hidden>
            {{ csrf_field() }}
                <input id="id_pdf" type="text" class="form-control" name="id_pdf">
                <input id="isIva" type="text" class="form-control" name="isIva">
                <button id="pdfsubmit" type="submit" form="form_pdf">Submit</button>
            </form>
            @include('desktop.account')
            @if(isconvenio())
                @include('desktop.convenios')
            @endif
        </div>
    </div>

    @include('ticket.modal_ticket_in')
    @include('ticket.modal_ticket_out')
    @include('ticket.modal_ticket_mod')
    @include('ticket.modal_ticket_pay')
    @include('convenio.modal_convenio_in')
    @include('convenio.modal_convenio_mod')
@endsection
@section('scripts')
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/datatable.min.js') }}"></script>
    <script src="{{ asset('js/moment.min.js') }}"></script>
    <script src="{{ asset('js/daterangepicker.js') }}"></script>
    <script src="{{ asset('js/pnotify.custom.min.js') }}"></script>
    <script src="{{ asset('js/validationEngine.min.js') }}"></script>
    <script src="{{ asset('js/validationEngine-es.min.js') }}"></script>
    <script>
        var typeParking = {{ $typeParking }};
        var parkingId = {{ \Auth::user()->parking_id }};
        function openModalIn(){
            $('#modal_ticket_in').modal('show');
            getFecha();
            $("#nameIn").css("display","none");
            $("#priceIn").css("display","none");
            $("#mailIn").css("display","none");
            $("#movilIn").css("display","none");
            $("#rangeIn").css("display","none");
            $("#schedule").val(1);
            $("#typeIn").val(1);
            $("#plate").val('');
            $("#id_convenio").val('');
        }
        function openModalInConvenio(){
            $('#modal_convenio_in').modal('show');
            $('#namec').val('');
            $('#min_cars_pricec').val('');
            $('#hour_cars_pricec').val('');
            $('#day_cars_pricec').val('');
            $('#min_motorcycles_pricec').val('');
            $('#hour_motorcycles_pricec').val('');
            $('#day_motorcycles_pricec').val('');
            $('#min_van_pricec').val('');
            $('#hour_van_pricec').val('');
            $('#day_van_pricec').val('');
        }
        function openModalModConvenio(ticket_id){
            $('#modal_convenio_mod').modal('show');
            loadConvenio(ticket_id);
            $('#convenio_id_mod').val(ticket_id);
        }
        function mensualidad(){
            var schedule = $("#schedule").val();
            if(schedule == 3){
                $("#nameIn").css("display","block");
                $("#rangeIn").css("display","block");
                $("#priceIn").css("display","block");
                $("#mailIn").css("display","block");
                $("#movilIn").css("display","block");
            }else{
                $("#nameIn").css("display","none");
                $("#priceIn").css("display","none");
                $("#mailIn").css("display","none");
                $("#movilIn").css("display","none");
                $("#rangeIn").css("display","none");
            }
        }
        function mensualidad2(){
            var schedule = $("#schedule_mod").val();
            if(schedule == 3){
                $("#nameIn_mod").css("display","block");
                $("#rangeIn_mod").css("display","block");
                $("#priceIn_mod").css("display","block");
                $("#mailIn_mod").css("display","block");
                $("#movilIn_mod").css("display","block");
            }else{
                $("#nameIn_mod").css("display","none");
                $("#priceIn_mod").css("display","none");
                $("#mailIn_mod").css("display","none");
                $("#movilIn_mod").css("display","none");
                $("#rangeIn_mod").css("display","none");
            }
        }
        function openModalOut(){
            $('#modal_ticket_out').modal('show');
            getFecha();
            $('#ticket_id').val('');
        }
        function openModalMod(ticket_id){
            $('#modal_ticket_mod').modal('show');
            loadTicket(ticket_id);
            $('#ticket_id_mod').val(ticket_id);
        }
        var getFecha = function(){
            var fecha = new Date();
            var fechaActual=fecha.getDate()+"/0"+(fecha.getMonth()+1)+"/"+fecha.getFullYear()
                +"  "+fecha.getHours()+":"+fecha.getMinutes();
            $('#fecha').val(fechaActual);
        };
        function pagar() {
            var ticket_id= $('#ticket_id').val();
            ticket_id = ticket_id.replace(/[^0-9]/g,'');
            $('#ticket_id').val(ticket_id*1);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "cobrar",
                data: {
                    ticket_id:ticket_id
                },
                success: function (datos) {
                    $('.alert').alert();
                    $('#pagar').html(datos[0]);
                    $('#tiempo').html(datos[1]);
                    $('#modal_ticket_out').modal('hide');
                    $('#modal_ticket_pay').modal('show');
                    $('#tickets-table').dataTable()._fnAjaxUpdate();
                    if(!$('#nav_inicio').hasClass('active'))
                        $('#month-table').dataTable()._fnAjaxUpdate();
                    $('#cobrar_id').attr("onclick","form_pdf('"+ticket_id+"'); $('#modal_ticket_pay').modal('hide')");
                    @if(isIva())
                        $('#cobrar_id_iva').attr("onclick","form_pdf_iva('"+ticket_id+"'); $('#modal_ticket_pay').modal('hide')");
                    @endif
                },
                error : function () {
                    location = '/login';
                }
            });
        }
        function form_pdf_iva(id) {
            $('#isIva').val('1');
            form_pdf(id);
            setTimeout(function (){
                $('#isIva').val('');
            },1500);
        }
        function form_pdf(id) {
            $('#id_pdf').val(id);
            $('#pdfsubmit').click();
        }
        function modificarTicket() {
            var ticket_id= $('#ticket_id_mod').val();
            var plate= $('#plate_mod').val();
            var type= $('#typeIn_mod').val();
            var schedule= $('#schedule_mod').val();
            var drawer= $('#drawer_mod').val();
            var extra= $('#extra').val();
            var name= $('#nombreIn_mod').val();
            var precioIn = $("#precioIn_mod").val();
            var emailIn = $("#emailIn_mod").val();
            var celularIn = $("#celularIn_mod").val();
            var range= $('#date_range_mod').val();
            var convenio= $('#id_convenio_mod').val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "actualizar",
                data: {
                    ticket_id:ticket_id,
                    plate:plate,
                    type:type,
                    schedule:schedule,
                    drawer:drawer,
                    name:name,
                    price:precioIn,
                    email:emailIn,
                    movil:celularIn,
                    extra:extra,
                    range:range,
                    convenio:convenio,
                },
                success: function (datos) {
                    new PNotify({
                        title: 'Exito',
                        type: 'success',
                        text: 'Se modificó el ticket con exito'
                    });
                    $('#modal_ticket_mod').modal('hide');
                    $('#tickets-table').dataTable()._fnAjaxUpdate();
                    if(!$('#nav_inicio').hasClass('active'))
                        $('#month-table').dataTable()._fnAjaxUpdate();
                    desktop_index_vm.load();
                },
                error : function () {
                    location = '/login';
                }
            });
        }
        function actualizarCuenta() {
            var vNombre=$('input[name=new_name]').validationEngine('validate');
            var vApellido=$('input[name=new_last_name]').validationEngine('validate');
            var vEmail=$('input[name=new_email]').validationEngine('validate');
            if ($('#password').val().length>0){
                var currentPassword = $('input[name=currentPassword]').validationEngine('validate');
                var password = $('input[name=password]').validationEngine('validate');
                var confirmPassword = $('input[name=confirm_password]').validationEngine('validate');
            }
            if (vNombre || vApellido || vEmail || ( $('#password').val().length>0 && (currentPassword || password || confirmPassword) ))
                return;
            desktop_index_vm.changeAccount();
        }

        function actualizarCuentaParking() {
            var min_cars_price=$('#min_cars_price').validationEngine('validate');
            var hour_cars_price=$('#hour_cars_price').validationEngine('validate');
            var day_cars_price=$('#day_cars_price').validationEngine('validate');
            var monthly_cars_price=$('#monthly_cars_price').validationEngine('validate');
            var min_motorcycles_price=$('#min_motorcycles_price').validationEngine('validate');
            var hour_motorcycles_price=$('#hour_motorcycles_price').validationEngine('validate');
            var day_motorcycles_price=$('#day_motorcycles_price').validationEngine('validate');
            var monthly_motorcycles_price=$('#monthly_motorcycles_price').validationEngine('validate');
            var min_van_price=$('#min_van_price').validationEngine('validate');
            var hour_van_price=$('#hour_van_price').validationEngine('validate');
            var day_van_price=$('#day_van_price').validationEngine('validate');
            var monthly_van_price=$('#monthly_van_price').validationEngine('validate');
            var free_time=$('#free_time').validationEngine('validate');
            var cars_num=$('#cars_num').validationEngine('validate');
            var motorcycles_num=$('#motorcycles_num').validationEngine('validate');

            if (hour_cars_price || day_cars_price || min_cars_price || monthly_cars_price || hour_motorcycles_price || min_motorcycles_price || day_motorcycles_price || monthly_motorcycles_price || hour_van_price || min_van_price || day_van_price || monthly_van_price || free_time || cars_num || motorcycles_num )
                return;
            desktop_index_vm.changePrice();
        }

        function eliminarTicket(id) {
            (new PNotify({
                title: 'Necesita confirmación',
                text: 'Esta seguro de querer eliminar el registro?',
                icon: 'glyphicon glyphicon-question-sign',
                hide: false,
                confirm: {
                    confirm: true
                },
                buttons: {
                    closer: false,
                    sticker: false
                },
                history: {
                    history: false
                },
                addclass: 'stack-modal',
                stack: {
                    'dir1': 'down',
                    'dir2': 'right',
                    'modal': true
                }
            })).get().on('pnotify.confirm', function() {
                desktop_index_vm.delete(id);
            }).on('pnotify.cancel', function() {
               ;
            });
        }
        function eliminarConvenio(id) {
            (new PNotify({
                title: 'Necesita confirmación',
                text: 'Esta seguro de querer eliminar el registro?',
                icon: 'glyphicon glyphicon-question-sign',
                hide: false,
                confirm: {
                    confirm: true
                },
                buttons: {
                    closer: false,
                    sticker: false
                },
                history: {
                    history: false
                },
                addclass: 'stack-modal',
                stack: {
                    'dir1': 'down',
                    'dir2': 'right',
                    'modal': true
                }
            })).get().on('pnotify.confirm', function() {
                desktop_index_vm.deleteConvenio(id);
            }).on('pnotify.cancel', function() {
                ;
            });
        }
        function recuperarTicket(id) {
            (new PNotify({
                title: 'Necesita confirmación',
                text: 'Esta seguro de querer recuperar el registro?',
                icon: 'glyphicon glyphicon-question-sign',
                hide: false,
                confirm: {
                    confirm: true
                },
                buttons: {
                    closer: false,
                    sticker: false
                },
                history: {
                    history: false
                },
                addclass: 'stack-modal',
                stack: {
                    'dir1': 'down',
                    'dir2': 'right',
                    'modal': true
                }
            })).get().on('pnotify.confirm', function() {
                desktop_index_vm.recovery(id);
            }).on('pnotify.cancel', function() {
               ;
            });
        }
        function renovarTicket(id) {
            (new PNotify({
                title: 'Necesita confirmación',
                text: 'Esta seguro de querer renovar mensualidad?',
                icon: 'glyphicon glyphicon-question-sign',
                hide: false,
                confirm: {
                    confirm: true
                },
                buttons: {
                    closer: false,
                    sticker: false
                },
                history: {
                    history: false
                },
                addclass: 'stack-modal',
                stack: {
                    'dir1': 'down',
                    'dir2': 'right',
                    'modal': true
                }
            })).get().on('pnotify.confirm', function() {
                desktop_index_vm.renovar(id);
            }).on('pnotify.cancel', function() {
               ;
            });
        }
        function crearTicket() {
            type();
            var plate = $("#plate").val();
            var typeIn = $("#typeIn").val();
            var schedule = $("#schedule").val();
            var drawer = $("#drawer").val();
            var nameIn = $("#nombreIn").val();
            var precioIn = $("#precioIn").val();
            var emailIn = $("#emailIn").val();
            var celularIn = $("#celularIn").val();
            var date = $("#date-range").val();
            var convenio = $('#id_convenio').val();

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "tickets",
                data: {
                    plate:plate,
                    type:typeIn,
                    schedule:schedule,
                    drawer:drawer,
                    name:nameIn,
                    price:precioIn,
                    email:emailIn,
                    movil:celularIn,
                    range:date,
                    convenio:convenio,
                },
                success: function (datos) {
                    console.log(datos);
                    if(datos==0){
                        new PNotify({
                            title: 'Exito',
                            type: 'info',
                            text: 'Ya existe un ticket con esta placa'
                        });
                        return;
                    }
                    $('#modal_ticket_in').modal('hide');
                    new PNotify({
                        title: 'Exito',
                        type: 'success',
                        text: 'Se agregó el ticket con exito'
                    });
                    desktop_index_vm.loadTable();
                    form_pdf(datos);
                },
                error : function () {
                    location = '/login';
                }
            });
        }
        function loadTicket(id) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "get_ticket",
                data: {
                    ticket_id:id
                },
                success: function (datos) {
                    $('#plate_mod').val(datos['plate']);
                    $('#fecha_mod').val(datos['hour']);
                    $('#typeIn_mod').val(datos['type']);
                    $('#schedule_mod').val(datos['schedule']);
                    $('#drawer_mod').val(datos['drawer']);
                    $('#nombreIn_mod').val(datos['name']);
                    $('#precioIn_mod').val(datos['price']);
                    $('#emailIn_mod').val(datos['email']);
                    $('#celularIn_mod').val(datos['phone']);
                    $('#extra').val(datos['extra']);
                    $('#id_convenio_mod').val(datos['convenio_id']);
                    $('#date_range_mod').val(datos['hour']+' - '+datos['date_end']);
                    mensualidad2();
                    $('#date_range_mod').daterangepicker({
                        "locale": {
                            "format": "YYYY-MM-DD"
                        },
                        "opens": "center",
                        "drops": "up"
                    });
                },
                error : function () {
                    location = '/login';
                }
            });
        }
        function loadConvenio(id) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "get_convenio",
                data: {
                    convenio:id
                },
                success: function (datos) {
                    $('#namecm').val(datos['name']);
                    $('#min_cars_pricecm').val(datos['min_cars_price']);
                    $('#hour_cars_pricecm').val(datos['hour_cars_price']);
                    $('#day_cars_pricecm').val(datos['day_cars_price']);
                    $('#min_motorcycles_pricecm').val(datos['min_motorcycles_price']);
                    $('#hour_motorcycles_pricecm').val(datos['hour_motorcycles_price']);
                    $('#day_motorcycles_pricecm').val(datos['day_motorcycles_price']);
                    $('#min_van_pricecm').val(datos['min_van_price']);
                    $('#hour_van_pricecm').val(datos['hour_van_price']);
                    $('#day_van_pricecm').val(datos['day_van_price']);
                },
                error : function () {
                    location = '/login';
                }
            });
        }
        function mayus(e) {
            if (screen.width>=500 )
                e.value = e.value.toUpperCase();
        }
        $(function() {
            $("#ticket_id").keypress(function(e) {
                if(e.which == 13) {
                    // Acciones a realizar, por ej: enviar formulario.
                    $('#b_pagar').click();
                }
            });
            $("#plate").keypress(function(e) {
                if(e.which == 13) {
                    // Acciones a realizar, por ej: enviar formulario.
                    $('#new_ticket').click();
                }
            });
            $("#plate").blur(function(){
                type();
            });
            $('input[name="daterange"]').daterangepicker({
                timePicker: true,
                timePickerIncrement: 30,
                locale: {
                    format: 'YYYY/MM/DD h:mm A'
                }
            });
            $('#date-range').daterangepicker({
                "startDate": "<?php  use Carbon\Carbon;$now = Carbon::now(); echo $now->format('m/d/Y')?>",
                "endDate": "<?php   echo $now->addMonth()->format('m/d/Y')?>",
                "opens": "center",
                "drops": "up"
            }, function(start, end, label) {
                console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
            });
            $('#Tiempo').daterangepicker({
                "locale": {
                    "format": "YYYY-MM-DD"
                },
                "startDate": "<?php $now = Carbon::now(); echo Carbon::now()->format('Y-m-d')?>",
                "endDate": "<?php   echo $now->format('Y-m-d')?>",
                "opens": "center",
                "drops": "up"
            }, function(start, end, label) {
                console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
            });
            $('#date_range_mod').daterangepicker({
                "locale": {
                    "format": "YYYY-MM-DD"
                },
                "opens": "center",
                "drops": "up"
            }, function(start, end, label) {
                console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
            });
            var fecha = new Date();
            var hoy=fecha.getFullYear()+"/"+(fecha.getMonth()+1)+"/"+fecha.getDate();
            $.extend(true, $.fn .dataTable.defaults, {
                "stateSave": true,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.9/i18n/Spanish.json"
                }
            });
            $('#advanced_search').click(function() {
                desktop_index_vm.loadTable();
            });
        });
        function getOpt() {
            var opt = {
                processing     : true,
                serverSide     : true,
                destroy        : true,
                ajax           : '',
                columns        : [],
                sDom           : 'r<Hlf><"datatable-scroll"t><Fip>',
                pagingType     : "simple_numbers",
                iDisplayLength : 5,

            };
            return opt;
        }
        function validar(e) {
            tecla = (document.all) ? e.keyCode : e.which;
            if (tecla==13){
                type();
            }
        }
        function type() {
            @if(\Auth::user()->parking_id != 9)
                var plate = $("#plate").val();
                if(plate ==""){
                    return true;
                }
                if(plate.length == 6 && !isNaN(plate.charAt(plate.length-1))){
                    if($("#typeIn").val() == 2 && parkingId!=11)
                        $("#typeIn").val(1);
                }
                else{
                    if(plate.length > 6 && parkingId==11)
                        $("#typeIn").val(3);
                    else
                        $("#typeIn").val(2);
                }
            @endif
        }
        function createDataTableStandar(selector, opt) {
            if (typeof opt.scroll === 'undefined')
                opt.scroll = true;
            var myTable = $(selector).DataTable(opt);
            $(".dataTables_filter input[aria-controls='" + selector.substring(1) + "']").unbind().bind("keyup", function(e) {
                //if(this.value.length >= 3 || e.keyCode == 13) {
                if (e.keyCode == 13) {
                    myTable.search(this.value).draw();
                    return;
                }
                if (this.value == "")
                    myTable.search("").draw();
                return;
            });
            if (opt.scroll) {
                myTable.on('page.dt', function() {
                    $('html, body').animate({
                        scrollTop: $(".dataTables_wrapper").offset().top
                    }, 'fast');
                });
            }
            return myTable;
        }
        function reporte(){
            window.open("/export_tickets/"+ $("#Tiempo").val(), "_blank");
        }
        function verificar(e) {
 
            // comprovamos con una expresion regular que el caracter pulsado sea
            // una letra, numero o un espacio
            if(e.key.match(/[a-z0-9]/i)===null) {
     
                // Si la tecla pulsada no es la correcta, eliminado la pulsación
                e.preventDefault();
            }
        }
        function crearConvenio() {
            var name=$('#namec').validationEngine('validate');
            var min_cars_price=$('#min_cars_pricec').validationEngine('validate');
            var hour_cars_price=$('#hour_cars_pricec').validationEngine('validate');
            var day_cars_price=$('#day_cars_pricec').validationEngine('validate');
            var min_motorcycles_price=$('#min_motorcycles_pricec').validationEngine('validate');
            var hour_motorcycles_price=$('#hour_motorcycles_pricec').validationEngine('validate');
            var day_motorcycles_price=$('#day_motorcycles_pricec').validationEngine('validate');
            var min_van_price=$('#min_van_pricec').validationEngine('validate');
            var hour_van_price=$('#hour_van_pricec').validationEngine('validate');
            var day_van_price=$('#day_van_pricec').validationEngine('validate');

            if (name || hour_cars_price || day_cars_price || min_cars_price  || hour_motorcycles_price || min_motorcycles_price || day_motorcycles_price || hour_van_price || min_van_price || day_van_price )
                return;

            name=$('#namec').val();
            min_cars_price=$('#min_cars_pricec').val();
            hour_cars_price=$('#hour_cars_pricec').val();
            day_cars_price=$('#day_cars_pricec').val();
            min_motorcycles_price=$('#min_motorcycles_pricec').val();
            hour_motorcycles_price=$('#hour_motorcycles_pricec').val();
            day_motorcycles_price=$('#day_motorcycles_pricec').val();
            min_van_price=$('#min_van_pricec').val();
            hour_van_price=$('#hour_van_pricec').val();
            day_van_price=$('#day_van_pricec').val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "convenios",
                data: {
                    name:name,
                    min_cars_price:min_cars_price,
                    hour_cars_price:hour_cars_price,
                    day_cars_price:day_cars_price,
                    min_motorcycles_price:min_motorcycles_price,
                    hour_motorcycles_price:hour_motorcycles_price,
                    day_motorcycles_price:day_motorcycles_price,
                    min_van_price:min_van_price,
                    hour_van_price:hour_van_price,
                    day_van_price:day_van_price,
                },
                success: function (datos) {
                    console.log(datos);
                    $('#modal_convenio_in').modal('hide');
                    new PNotify({
                        title: 'Exito',
                        type: 'success',
                        text: 'Se agregó el convenio con éxito'
                    });
                    $('#convenios-table').dataTable()._fnAjaxUpdate();
                },
                error : function () {
                    //location = '/login';
                }
            });
        }
        function modificarConvenio() {
            var convenio= $('#convenio_id_mod').val();
            var name=$('#namecm').validationEngine('validate');
            var min_cars_price=$('#min_cars_pricecm').validationEngine('validate');
            var hour_cars_price=$('#hour_cars_pricecm').validationEngine('validate');
            var day_cars_price=$('#day_cars_pricecm').validationEngine('validate');
            var min_motorcycles_price=$('#min_motorcycles_pricecm').validationEngine('validate');
            var hour_motorcycles_price=$('#hour_motorcycles_pricecm').validationEngine('validate');
            var day_motorcycles_price=$('#day_motorcycles_pricecm').validationEngine('validate');
            var min_van_price=$('#min_van_pricecm').validationEngine('validate');
            var hour_van_price=$('#hour_van_pricecm').validationEngine('validate');
            var day_van_price=$('#day_van_pricecm').validationEngine('validate');

            if (name || hour_cars_price || day_cars_price || min_cars_price  || hour_motorcycles_price || min_motorcycles_price || day_motorcycles_price || hour_van_price || min_van_price || day_van_price )
                return;
            name=$('#namecm').val();
            min_cars_price=$('#min_cars_pricecm').val();
            hour_cars_price=$('#hour_cars_pricecm').val();
            day_cars_price=$('#day_cars_pricecm').val();
            min_motorcycles_price=$('#min_motorcycles_pricecm').val();
            hour_motorcycles_price=$('#hour_motorcycles_pricecm').val();
            day_motorcycles_price=$('#day_motorcycles_pricecm').val();
            min_van_price=$('#min_van_pricecm').val();
            hour_van_price=$('#hour_van_pricecm').val();
            day_van_price=$('#day_van_pricecm').val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "actualizar_convenio",
                data: {
                    convenio:convenio,
                    name:name,
                    min_cars_price:min_cars_price,
                    hour_cars_price:hour_cars_price,
                    day_cars_price:day_cars_price,
                    min_motorcycles_price:min_motorcycles_price,
                    hour_motorcycles_price:hour_motorcycles_price,
                    day_motorcycles_price:day_motorcycles_price,
                    min_van_price:min_van_price,
                    hour_van_price:hour_van_price,
                    day_van_price:day_van_price,
                },
                success: function (datos) {
                    new PNotify({
                        title: 'Exito',
                        type: 'success',
                        text: 'Se modificó el convenio con exito'
                    });
                    $('#modal_convenio_mod').modal('hide');
                    $('#convenios-table').dataTable()._fnAjaxUpdate();
                },
                error : function () {
                    //location = '/login';
                }
            });
        }
        var desktop_index_vm = new Vue({
            el         : '#main',
            data       : {
                ajax        : true,
                all         : true,
                account     : false,
                month       : false,
                nav         : 'all',
                total       : 0,
                retired     : 0,
                assets      : 0,
                value       : 0,
            },
            computed   : {

            },
            mounted    : function() {
                this.loadTable();
                setInterval(function(){$('#tickets-table').dataTable()._fnAjaxUpdate();}, 60000);
            },
            methods    : {
                load : function() {
                    $("#Tiempo").val();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: "get_status",
                        data: {
                            type_car        : $("#type-car").val(),
                            type            : $("#type").val(),
                            status          : $("#status").val(),
                            range           : $("#Tiempo").val(),
                            partner         : $("#partnersList").val(),

                        },
                        success: function (datos) {
                            var month= datos['month_expire_num'];
                            if(datos['extra']=='$0')
                                $("#total").html(datos['total']);
                            else
                                $("#total").html(datos['total']+'</br>('+datos['extra']+')');
                            $("#motos").html(datos['motos']);
                            $("#carros").html(datos['carros']);
                            $("#month_expired").html(datos['month_expire_num']);
                            if( datos['month_expire_num']>0){
                                new PNotify({
                                    title: 'Mensualidades pendientes',
                                    text: datos['month_expire'],
                                    type: 'info'
                                });
                            }
                            this.retired = 1;
                        },
                        error : function () {
                            location = '/login';
                        }
                    });
                },
                loadTable : function(status,idTransaction) {
                    $.extend(true, $.fn .dataTable.defaults, {
                        "stateSave": true,
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.10.9/i18n/Spanish.json"
                        }
                    });
                    if(status == 'history'){
                        $('#table-transaction').DataTable({
                            sDom           : 'r<Hlf><"datatable-scroll"t><Fip>',
                            order          : [],
                            processing     : true,
                            serverSide     : true,
                            deferRender    : true,
                            destroy        : true,
                            ajax: {
                                url  : laroute.route('transaction.get_list'),
                                error : function () {
                                    location = '/login';
                                }
                            },
                            columns: [
                                { data: 'rank', orderable  : false, searchable : false },
                                { data: 'income', orderable  : false, searchable : false },
                                { data: 'value', orderable  : false, searchable : false },
                                { data: 'accion', orderable  : false, searchable : false },
                            ],
                            lengthMenu: [[ 10, 25, 50], [ 10, 25, 50]]
                        });
                    }else if(status == 'month'){
                        $('#month-table').DataTable({
                            sDom           : 'r<Hlf><"datatable-scroll"t><Fip>',
                            order          : [],
                            processing     : true,
                            serverSide     : true,
                            deferRender    : true,
                            destroy        : true,
                                ajax: '{!! route('get_months') !!}',
                            columns: [
                                { data: 'plate', name: 'Placa', orderable  : false, searchable : false },
                                { data: 'Tipo', name: 'Tipo', orderable  : false, searchable : false },
                                { data: 'Estado', name: 'Estado', orderable  : false, searchable : false },
                                { data: 'price', name: 'Precio', orderable  : false, searchable : false },
                                { data: 'date_end', name: 'Fecha Vencimiento', orderable  : false, searchable : false },
                                { data: 'name', name: 'Nombre', orderable  : false, searchable : false },
                                { data: 'Atendio', name: 'Atendió', orderable  : false, searchable : false },
                                { data: 'action', name: 'acciones', orderable  : false, searchable : false },
                            ],
                            lengthMenu: [[ 10, 25, 50, -1], [ 10, 25, 50, "Todos"]]
                        });
                    }else if(status == 'convenios'){
                        $('#convenios-table').DataTable({
                            sDom           : 'r<Hlf><"datatable-scroll"t><Fip>',
                            order          : [],
                            processing     : true,
                            serverSide     : true,
                            deferRender    : true,
                            destroy        : true,
                                ajax: '{!! route('get_convenios') !!}',
                            columns: [
                                { data: 'name', orderable  : false, searchable : false },
                                { data: 'carro', orderable  : false, searchable : false },
                                { data: 'moto', orderable  : false, searchable : false },
                                { data: 'camioneta', orderable  : false, searchable : false },
                                { data: 'action', orderable  : false, searchable : false },
                            ],
                            lengthMenu: [[ 10, 25, 50, -1], [ 10, 25, 50, "Todos"]]
                        });
                    }else{
                        this.load();
                    $('#tickets-table').DataTable({
                        sDom           : 'r<Hlf><"datatable-scroll"t><Fip>',
                        order          : [],
                        processing     : true,
                        serverSide     : true,
                        deferRender    : true,
                        destroy        : true,
                        ajax: {
                            url  : '{!! route('get_tickets') !!}',
                            data : {
                                type_car        : $("#type-car").val(),
                                type            : $("#type").val(),
                                status          : $("#status").val(),
                                range           : $("#Tiempo").val(),
                                partner         : $("#partnersList").val(),
                            },
                            error : function () {
                                location = '/login';
                            }
                        },
                        columns: [
                            { data: 'plate', name: 'Placa', orderable  : false, searchable : false },
                            { data: 'Tipo', name: 'Tipo', orderable  : false, searchable : false },
                            { data: 'Estado', name: 'Estado', orderable  : false, searchable : false },
                            { data: 'price', name: 'Precio', orderable  : false, searchable : false },
                            { data: 'entrada', name: 'Locker', orderable  : false, searchable : false },
                            { data: 'Atendio', name: 'Atendió', orderable  : false, searchable : false },
                            { data: 'action', name: 'acciones', orderable  : false, searchable : false },
                        ],
                        lengthMenu: [[ 10, 25, 50, -1], [ 10, 25, 50, "Todos"]]
                    });
                    }
                },
                changeAccount : function() {
                    var nombre=$('input[name=new_name]').val();
                    var apellido=$('input[name=new_last_name]').val();
                    var email=$('input[name=new_email]').val();
                    var password = $('#password').val();
                    var currentPassword = $('#currentPassword').val();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: "update_cuenta",
                        data: {
                            name : nombre,
                            last_name : apellido,
                            email : email,
                            password: password,
                            currentPassword: currentPassword
                        },
                        success: function (response) {
                            if (response == 1){
                                new PNotify({
                                    title: 'Listo!',
                                    text: 'Se han modificado los datos correctamente.',
                                    type: 'success',
                                    buttons: {
                                        sticker: false
                                    }
                                });
                            }else{
                                new PNotify({
                                    title: 'Contraseña',
                                    text: 'No coincide la contraseña actual.',
                                    type: 'info',
                                    buttons: {
                                        sticker: false
                                    }
                                });
                            }
                        },
                        error : function () {
                            location = '/login';
                        }
                    });
                },
                changePrice : function() {
                    var hour_cars_price=$('#hour_cars_price').val();
                    var min_cars_price=$('#min_cars_price').val();
                    var day_cars_price=$('#day_cars_price').val();
                    var monthly_cars_price=$('#monthly_cars_price').val();
                    var hour_motorcycles_price=$('#hour_motorcycles_price').val();
                    var min_motorcycles_price=$('#min_motorcycles_price').val();
                    var day_motorcycles_price=$('#day_motorcycles_price').val();
                    var monthly_motorcycles_price=$('#monthly_motorcycles_price').val();
                    var hour_van_price=$('#hour_van_price').val();
                    var min_van_price=$('#min_van_price').val();
                    var day_van_price=$('#day_van_price').val();
                    var monthly_van_price=$('#monthly_van_price').val();
                    var free_time=$('#free_time').val();
                    var cars_num=$('#cars_num').val();
                    var motorcycles_num=$('#motorcycles_num').val();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: "update_parking",
                        data: {
                            hour_cars_price: hour_cars_price,
                            min_cars_price: min_cars_price,
                            day_cars_price : day_cars_price  ,
                            monthly_cars_price : monthly_cars_price  ,
                            hour_motorcycles_price : hour_motorcycles_price  ,
                            min_motorcycles_price : min_motorcycles_price  ,
                            day_motorcycles_price  : day_motorcycles_price   ,
                            monthly_motorcycles_price  : monthly_motorcycles_price   ,
                            hour_van_price : hour_van_price  ,
                            min_van_price : min_van_price  ,
                            day_van_price  : day_van_price   ,
                            monthly_van_price  : monthly_van_price   ,
                            free_time: free_time,
                            cars_num: cars_num,
                            motorcycles_num: motorcycles_num
                        },
                        success: function (response) {
                            new PNotify({
                                title: 'Listo!',
                                text: 'Se han modificado los datos correctamente.',
                                type: 'success',
                                buttons: {
                                    sticker: false
                                }
                            });
                        },
                        error : function () {
                            location = '/login';
                        }
                    });
                },
                delete : function(ticket_id) {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: "POST",
                            url: "eliminar",
                            data: {
                                ticket_id:ticket_id
                            },
                            success: function (datos) {
                                new PNotify({
                                    title: 'Exito',
                                    type: 'success',
                                    text: 'Se Eliminó el ticket con exito'
                                });
                                $('#tickets-table').dataTable()._fnAjaxUpdate();
                                if($('#nav_inicio').hasClass('active'))
                                    $('#tickets-table').dataTable()._fnAjaxUpdate();
                                else
                                    $('#month-table').dataTable()._fnAjaxUpdate();

                            },
                            error : function () {
                                location = '/login';
                            }
                        });
                },
                deleteConvenio : function(ticket_id) {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: "POST",
                            url: "eliminar_convenio",
                            data: {
                                convenio:ticket_id
                            },
                            success: function (datos) {
                                new PNotify({
                                    title: 'Exito',
                                    type: 'success',
                                    text: 'Se Eliminó el convenio con exito'
                                });
                                $('#convenios-table').dataTable()._fnAjaxUpdate();

                            },
                            error : function () {
                                location = '/login';
                            }
                        });
                },
                recovery : function(ticket_id) {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: "POST",
                            url: "recuperar",
                            data: {
                                ticket_id:ticket_id
                            },
                            success: function (datos) {
                                new PNotify({
                                    title: 'Exito',
                                    type: 'success',
                                    text: 'Se recuperó el ticket con exito'
                                });
                                if($('#nav_inicio').hasClass('active'))
                                    $('#tickets-table').dataTable()._fnAjaxUpdate();
                                else
                                    $('#month-table').dataTable()._fnAjaxUpdate();

                            },
                            error : function () {
                                location = '/login';
                            }
                        });
                },
                renovar : function(ticket_id) {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: "POST",
                            url: "renovar",
                            data: {
                                ticket_id:ticket_id
                            },
                            success: function (datos) {
                                new PNotify({
                                    title: 'Exito',
                                    type: 'success',
                                    text: 'Se recuperó el ticket con exito'
                                });
                                if($('#nav_inicio').hasClass('active'))
                                    $('#tickets-table').dataTable()._fnAjaxUpdate();
                                else
                                    $('#month-table').dataTable()._fnAjaxUpdate();

                            },
                            error : function () {
                                location = '/login';
                            }
                        });
                }
            }

        });
    </script>
@endsection