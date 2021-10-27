<div id="modal_ticket_mod" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Modificar Ticket</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="min-height: 130px;" >
                <div id="formIn" class="row">
                    <input id="ticket_id_mod" type="text" class="form-control" style="display: none">
                    <div class="form-group col-md-12">
                        <label for="">Fecha y hora</label>
                        <input id="fecha_mod" type="text" class="form-control" disabled>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="plate_mod" class="control-label">PLACA</label>

                        <div>
                            <input id="plate_mod" type="plate" class="form-control validate[required]" value="{{ old('plate') }}" onkeypress="validar(event)" required autofocus {!! \Auth::user()->type == 2 ?'disabled':'' !!}>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="">Tipo</label>
                        <select class="form-control" id="typeIn_mod" {!! \Auth::user()->type == 2 ?'disabled':'' !!}>
                            <!--<option value="1" selected >Carro</option>-->
                            <option value="2" selected >Moto</option>
                            @if($typeParking == 2)
                                <option value="3">{{ isBici()?'Bicicleta':'Camioneta' }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="">Horario</label>
                        <select class="form-control" id="schedule_mod" onchange="mensualidad2()" {!! \Auth::user()->type == 2 ?'disabled':'' !!}>
                            <option value="1" selected >Hora</option>
                            <option value="2">Dia</option>
                            <option value="3">Mes</option>
                        </select>
                    </div>
                    <div class="form-group col-md-12{{ $errors->has('name') ? ' has-error' : '' }}">
                        <label for="drawer" class="control-label"># Cascos</label>
                        <input id="drawer_mod" type="text" class="form-control" >
                    </div>

                    <div class="form-group col-md-12{{ $errors->has('name') ? ' has-error' : '' }}">
                        <label for="extra" class="control-label">Extra</label>
                        <input id="extra" type="number" class="form-control" {!! \Auth::user()->type == 2 ?'disabled':'' !!}>
                    </div>
                    <div class="form-group col-md-12" id="nameIn_mod">
                        <label for="nombre" class="control-label">Nombre</label>
                        <input id="nombreIn_mod" type="text" class="form-control validate[required]" {!! \Auth::user()->type == 2 ?'disabled':'' !!}>
                    </div>
                    <div class="form-group col-md-12" id="priceIn_mod">
                        <label for="nombre" class="control-label">Precio</label>
                        <input id="precioIn_mod" type="number" class="form-control validate[required]" name="price" {!! \Auth::user()->type == 2 ?'disabled':'' !!}>
                    </div>
                    <div class="form-group col-md-12" id="mailIn_mod">
                        <label for="nombre" class="control-label">Email</label>
                        <input id="emailIn_mod" type="text" class="form-control validate[required]" name="email" {!! \Auth::user()->type == 2 ?'disabled':'' !!}>
                    </div>
                    <div class="form-group col-md-12" id="movilIn_mod">
                        <label for="nombre" class="control-label">Celular</label>
                        <input id="celularIn_mod" type="number" class="form-control validate[required]" name="nombre" {!! \Auth::user()->type == 2 ?'disabled':'' !!}>
                    </div>
                    <div class="form-group col-md-12" id="rangeIn_mod">
                        <label for="fechas" class="control-label">Rango fechas</label>
                        <input id="date_range_mod" type="text" class="form-control validate[required]" {!! \Auth::user()->type == 2 ?'disabled':'' !!}>
                    </div>
                    <div class="form-group col-md-12 pt-3">
                        <button class="btn btn-primary full-width waves-effect waves-light" onclick="modificarTicket()"><strong>Modificar</strong></button>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
