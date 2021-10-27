<div id="modal_product_mod" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Producto</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="min-height: 130px;" >
                <div id="formAddCustomer" class="row">
                    <div class="form-group col-md-12" style="display: none">
                        <label for="nombre" class="control-label">producto</label>
                        <input id="idProductMod" type="text" class="form-control validate[required]">
                    </div>
                    <div class="form-group col-md-12">
                        <label for="nombre" class="control-label">Nombre</label>
                        <input onkeyup="mayus(this);" id="namePrMod" type="text" class="form-control validate[required]">
                    </div>
                    <div class="form-group col-md-12">
                        <label for="fechas" class="control-label">Descripción</label>
                        <input id="descriptionPrMod" type="text" class="form-control">
                    </div>
                    <div class="form-group col-md-12">
                        <label for="nombre" class="control-label">minimo</label>
                        <input id="minimoPrMod" type="number" class="form-control validate[required]">
                    </div>
                    <div class="form-group col-md-12">
                        <label for="nombre" class="control-label">cantidad</label>
                        <input id="cantidadPrMod" type="number" class="form-control validate[required]">
                    </div>
                    <div class="form-group col-md-12">
                        <label for="nombre" class="control-label">precio</label>
                        <input id="precioPrMod" type="number" class="form-control validate[required]">
                    </div>

                    <div class="form-group col-md-12 pt-3">
                        <button id="new_prestamo" class="btn btn-primary full-width waves-effect waves-light" onclick="modificarProducto()"><strong>MODIFICAR</strong></button>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
