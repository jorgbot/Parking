<div id="modal_mod" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Cliente</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="min-height: 130px;" >
                <div id="formAddCustomer" class="row">
                    <div class="form-group col-md-12" style="display: none">
                        <label for="nombre" class="control-label">Customer</label>
                        <input id="idCustomerMod" type="text" class="form-control validate[required]">
                    </div>
                    <div class="form-group col-md-12">
                        <label for="nombre" class="control-label">Nombre</label>
                        <input onkeyup="mayus(this);" id="nombreCustomerMod" type="text" class="form-control validate[required]">
                    </div>
                    <div class="form-group col-md-12">
                        <label for="nombre" class="control-label">Cedula</label>
                        <input id="cedulaCustomerMod" type="text" class="form-control validate[required]">
                    </div>
                    <div class="form-group col-md-12">
                        <label for="nombre" class="control-label">Celular</label>
                        <input id="celularCustomerMod" type="number" class="form-control validate[required]">
                    </div>
                    <div class="form-group col-md-12">
                        <label for="fechas" class="control-label">Observación</label>
                        <input id="observacionCustomerMod" type="text" class="form-control">
                    </div>
                    <div class="form-group col-md-12 pt-3">
                        <button id="new_customer" class="btn btn-primary full-width waves-effect waves-light" onclick="modificarCliente()"><strong>Modificar</strong></button>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
