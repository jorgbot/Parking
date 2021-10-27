@extends('layouts.app')
@section('content')
    @include('app/nav_panel_acueducto')
    <div class="container-fluid">
        <div class="panelPartner auto_margin">
            <!---->
            <div class="row">
                <div class="col-md-6" style="text-align: center;">
                    <button type="button" onclick="openModalVenta('','','','','')" class="btn btn-primary col-md-10 btn-lg">Nueva Entrada</button>
                </div>
                <div class="col-md-6" style="text-align: center;">
                    <button type="button" onclick="openModalGasto('','','','','')" class="btn btn-outline-info col-md-10 btn-lg">Nueva Salida</button>
                </div>
            </div>
            <p class="height_10"></p>
            <!---->
            <p class="height_10" v-show="all"></p>


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
                        <!--<div class="col-md-2">
                            <div class="form-group">
                                {!! Form::label('Estado', 'Estado', ['class' => 'control-label']) !!}
                                <select id="status" name="status" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="1" selected="selected">Pendiente</option>
                                    <option value="2">Pagó</option>
                                </select>
                            </div>
                        </div>-->
                        <div class="col-md-2">
                            <div class="form-group">
                                {!! Form::label('tipoT', 'Tipo', ['class' => 'control-label']) !!}
                                <select id="typeS" name="type" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="2">Reparaciones</option>
                                    <option value="3">Instalaciones</option>
                                    <option value="4">Extensiones</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-2">
                            <div class="form-group">
                                <label class="control-label">&nbsp;</label>
                                <button class="btn btn-success form-control" id="advanced_search"><i class="fa fa-search"></i> Buscar</button>
                            </div>
                        </div>
                        @if( \Auth::user()->type != 6)
                        <div class="col-md-2 col-sm-2">
                            <div class="form-group">
                                <label class="control-label">&nbsp;</label>
                                <button class="btn btn-outline-info form-control" onclick="modalReport()"><i class="fa fa-search"></i> Reporte</button>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <br>
            <hr>
            <br>

            <div class="row" v-show="all">
                <div class="col-12" style="overflow:  auto;">
                    <table class="table responsive" id="transaction-table">
                        <thead>
                        <tr>
                            <th class="all">Descripción</th>
                            <th class="min-tablet">Precio</th>
                            <th class="min-tablet">Atendió</th>
                            <th class="all">acciones</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <br>
            <hr>
            <br>
            <h2 class="title_a"  v-show="all" >Estado actual</h2>
            <div class="row" v-show="all">
                <div class="col-lg-3 col-md-6">
                    <div class="widget_box_b bdred">
                        <div class="contt">
                            <div class="fl_layer">
                                <h4 class="title">Entradas</h4>
                                <span class="line"></span>
                                <span class="data red" id="entradas"> - </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="widget_box_b">
                        <div class="contt">
                            <div class="fl_layer">
                                <h4 class="title">Reparaciones</h4>
                                <span class="line"></span>
                                <span class="data" id="reparaciones"> - </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="widget_box_b">
                        <div class="contt">
                            <div class="fl_layer">
                                <h4 class="title">Instalaciones</h4>
                                <span class="line"></span>
                                <span class="data" id="instalaciones"> - </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="widget_box_b">
                        <div class="contt">
                            <div class="fl_layer">
                                <h4 class="title">Extensiones</h4>
                                <span class="line"></span>
                                <span class="data total" id="extensiones"> - </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <hr>
            <br>
            <div class="row">
                <div class="col-md-6" style="text-align: center;">
                    <button type="button" onclick="openModalProduct()" class="btn btn-outline-info col-md-10 btn-lg">Nuevo Producto</button>
                </div>
                @if( \Auth::user()->type != 6)
                <div class="col-md-6" style="text-align: center;">
                    <button type="button" onclick="reporteProductos()" class="btn btn-primary col-md-10 btn-lg">Reporte Productos</button>
                </div>
                @endif
            </div>
            <br>
            <hr>
            <br>
            <p class="height_10"></p>
            <div class="row" v-show="nav=='products' || all">
                <div class="col-12" style="overflow:  auto;">
                    <table class="table responsive" id="tickets-table">
                        <thead>
                        <tr>
                            <th class="all">Nombre</th>
                            <th class="min-tablet">Precio</th>
                            <th class="min-tablet">Cantidad</th>
                            <th class="min-tablet">Valor</th>
                            <th class="min-tablet">Minimo</th>
                            <th class="all">acciones</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <br>
            <h2 class="title_a"  v-show="all" >Estado Inventario</h2>
            <div class="row" v-show="all">
                <div class="col-lg-6 col-md-6">
                    <div class="widget_box_b">
                        <div class="contt">
                            <div class="fl_layer">
                                <h4 class="title">Cantidad Productos</h4>
                                <span class="line"></span>
                                <span class="data red" id="cantidad"> - </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="widget_box_b">
                        <div class="contt">
                            <div class="fl_layer">
                                <h4 class="title">Valor total</h4>
                                <span class="line"></span>
                                <span class="data" id="inventario"> - </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <form id="form_pdf" class="row" method="POST" action="{{ route('pdf_acueducto') }}" TARGET="_blank" hidden>
            {{ csrf_field() }}
                <input id="fechaReporte" type="text" class="form-control" name="fechaReporte">
                <button id="pdfsubmit" type="submit" form="form_pdf">Submit</button>
            </form>
            @include('desktop.account')
        </div>
    </div>

    @include('acueducto.modal_add')
    @include('acueducto.modal_report')
    @include('acueducto.modal_add2')
    @include('transaction.modal_mod')
    @include('acueducto.modal_venta')
    @include('acueducto.modal_product_mod')
    @include('product.modal_abono')
    @include('product.modal_list_abonos')
    @include('product.modal_movimientos')
    @include('customer.modal_add')
    @include('customer.modal_mod')
