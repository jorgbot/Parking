<div id="modal_report" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Fecha del Reporte</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="min-height: 130px;" >
                <div id="formReport" class="row">
                    <div class="form-group col-md-12">
                        <label for="nombre" class="control-label">Fecha</label>
                        <input id="mesReport" type="month" class="form-control validate[required]">
                        <hr>
                    </div>
                    <div class="form-group col-md-12 pt-3">
                        <button id="new_income" class="btn btn-primary full-width waves-effect waves-light" onclick="crearReporte()"><strong>Crear</strong></button>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
