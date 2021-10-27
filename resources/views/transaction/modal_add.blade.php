<div id="modal_add_transaction" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nuevo Gasto</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="min-height: 130px;" >
                <input id="transaction_id_2" type="text" class="form-control" style="display: none">
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="fechas" class="control-label">Descripción</label>
                        <input id="descriptionGt" type="text" class="form-control">
                    </div>
                    <div class="form-group col-md-12">
                        <label for="nombre" class="control-label">precio</label>
                        <input id="precioGt" type="number" class="form-control validate[required]">
                    </div>
                    <div class="form-group col-md-12">
                        <label for="nombre" class="control-label">Tipo</label>
                        <select id="tipoGt" name="type" class="form-control">
                            <option value="3">Gasto</option>
                            <option value="2">Surtido</option>
                        </select>
                    </div>
                    <div class="form-group col-md-12 pt-3" id="gasto_opt">
                        <button id="new_customer" class="btn btn-primary full-width waves-effect waves-light" onclick="crearGasto()"><strong>Guardar</strong></button>
                    </div>
                </div>
                <div class="row" id="surtido_opt" style="display:none">
                    <input id="id_transaction_2" type="number" class="form-control" style="display: none">
                    <div class="form-group col-md-6">
                        <label for="nombre" class="control-label">Productos</label>
                        @php($products = App\Product::where('parking_id',Illuminate\Support\Facades\Auth::user()->parking_id)->get())
                        <select class="validate[required] selectpicker2" id="productsList_2"  data-live-search="true" data-size="10">
                            <option value="">Seleccionar</option>
                            @foreach($products as $product)
                                {!! '<option data-toggle="tooltip" title="'.$product->description.'"value="'.$product->id_product.'">'.$product->name.(!empty($product->cantidad) && $product->cantidad !='-1'?' ('.$product->cantidad.')':'').'</option>' !!}
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="nombre" class="control-label">Cantidad</label>
                        <input id="cantIncome_2" type="number" class="form-control validate[required]">
                    </div>
                    <div class="form-group col-md-12 pt-3">
                        <button id="new_income_2" class="btn btn-primary full-width waves-effect waves-light" onclick="agregarIncome2(1)"><strong>Agregar</strong></button>
                    </div>
                    <div class="form-group col-md-12 pt-3">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Terminar</button>
                    </div>
                    <div class="col-12"  style="overflow:  auto;">
                        <table class="table responsive" id="income-table-2">
                            <thead>
                            <tr>
                                <th class="all">Producto</th>
                                <th class="min-tablet">Cantidad</th>
                                <th class="all">acciones</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
