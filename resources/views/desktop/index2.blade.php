@extends('layouts.app')
@section('content')
    @include('app/nav_panel')
    <div class="container-fluid">
        <div class="panelPartner auto_margin">
            <!---->
            <div class="row">
                <div class="col-md-6" style="text-align: center;">
                    <button type="button" onclick="openModalCliente()" class="btn btn-primary col-md-10 btn-lg">Nuevo Cliente</button>
                </div>
                <div class="col-md-6" style="text-align: center;">
                    <button type="button" onclick="openModalPrestamo()" class="btn btn-default col-md-10 btn-lg">Nuevo Prestamo</button>
                </div>
            </div>
            <p class="height_10"></p>
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
                                {!! Form::label('tipoT', 'Tipo Tiempo', ['class' => 'control-label']) !!}
                                <select id="type" name="type" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="1">Mensual</option>
                                    <option value="2">Quincenal</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                {!! Form::label('tipoT', 'Cliente', ['class' => 'control-label']) !!}
                                <select class="form-control" id="customerList">
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-2">
                            <div class="form-group">
                                <label class="control-label">&nbsp;</label>
                                <button class="btn btn-success form-control" id="advanced_search"><i class="fa fa-search"></i> Buscar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                <div class="row" v-show="all">
                <div class="col-12" style="overflow:  auto;">
                    <table class="table responsive" id="tickets-table">
                        <thead>
                        <tr>
                            <th class="all">Fecha</th>
                            <th class="all">Cliente</th>
                            <th class="min-tablet">Monto</th>
                            <th class="min-tablet">Interes</th>
                            <th class="min-tablet">Tiempo</th>
                            <th class="min-tablet">Tipo</th>
                            <th class="min-tablet">Cuota</th>
                            <th class="min-tablet">Saldo</th>
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
                                <h4 class="title">Prestadó</h4>
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
                                <h4 class="title">Pagos</h4>
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
                                <h4 class="title">Pendientes</h4>
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
                <button id="pdfsubmit" type="submit" form="form_pdf">Submit</button>
            </form>
            @include('desktop.account')
        </div>
    </div>

    @include('customer.modal_add')
    @include('customer.modal_mod')
    @include('customer.modal_prestamo')
    @include('customer.modal_prestamo_mod')
    @include('customer.modal_abono')
    @include('customer.modal_list_abonos')
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
        function openModalCliente(){
            $('#modal_add').modal('show');
            $("#nombreCustomer").val("");
            $("#celularCustomer").val("");
            $("#observacionCustomer").val("");
            $("#cedulaCustomer").val("");
        }
        function openModalClienteMod(idCLiente){
            loadCustomer(idCLiente);
            $('#modal_mod').modal('show');
        }

        function openModalPrestamo(){
            loadCustomers();
            $('#modal_prestamo').modal('show');
        }
        function openModalPrestamoMod(idPrestamo){
            loadCustomers();
            $('#modal_prestamo_mod').modal('show');
            $('#idPrestamoMod').val(idPrestamo);
            loadPrestamo(idPrestamo);
        }
        function openModalAbono(prestamo,tipo,cuota){
            $('#modal_abono').modal('show');
            $('#tipoAbono').val(tipo);
            $('#abonoPrestamo').val(prestamo);
            $('#abonoValor').val(cuota);
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
                },
                error : function () {
                    location = '/login';
                }
            });
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
                    range:range
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
            var hour_cars_price=$('#hour_cars_price').validationEngine('validate');
            var day_cars_price=$('#day_cars_price').validationEngine('validate');
            var monthly_cars_price=$('#monthly_cars_price').validationEngine('validate');
            var hour_motorcycles_price=$('#hour_motorcycles_price').validationEngine('validate');
            var day_motorcycles_price=$('#day_motorcycles_price').validationEngine('validate');
            var monthly_motorcycles_price=$('#monthly_motorcycles_price').validationEngine('validate');
            var free_time=$('#free_time').validationEngine('validate');
            var cars_num=$('#cars_num').validationEngine('validate');
            var motorcycles_num=$('#motorcycles_num').validationEngine('validate');

            if (hour_cars_price || day_cars_price || monthly_cars_price || hour_motorcycles_price || day_motorcycles_price || monthly_motorcycles_price || free_time || cars_num || motorcycles_num )
                return;
            desktop_index_vm.changePrice();
        }

        function eliminarAbono(id) {
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

        function eliminarPrestamo(id) {
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
                desktop_index_vm.deletePrestamo(id);
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
        function crearCliente() {
            var vNombre=$("#nombreCustomer").validationEngine('validate');
            var vtelefono=$("#celularCustomer").validationEngine('validate');
            var vcedula=$("#cedulaCustomer").validationEngine('validate');

            if (vNombre || vtelefono || vcedula)
                return;
            var nombre = $("#nombreCustomer").val();
            var telefono = $("#celularCustomer").val();
            var cedula = $("#cedulaCustomer").val();
            var observacion = $("#observacionCustomer").val();

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "customers",
                data: {
                    name:nombre,
                    movil:telefono,
                    cedula:cedula,
                    observacion:observacion,
                },
                success: function (datos) {
                    $('#modal_add').modal('hide');
                    new PNotify({
                        title: 'Exito',
                        type: 'success',
                        text: 'Se agregó el cliente con exito'
                    });
                    loadCustomers();
                },
                error : function () {
                    location = '/login';
                }
            });
        }
        function modificarCliente() {
            var vNombre=$("#nombreCustomerMod").validationEngine('validate');
            var vtelefono=$("#celularCustomerMod").validationEngine('validate');
            var vcedula=$("#cedulaCustomerMod").validationEngine('validate');

            if (vNombre || vtelefono || vcedula)
                return;
            var id = $("#idCustomerMod").val();
            var nombre = $("#nombreCustomerMod").val();
            var telefono = $("#celularCustomerMod").val();
            var cedula = $("#cedulaCustomerMod").val();
            var observacion = $("#observacionCustomerMod").val();

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "customers_mod",
                data: {
                    id:id,
                    name:nombre,
                    movil:telefono,
                    cedula:cedula,
                    observacion:observacion,
                },
                success: function (datos) {
                    $('#modal_mod').modal('hide');
                    new PNotify({
                        title: 'Exito',
                        type: 'success',
                        text: 'Se modificó el cliente con exito'
                    });
                    loadCustomers();
                },
                error : function () {
                    location = '/login';
                }
            });
        }
        function listarAbonos(id_prestamo) {
            desktop_index_vm.loadAbonos(id_prestamo);
            $('#modal_list_abonos').modal('show');
        }
        function loadPrestamo(id) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "get_prestamo",
                data: {
                    prestamo_id:id
                },
                success: function (datos) {
                    $('#fechaPrestMod').val(datos['created_at'].substr(0,10));
                    $('#interestPrestMod').val(datos['interes']);
                    $('#timePrestMod').val(datos['tiempo']);
                    $('#typePrestMod').val(datos['tipo']);
                    $('#montoPrestMod').val(datos['monto']);
                    $('#CuotaPrestMod').val(datos['cuota']);
                    setTimeout(function () {
                        $('#customerPrestMod').val(datos['id_customer']);
                    }.bind(this),0);
                },
                error : function () {
                    location = '/login';
                }
            });
        }
        function loadCustomer(id) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "get_cliente",
                data: {
                    cliente_id:id
                },
                success: function (datos) {
                    $('#idCustomerMod').val(id);
                    $('#nombreCustomerMod').val(datos['nombre']);
                    $('#celularCustomerMod').val(datos['telefono']);
                    $('#cedulaCustomerMod').val(datos['cedula']);
                    $('#observacionCustomerMod').val(datos['observacion']);
                },
                error : function () {
                    //location = '/login';
                }
            });
        }
        function crearPrestamo() {
            var vNombre=$("#customerPrest").validationEngine('validate');
            var vInteres=$("#interestPrest").validationEngine('validate');
            var vTiempo=$("#timePrest").validationEngine('validate');
            var vMonto=$("#montoPrest").validationEngine('validate');
            var vCuota=$("#CuotaPrest").validationEngine('validate');
            var vFecha=$("#fechaPrest").validationEngine('validate');

            if (vNombre || vInteres || vTiempo || vMonto || vCuota|| vFecha)
                return;
            var customer=   $("#customerPrest").val();
            var Interes=    $("#interestPrest").val();
            var Tiempo=     $("#timePrest").val();
            var Monto=      $("#montoPrest").val();
            var Cuota=      $("#CuotaPrest").val();
            var tipo=       $("#typePrest").val();
            var fecha=      $("#fechaPrest").val();

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "prestamos",
                data: {
                    customer:   customer,
                    interes:    Interes,
                    tiempo:     Tiempo,
                    monto:      Monto,
                    cuota:      Cuota,
                    tipo:       tipo,
                    fecha:      fecha
                },
                success: function (datos) {
                    $('#modal_prestamo').modal('hide');
                    new PNotify({
                        title: 'Exito',
                        type: 'success',
                        text: 'Se agregó el prestamo con exito'
                    });
                    $('#tickets-table').dataTable()._fnAjaxUpdate();
                },
                error : function () {
                    ;
                }
            });
        }
        function calcularCuota(){
            var Interes=    $("#interestPrest").val();
            var Tiempo=     $("#timePrest").val();
            var Monto=      $("#montoPrest").val();
            var Cuota=      0;
            var tipo=       $("#typePrest").val();
            if(Interes =='' || Tiempo =='' || Monto=='' || tipo=='')
                return ;
            Cuota= (((Monto*Interes/100)*Tiempo)+(Monto*1))/(Tiempo*tipo);
            $("#CuotaPrest").val(Math.ceil(Cuota/1000)*1000);
        }
        function calcularCuota2(){
            var Interes=    $("#interestPrestMod").val();
            var Tiempo=     $("#timePrestMod").val();
            var Monto=      $("#montoPrestMod").val();
            var Cuota=      0;
            var tipo=       $("#typePrestMod").val();
            if(Interes =='' || Tiempo =='' || Monto=='' || tipo=='')
                return ;
            Cuota= (((Monto*Interes/100)*Tiempo)+(Monto*1))/(Tiempo*tipo);
            $("#CuotaPrestMod").val(Math.ceil(Cuota/1000)*1000);
        }
        function modificarPrestamo() {
            var vNombre=$("#customerPrestMod").validationEngine('validate');
            var vInteres=$("#interestPrestMod").validationEngine('validate');
            var vTiempo=$("#timePrestMod").validationEngine('validate');
            var vMonto=$("#montoPrestMod").validationEngine('validate');
            var vCuota=$("#CuotaPrestMod").validationEngine('validate');
            var vFecha=$("#fechaPrestMod").validationEngine('validate');

            if (vNombre || vInteres || vTiempo || vMonto || vCuota|| vFecha)
                return;
            var prestamo=   $("#idPrestamoMod").val();
            var customer=   $("#customerPrestMod").val();
            var Interes=    $("#interestPrestMod").val();
            var Tiempo=     $("#timePrestMod").val();
            var Monto=      $("#montoPrestMod").val();
            var Cuota=      $("#CuotaPrestMod").val();
            var tipo=       $("#typePrestMod").val();
            var fecha=      $("#fechaPrestMod").val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "actualizar_prestamo",
                data: {
                    prestamo:   prestamo,
                    customer:   customer,
                    interes:    Interes,
                    tiempo:     Tiempo,
                    monto:      Monto,
                    cuota:      Cuota,
                    tipo:       tipo,
                    fecha:      fecha
                },
                success: function (datos) {
                    new PNotify({
                        title: 'Exito',
                        type: 'success',
                        text: 'Se modificó el prestamo con exito'
                    });
                    $('#modal_prestamo_mod').modal('hide');
                    desktop_index_vm.load();
                    $('#tickets-table').dataTable()._fnAjaxUpdate();
                },
                error : function () {
                    //location = '/login';
                }
            });
        }
        function crearAbono() {
            var vPrestamo=$("#abonoPrestamo").validationEngine('validate');
            var vMonto=$("#abonoValor").validationEngine('validate');
            var vFecha=$("#abonoFecha").validationEngine('validate');

            if (vPrestamo || vMonto || vFecha)
                return;
            var prestamo=   $("#abonoPrestamo").val();
            var Monto=      $("#abonoValor").val();
            var tipo=       $("#tipoAbono").val();
            var fecha=       $("#abonoFecha").val();

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "abonos",
                data: {
                    prestamo:   prestamo,
                    valor:    Monto,
                    tipo:       tipo,
                    fecha:       fecha,
                },
                success: function (datos) {
                    $('#modal_abono').modal('hide');
                    new PNotify({
                        title: 'Exito',
                        type: 'success',
                        text: 'Se agregó el abono con exito'
                    });
                    $('#tickets-table').dataTable()._fnAjaxUpdate();
                },
                error : function () {
                    console.log('ha ocurrido un error');
                }
            });
        }
        function loadCustomers() {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "get_customers",

                success: function (datos) {
                    $('#customerPrest').html(datos);
                    $('#customerPrestMod').html(datos);
                    $('#customerList').html(datos);
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
                "startDate": "<?php $now = Carbon::now(); echo Carbon::yesterday()->format('Y-m-d')?>",
                "endDate": "<?php   echo $now->addDay()->format('Y-m-d')?>",
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
            var plate = $("#plate").val();
            if(plate ==""){
                return true;
            }
            if(plate.length == 6 && !isNaN(plate.charAt(plate.length-1))){
                $("#typeIn").val(1);
            }
            else{
                $("#typeIn").val(2);
            }
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
                loadCustomers();
            },
            methods    : {
                load : function() {
                    $("#Tiempo").val();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: "get_status_prestamo",
                        data: {
                            type_car        : $("#type-car").val(),
                            type            : $("#type").val(),
                            status          : $("#status").val(),
                            range           : $("#Tiempo").val(),
                            customer        : $("#customerList").val()
                        },
                        success: function (datos) {
                            var month= datos['month_expire_num'];
                            if(datos['extra']=='$0')
                                $("#total").html(datos['total']);
                            else
                                $("#total").html(datos['total']);
                            $("#motos").html(datos['motos']);
                            $("#carros").html(datos['carros']);
                            $("#month_expired").html(datos['month_expire_num']);
                            if( datos['month_expire_num']>0){
                                new PNotify({
                                    title: 'Prestamos pendientes de pago',
                                    text: datos['month_expire'],
                                    type: 'info'
                                });
                            }
                            this.retired = 1;
                        },
                        error : function () {
                            //alert('s');
                            //location = '/login';
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
                            url  : '{!! route('get_prestamos') !!}',
                            data : {
                                type_car        : $("#type-car").val(),
                                type            : $("#type").val(),
                                status          : $("#status").val(),
                                range           : $("#Tiempo").val(),
                                customer        : $("#customerList").val()
                            },
                            error : function () {
                                ;
                            }
                        },
                        columns: [
                            { data: 'Fecha', name: 'Fecha', orderable  : false, searchable : false },
                            { data: 'id_customer', name: 'Cliente', orderable  : false, searchable : false },
                            { data: 'monto', name: 'Monto', orderable  : false, searchable : false },
                            { data: 'interes', name: 'Interes', orderable  : false, searchable : false },
                            { data: 'tiempo', name: 'Tiempo', orderable  : false, searchable : false },
                            { data: 'tipo', name: 'Tipo', orderable  : false, searchable : false },
                            { data: 'cuota', name: 'Cuota', orderable  : false, searchable : false },
                            { data: 'saldo', name: 'Saldo', orderable  : false, searchable : false },
                            { data: 'action', name: 'acciones', orderable  : false, searchable : false },
                        ],
                        lengthMenu: [[ 10, 25, 50, -1], [ 10, 25, 50, "Todos"]]
                    });
                    }
                },
                loadAbonos: function(id_prestamo){
                    $('#abonos-table').DataTable({
                        sDom           : 'r<Hlf><"datatable-scroll"t><Fip>',
                        order          : [],
                        processing     : true,
                        serverSide     : true,
                        deferRender    : true,
                        destroy        : true,
                        ajax: {
                            url  : '{!! route('get_abonos') !!}',
                            data : {
                                prestamo        : id_prestamo
                            },
                            error : function () {
                                ;
                            }
                        },
                        columns: [
                            { data: 'created_at', name: 'Fecha', orderable  : false, searchable : false },
                            { data: 'tipo', name: 'Tipo', orderable  : false, searchable : false },
                            { data: 'valor', name: 'Valor', orderable  : false, searchable : false },
                            { data: 'action', name: 'Acciones', orderable  : false, searchable : false },
                        ],
                        lengthMenu: [[ 10, 25, 50, -1], [ 10, 25, 50, "Todos"]]
                    });
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
                    var day_cars_price=$('#day_cars_price').val();
                    var monthly_cars_price=$('#monthly_cars_price').val();
                    var hour_motorcycles_price=$('#hour_motorcycles_price').val();
                    var day_motorcycles_price=$('#day_motorcycles_price').val();
                    var monthly_motorcycles_price=$('#monthly_motorcycles_price').val();
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
                            day_cars_price : day_cars_price  ,
                            monthly_cars_price : monthly_cars_price  ,
                            hour_motorcycles_price : hour_motorcycles_price  ,
                            day_motorcycles_price  : day_motorcycles_price   ,
                            monthly_motorcycles_price  : monthly_motorcycles_price   ,
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
                delete : function(abono_id) {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: "POST",
                            url: "eliminar_abono",
                            data: {
                                abono_id:abono_id
                            },
                            success: function (datos) {
                                new PNotify({
                                    title: 'Exito',
                                    type: 'success',
                                    text: 'Se Eliminó el abono con exito'
                                });
                                $('#abonos-table').dataTable()._fnAjaxUpdate();
                            },
                            error : function () {
                                location = '/login';
                            }
                        });
                },
                deletePrestamo : function(prestamo) {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: "POST",
                            url: "eliminar_prestamo",
                            data: {
                                prestamo:prestamo
                            },
                            success: function (datos) {
                                new PNotify({
                                    title: 'Exito',
                                    type: 'success',
                                    text: 'Se Eliminó el prestamo con exito'
                                });
                                $('#tickets-table').dataTable()._fnAjaxUpdate();
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