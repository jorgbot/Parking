<div id="modal_convenio_in" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Agregar Convenio</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="min-height: 130px;" >
                <div id="formIn" class="row">
                    <div class="form-group col-sm-6">
                        <label for="">Nombre</label>
                        <input type="text" class="form-control validate[required]"  id="namec" min="0">
                    </div>
                    <div class="form-group col-sm-12">
                        <div class="head">
                            <h5>Precio carro</h5>
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="">Minuto</label>
                        <input type="number" class="form-control validate[onlyNumber]"  id="min_cars_pricec" min="0">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="">Hora</label>
                        <input type="number" class="form-control validate[onlyNumber]"  id="hour_cars_pricec" min="0">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="">Dia</label>
                        <input type="number" class="form-control validate[onlyNumber]"  id="day_cars_pricec" min="0">
                    </div>

                    <div class="form-group col-sm-12">
                        <div class="head">
                            <h5>Precio Moto</h5>
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="">Minuto</label>
                        <input type="number" class="form-control validate[onlyNumber]" id="min_motorcycles_pricec" min="0">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="">Hora</label>
                        <input type="number" class="form-control validate[onlyNumber]" id="hour_motorcycles_pricec" min="0">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="">Dia</label>
                        <input type="number" class="form-control validate[onlyNumber]" id="day_motorcycles_pricec" min="0">
                    </div>
                    <div class="form-group col-sm-12">
                        <div class="head">
                            <h5>Precio {{ isBici()?'Bicicletas':'Camionetas' }}</h5>
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="">Minuto</label>
                        <input type="number" class="form-control validate[onlyNumber]" id="min_van_pricec" min="0">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="">Hora</label>
                        <input type="number" class="form-control validate[onlyNumber]" id="hour_van_pricec" min="0">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="">Dia</label>
                        <input type="number" class="form-control validate[onlyNumber]" id="day_van_pricec" min="0">
                    </div>
                    <div class="form-group col-md-12 pt-3">
                        <button id="new_ticket" class="btn btn-primary full-width waves-effect waves-light" onclick="crearConvenio()"><strong>REGISTRAR</strong></button>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
