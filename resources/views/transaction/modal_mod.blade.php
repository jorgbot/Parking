<div id="modal_mod_transaction" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Gasto</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="min-height: 130px;" >
                <div id="formAddCustomer" class="row">
                    <input id="transaction_id_mod" type="text" class="form-control" style="display: none">
                    <div class="form-group col-md-12">
                        <label for="fechas" class="control-label">Descripción</label>
                        <input id="descriptionGtMod" type="text" class="form-control">
                    </div>
                    <div class="form-group col-md-12">
                        <label for="nombre" class="control-label">precio</label>
                        <input id="precioGtMod" type="number" class="form-control validate[required]">
                    </div>
                    <div class="form-group col-md-12">
                        <label for="nombre" class="control-label">Tipo</label>
                        <select id="tipoGtMod" name="type" class="form-control">
                            <option value="3">Gasto</option>
                            <option value="2">Surtido</option>
                        </select>
                    </div>

                    <div class="form-group col-md-12 pt-3">
                        <button id="new_prestamo" class="btn btn-primary full-width waves-effect waves-light" onclick="modificarGasto()"><strong>MODIFICAR</strong></button>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
