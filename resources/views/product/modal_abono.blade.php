<div id="modal_abono" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Abono</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="min-height: 130px;" >
                <div id="formAddAbono" class="row">
                    <div class="form-group col-md-12" style="display: none">
                        <label for="nombre" class="control-label">prestamo</label>
                        <input id="abonoPrestamo" type="text" class="form-control validate[required]">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="nombre" class="control-label">Fecha</label>
                        <input id="abonoFecha" type="date" class="form-control validate[required]">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="nombre" class="control-label">Tipo</label>
                        <select class="form-control" id="tipoAbono">
                            <option value="1">Abono</option>
                            <option value="2">Pago Total</option>
                        </select>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="nombre" class="control-label">Valor</label>
                        <input id="abonoValor" type="number" class="form-control validate[required]">
                    </div>
                    <div class="form-group col-md-12 pt-3">
                        <button id="new_abono" class="btn btn-primary full-width waves-effect waves-light" onclick="crearAbono()"><strong>REGISTRAR</strong></button>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