@endsection
@section('scripts')
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/datatable.min.js') }}"></script>
    <script src="{{ asset('js/moment.min.js') }}"></script>
    <script src="{{ asset('js/daterangepicker.js') }}"></script>
    <script src="{{ asset('js/pnotify.custom.min.js') }}"></script>
    <script src="{{ asset('js/validationEngine.min.js') }}"></script>
    <script src="{{ asset('js/validationEngine-es.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.2/js/bootstrap-select.min.js"></script>
    <script>

        function modalReport(){
            $('#modal_report').modal('show');
        }
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
        function openModalProduct(){
            $("#nombreCustomer").val("");
            $("#celularCustomer").val("");
            $("#observacionCustomer").val("");
            $("#cedulaCustomer").val("");
            $("#cantidadPr").val(0);
            $("#precioPr").val(0);
            $('#modal_add_product').modal('show');
        }
        function openModalGasto(transaction,precio,descripcion,tipo,fecha){
            $('#transaction_id_2').val(transaction);
            $('#precioSalida').html(precio);
            $('#descriptionGt').val(descripcion);
            $('#tipoGts').val(tipo);
            $('#FechaGts').val(fecha);
            $('#modal_add_transaction').modal('show');
            $('#cantIncome_2').val('');
            $('#productsList_2').val('');
            $('.selectpicker2').selectpicker('refresh');
            loadIncomes2();
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

        function reporteProductos(){
            window.open("{!! route('export_products') !!}", "_blank");
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
        function openModalVenta(transaction,precio, customer,descripcion,fecha){
            $('#modal_venta').modal('show');
            $('#id_transaction').val(transaction);
            $('#descripcionIncome').val(descripcion);
            $('#cantIncome').val('');
            $('#precioIncome').val('');
            $('#productsList').val('');
            $('#FechaIncome').val(fecha);
            $('#precioVenta').html(precio);
            $('.selectpicker2').selectpicker('refresh');
            loadIncomes();
        }
        function openMovimientos(product){
            $('#modal_movimientos').modal('show');
            loadMovimientos(product);
        }
        function loadProducts() {
            return;

        }
        function agregarIncome(validar) {
            if($("#id_transaction").val() == '' && validar ==2)
                return;
            if(validar==1) {
                var vproduct = $("#productsList").validationEngine('validate');
                var vCant = $("#cantIncome").validationEngine('validate');
                var vFecha = $("#FechaIncome").validationEngine('validate');
                var vPrecio = $("#precioIncome").validationEngine('validate');
                if (vproduct || vCant || vFecha)
                    return;
            }

            var product=$("#productsList").val();
            var cantidad=$("#cantIncome").val();
            var precio=$("#precioIncome").val();
            var descripcion=$("#descripcionIncome").val();
            var transaction=$("#id_transaction").val();
            var fecha=$("#FechaIncome").val();
            if(validar==2){
                product='';
                cantidad='';
            }

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "incomes_a",
                data: {
                    product : product,
                    cantidad : cantidad,
                    transaction : transaction,
                    precio      : precio,
                    descripcion : descripcion,
                    fecha       :fecha
                },
                success: function (datos) {
                    var texto ='Se agregó el producto con exito';
                    if(validar == '')
                        texto = 'Se actualizó la entrada con exito';
                    new PNotify({
                        title: 'Exito',
                        type: 'success',
                        text: texto
                    });
                    $("#productsList").val('');
                    $("#cantIncome").val('');
                    $("#precioIncome").val('');
                    if(transaction =='')
                        $("#id_transaction").val(datos['transaction_id']);
                    $('#precioVenta').html(datos['precio']);
                    loadIncomes();
                    $('.selectpicker2').selectpicker('refresh');
                    $('#transaction-table').dataTable()._fnAjaxUpdate();
                    desktop_index_vm.load();
                },
                error : function () {
                    //location = '/login';
                }
            });
        }
        function agregarIncome2(validar) {
            if($("#transaction_id_2").val() == '' && validar ==2)
                return;
            if(validar==1){
                var vproduct=$("#productsList_2").validationEngine('validate');
                var vCant=$("#cantIncome_2").validationEngine('validate');
                var vFecha=$("#FechaGts").validationEngine('validate');
                if (vproduct || vCant || vFecha)
                    return;
            }

            var product=$("#productsList_2").val();
            var cantidad=$("#cantIncome_2").val();
            var transaction=$("#transaction_id_2").val();
            var fecha=$("#FechaGts").val();
            var tipo=$("#tipoGts").val();
            if(validar==2){
                product='';
                cantidad='';
            }

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "incomes_acueducto",
                data: {
                    product : product,
                    cantidad : cantidad,
                    transaction : transaction,
                    descripcion : $('#descriptionGt').val(),
                    tipo: tipo,
                    fecha: fecha
                },
                success: function (datos) {
                    var texto ='Se agregó el producto con exito';
                    if(validar == '')
                        texto = 'Se actualizo la salida con exito';

                    new PNotify({
                        title: 'Exito',
                        type: 'success',
                        text: texto
                    });
                    $("#productsList_2").val('');
                    $("#cantIncome_2").val('');
                    if(transaction =='')
                        $("#transaction_id_2").val(datos['transaction_id']);
                    loadIncomes2();
                    $('#precioSalida').html(datos['precio']);
                    $('.selectpicker2').selectpicker('refresh');
                    $('#transaction-table').dataTable()._fnAjaxUpdate();
                    desktop_index_vm.load();
                },
                error : function () {
                    //location = '/login';
                }
            });
        }
        function loadIncomes2(){
            $('#income-table-2').DataTable({
                sDom           : 'r<Hlf><"datatable-scroll"t><Fip>',
                order          : [],
                processing     : true,
                serverSide     : true,
                deferRender    : true,
                destroy        : true,
                ajax: {
                    url  : '{!! route('get_incomes') !!}',
                    data : {
                        transaction        : $("#transaction_id_2").val(),
                        tipo        : 2,
                    },
                    error : function () {
                        location = '/login';
                    }
                },
                columns: [
                    { data: 'product_id', name: 'Producto', orderable  : false, searchable : false },
                    { data: 'cantidad', name: 'Cantidad', orderable  : false, searchable : false },
                    { data: 'action', name: 'acciones', orderable  : false, searchable : false },
                ],
                lengthMenu: [[ 10, 25, 50, -1], [ 10, 25, 50, "Todos"]]
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
                    $('#customerList').html(datos);
                    //$('#customerList_id').html(datos);
                    $('.selectpicker2').selectpicker('refresh');
                },
                error : function () {
                    location = '/login';
                }
            });
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

        function loadIncomes(){
            $('#income-table').DataTable({
                sDom           : 'r<Hlf><"datatable-scroll"t><Fip>',
                order          : [],
                processing     : true,
                serverSide     : true,
                deferRender    : true,
                destroy        : true,
                ajax: {
                    url  : '{!! route('get_incomes') !!}',
                    data : {
                        transaction        : $("#id_transaction").val(),
                    },
                    error : function () {
                        //location = '/login';
                    }
                },
                columns: [
                    { data: 'product_id', name: 'Producto', orderable  : false, searchable : false },
                    { data: 'cantidad', name: 'Cantidad', orderable  : false, searchable : false },
                    { data: 'precio', name: 'Precio', orderable  : false, searchable : false },
                    { data: 'action', name: 'acciones', orderable  : false, searchable : false },
                ],
                lengthMenu: [[ 10, 25, 50, -1], [ 10, 25, 50, "Todos"]]
            });
        }
        function loadMovimientos(product){
            $('#movimientos-table').DataTable({
                sDom           : 'r<Hlf><"datatable-scroll"t><Fip>',
                order          : [],
                processing     : true,
                serverSide     : true,
                deferRender    : true,
                destroy        : true,
                ajax: {
                    url  : '{!! route('get_movimientos') !!}',
                    data : {
                        product        : product,
                    },
                    error : function () {
                        //location = '/login';
                    }
                },
                columns: [
                    { data: 'description', name: 'Descripción', orderable  : false, searchable : false },
                    { data: 'tipo', name: 'Tipo', orderable  : false, searchable : false },
                    { data: 'cantidad', name: 'Cantidad', orderable  : false, searchable : false },
                    { data: 'precio', name: 'Precio', orderable  : false, searchable : false },
                    { data: 'total', name: 'Total', orderable  : false, searchable : false },
                ],
                lengthMenu: [[ 10, 25, 50, -1], [ 10, 25, 50, "Todos"]]
            });
        }
        function openModalMod(product_id){
            $('#modal_product_mod').modal('show');
            loadProduct(product_id);
            $('#idProductMod').val(product_id);
        }
        function openModalGastoMod(gasto_id){
            $('#modal_mod_transaction').modal('show');
            loadGasto(gasto_id);
            $('#transaction_id_mod').val(gasto_id);
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
        function crearReporte() {
            var mes =$("#mesReport").val();
            $('#fechaReporte').val(mes);
            $('#pdfsubmit').click();
        }
        function modificarProducto() {
            var vname=$("#namePrMod").validationEngine('validate');
            var vprecio=$("#precioPrMod").validationEngine('validate');
            if (vname || vprecio)
                return;

            var name=$("#namePrMod").val();
            var description=$("#unidPrMod").val();
            var minimo=$("#minimoPrMod").val();
            var cantidad=$("#cantidadPrMod").val();
            var precio=$("#precioPrMod").val();


            var idProduct = $("#idProductMod").val();

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "actualizar_producto",
                data: {
                    idProduct:idProduct,
                    name:name,
                    description:description,
                    minimo:minimo,
                    cantidad:cantidad,
                    precio:precio,
                },
                success: function (datos) {
                    new PNotify({
                        title: 'Exito',
                        type: 'success',
                        text: 'Se modificó el producto con exito'
                    });
                    $('#modal_product_mod').modal('hide');
                    $('#tickets-table').dataTable()._fnAjaxUpdate();
                },
                error : function () {
                    location = '/login';
                }
            });
        }
        function modificarGasto() {
            var vname=$("#descriptionGtMod").validationEngine('validate');
            var vprecio=$("#precioGtMod").validationEngine('validate');
            if (vname || vprecio)
                return;

            var tipo=$("#tipoGtMod").val();
            var description=$("#descriptionGtMod").val();
            var precio=$("#precioGtMod").val();


            var transaction = $("#transaction_id_mod").val();

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "actualizar_transaction",
                data: {
                    tipo : tipo,
                    description : description,
                    precio : precio,
                    transaction : transaction,
                },
                success: function (datos) {
                    new PNotify({
                        title: 'Exito',
                        type: 'success',
                        text: 'Se modificó el gasto con exito'
                    });
                    $('#modal_mod_transaction').modal('hide');
                    $('#transaction-table').dataTable()._fnAjaxUpdate();
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

        function eliminarIncome(id) {
            (new PNotify({
                title: 'Necesita confirmación',
                text: 'Esta seguro de querer eliminar el producto?',
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
                desktop_index_vm.deleteIncome(id);
            }).on('pnotify.cancel', function() {
               ;
            });
        }
        function eliminarIncome2(id) {
            (new PNotify({
                title: 'Necesita confirmación',
                text: 'Esta seguro de querer eliminar el producto?',
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
                desktop_index_vm.deleteIncome2(id);
            }).on('pnotify.cancel', function() {
               ;
            });
        }

        function eliminarTransaction(id) {
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
                desktop_index_vm.deleteTransaction(id);
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
        function crearProducto() {
            var vname=$("#namePr").validationEngine('validate');
            var vprecio=$("#precioPr").validationEngine('validate');
            var vminimo=$("#minimoPr").validationEngine('validate');
            if (vname || vprecio || vminimo)
                return;

            var name=$("#namePr").val();
            var minimo=$("#minimoPr").val();
            var cantidad=$("#cantidadPr").val();
            var precio=$("#precioPr").val();
            var description=$("#unidPr").val();

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "productos",
                data: {
                    name : name,
                    minimo : minimo,
                    cantidad : cantidad,
                    precio : precio,
                    description : description,
                },
                success: function (datos) {
                    $('#modal_add_product').modal('hide');
                    new PNotify({
                        title: 'Exito',
                        type: 'success',
                        text: 'Se agregó el producto con exito'
                    });
                    $('#tickets-table').dataTable()._fnAjaxUpdate();
                    $("#namePr").val('');
                    $("#minimoPr").val('');
                    $("#cantidadPr").val('');
                    $("#precioPr").val('');
                    loadProducts();
                },
                error : function () {
                    //location = '/login';
                }
            });
        }
        function crearGasto() {
            var vname=$("#descriptionGt").validationEngine('validate');
            var vprecio=$("#precioGt").validationEngine('validate');
            if (vname || vprecio)
                return;

            var tipo=$("#tipoGt").val();
            var description=$("#descriptionGt").val();
            var precio=$("#precioGt").val();

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "transaction",
                data: {
                    tipo : tipo,
                    description : description,
                    precio : precio,
                },
                success: function (datos) {
                    $('#modal_add_transaction').modal('hide');
                    new PNotify({
                        title: 'Exito',
                        type: 'success',
                        text: 'Se agregó el Gasto con exito'
                    });
                    $('#tickets-table').dataTable()._fnAjaxUpdate();
                    $("#precioGt").val('');
                    $("#descriptionGt").val('');
                    $('#transaction-table').dataTable()._fnAjaxUpdate();
                    desktop_index_vm.load();
                },
                error : function () {
                    //location = '/login';
                }
            });
        }
        function listarAbonos(id_prestamo) {
            desktop_index_vm.loadAbonos(id_prestamo);
            $('#modal_list_abonos').modal('show');
        }
        function loadProduct(id) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "get_producto",
                data: {
                    product_id:id
                },
                success: function (datos) {
                    $("#namePrMod").val(datos['name']);
                    $("#unidPrMod").val(datos['description']);
                    $("#minimoPrMod").val(datos['minimo']);
                    $("#cantidadPrMod").val(datos['cantidad']);
                    $("#precioPrMod").val(datos['precio']);
                },
                error : function () {
                   // location = '/login';
                }
            });
        }
        function loadGasto(id) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "get_transaction",
                data: {
                    id:id
                },
                success: function (datos) {
                    $("#tipoGtMod").val(datos['tipo']);
                    $("#descriptionGtMod").val(datos['description']);
                    $("#precioGtMod").val(datos['precio']);
                },
                error : function () {
                   // location = '/login';
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
        function mayus(e) {
            if (screen.width>=500 )
                e.value = e.value.toUpperCase();
        }
        $(function() {
            $("#descriptionGt, #tipoGts, #FechaGts").change(function(){
                if($("#transaction_id_2").val() !='')
                    agregarIncome2(2);
            });
            $("#descripcionIncome, #FechaIncome").change(function(){
                if($("#id_transaction").val() !='')
                    agregarIncome(2);
            });
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
                "startDate": "<?php $now = Carbon::now()->startOfMonth(); echo $now->format('Y-m-d')?>",
                "endDate": "<?php  $now = Carbon::now()->endOfMonth();  echo $now->format('Y-m-d')?>",
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
                desktop_index_vm.load();
                var tiempo = $('#Tiempo').val();
                if(tiempo != ''){
                    setCookie("tiempo", tiempo, 1)
                }
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
        function eliminarProduct(id){

            (new PNotify({
                title: 'Necesita confirmación',
                text: 'Esta seguro de querer eliminar el producto?',
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
                desktop_index_vm.deleteProduct(id);
            }).on('pnotify.cancel', function() {
                ;
            });
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
        function setCookie(cname, cvalue, exdays) {
            var d = new Date();
            d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
            var expires = "expires="+d.toUTCString();
            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        }

        function getCookie(cname) {
            var name = cname + "=";
            var ca = document.cookie.split(';');
            for(var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
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
                loadCustomers();
                setInterval(function(){
                    $('#tickets-table').dataTable()._fnAjaxUpdate();
                    $('#transaction-table').dataTable()._fnAjaxUpdate();
                    }, 60000);
                $('.selectpicker2').selectpicker();
                setTimeout(function (){
                    var tiempo = getCookie('tiempo');
                    if(tiempo !=''){
                        $('#Tiempo').val(tiempo);
                    }
                    desktop_index_vm.loadTable();
                    desktop_index_vm.load();
                },2000);

            },
            methods    : {
                load : function() {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: "get_status_acueducto",
                        data : {
                            range           : $("#Tiempo").val(),
                            customer        :$('#customerList').val()
                        },
                        success: function (datos) {
                            $("#entradas").html(datos['entradas']);
                            $("#instalaciones").html(datos['instalaciones']);
                            $("#reparaciones").html(datos['reparaciones']);
                            $("#extensiones").html(datos['extensiones']);
                            $("#inventario").html(datos['inventario']);
                            $("#cantidad").html(datos['cantidad']);
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
                            url  : '{!! route('get_productos') !!}',
                            error : function () {
                                ;
                            }
                        },
                        columns: [
                            { data: 'name', name: 'Nombre', orderable  : false, searchable : false },
                            { data: 'precio', name: 'Precio', orderable  : false, searchable : false },
                            { data: 'cantidad', name: 'Cantidad', orderable  : false, searchable : false },
                            { data: 'valor', name: 'Valor', orderable  : false, searchable : false },
                            { data: 'minimo', name: 'minimo', orderable  : false, searchable : false },
                            { data: 'action', name: 'Acciones', orderable  : false, searchable : false },
                        ],
                        lengthMenu: [[ 10, 25, 50, -1], [ 10, 25, 50, "Todos"]]
                    });
                    $('#transaction-table').DataTable({
                        sDom           : 'r<Hlf><"datatable-scroll"t><Fip>',
                        order          : [],
                        processing     : true,
                        serverSide     : true,
                        deferRender    : true,
                        destroy        : true,
                        ajax: {
                            url  : '{!! route('get_transactions') !!}',
                            data : {
                                range           : $("#Tiempo").val(),
                                customer        :$('#customerList').val()
                            },
                            error : function () {
                                ;
                            }
                        },
                        columns: [
                            { data: 'created_at', name: 'Descripción', orderable  : false, searchable : false },
                            { data: 'precio', name: 'Precio', orderable  : false, searchable : false },
                            { data: 'partner_id', name: 'Atendió', orderable  : false, searchable : false },
                            { data: 'action', name: 'Acciones', orderable  : false, searchable : false },
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
                deleteProduct : function(product) {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: "POST",
                            url: "eliminar_product",
                            data: {
                                product:product
                            },
                            success: function (datos) {
                                new PNotify({
                                    title: 'Exito',
                                    type: 'success',
                                    text: 'Se Eliminó el producto con exito'
                                });
                                $('#tickets-table').dataTable()._fnAjaxUpdate();
                            },
                            error : function () {
                                location = '/login';
                            }
                        });
                },
                deleteIncome : function(income) {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: "POST",
                            url: "eliminar_income",
                            data: {
                                income:income,
                                tipo : 1
                            },
                            success: function (datos) {
                                new PNotify({
                                    title: 'Exito',
                                    type: 'success',
                                    text: 'Se Eliminó el producto con exito'
                                });
                                loadIncomes();
                                $('#precioVenta').html(datos['precio']);
                                $('#transaction-table').dataTable()._fnAjaxUpdate();
                                this.load();
                            },
                            error : function () {
                                //location = '/login';
                            }
                        });
                },
                deleteIncome2 : function(income) {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: "POST",
                            url: "eliminar_income",
                            data: {
                                income:income,
                                tipo : 2
                            },
                            success: function (datos) {
                                new PNotify({
                                    title: 'Exito',
                                    type: 'success',
                                    text: 'Se Eliminó el producto con exito'
                                });
                                loadIncomes2();
                                $('#precioSalida').html(datos['precio']);
                                $('#transaction-table').dataTable()._fnAjaxUpdate();
                                $('#income-table-2').dataTable()._fnAjaxUpdate();
                                $('#income-table').dataTable()._fnAjaxUpdate();
                                this.load();
                            },
                            error : function () {
                                location = '/login';
                            }
                        });
                },
                deleteTransaction : function(transaction) {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: "POST",
                            url: "eliminar_transaction",
                            data: {
                                transaction:transaction,
                                tipo:1
                            },
                            success: function (datos) {
                                new PNotify({
                                    title: 'Exito',
                                    type: 'success',
                                    text: 'Se Eliminó la venta con exito'
                                });
                                this.load();
                                $('#transaction-table').dataTable()._fnAjaxUpdate();
                            },
                            error : function () {
                                //location = '/login';
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
                                loadProducts();
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