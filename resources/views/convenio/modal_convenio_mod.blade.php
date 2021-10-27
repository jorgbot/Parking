<div id="modal_convenio_mod" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Convenio</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="min-height: 130px;" >
                <div id="formIn" class="row">
                        <input type="text" id="convenio_id_mod" style="display: none">
                    <div class="form-group col-sm-6">
                        <label for="">Nombre</label>
                        <input type="text" class="form-control validate[required]"  id="namecm" min="0">
                    </div>
                    <div class="form-group col-sm-12">
                        <div class="head">
                            <h5>Precio carro</h5>
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="">Minuto</label>
                        <input type="number" class="form-control validate[onlyNumber]"  id="min_cars_pricecm" min="0">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="">Hora</label>
                        <input type="number" class="form-control validate[onlyNumber]"  id="hour_cars_pricecm" min="0">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="">Dia</label>
                        <input type="number" class="form-control validate[onlyNumber]"  id="day_cars_pricecm" min="0">
                    </div>

                    <div class="form-group col-sm-12">
                        <div class="head">
                            <h5>Precio Moto</h5>
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="">Minuto</label>
                        <input type="number" class="form-control validate[onlyNumber]" id="min_motorcycles_pricecm" min="0">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="">Hora</label>
                        <input type="number" class="form-control validate[onlyNumber]" id="hour_motorcycles_pricecm" min="0">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="">Dia</label>
                        <input type="number" class="form-control validate[onlyNumber]" id="day_motorcycles_pricecm" min="0">
                    </div>
                    <div class="form-group col-sm-12">
                        <div class="head">
                            <h5>Precio {{ isBici()?'Bicicletas':'Camionetas' }}</h5>
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="">Minuto</label>
                        <input type="number" class="form-control validate[onlyNumber]" id="min_van_pricecm" min="0">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="">Hora</label>
                        <input type="number" class="form-control validate[onlyNumber]" id="hour_van_pricecm" min="0">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="">Dia</label>
                        <input type="number" class="form-control validate[onlyNumber]" id="day_van_pricecm" min="0">
                    </div>
                    <div class="form-group col-md-12 pt-3">
                        <button id="new_ticket" class="btn btn-primary full-width waves-effect waves-light" onclick="modificarConvenio()"><strong>Modificar</strong></button>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
