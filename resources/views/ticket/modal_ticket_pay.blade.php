<div id="modal_ticket_pay" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="alert alert-success alert-dismissible fade show">
            <div class="modal-header">
                <h4 class="modal-title">Es hora de cobrar</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="min-height: 130px;" >
                <div class="" role="alert">
                    <div class="col-lg-12 col-md-12">
                        <div class="widget_box_b">
                            <div class="contt">
                                <div class="fl_layer">
                                    <h4 class="title">ha tenido una duracion de :</h4>
                                    <span class="line"></span>
                                    <span class="data total" id="tiempo"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="col-lg-12 col-md-12">
                        <div class="widget_box_b">
                            <div class="contt">
                                <div class="fl_layer">
                                    <h4 class="title">Valor a pagar :</h4>
                                    <span class="line"></span>
                                    <span class="data total" id="pagar"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <span class="height_10"></span>
                    <button type="button" id="cobrar_id" onclick="form_pdf()" class="btn btn-success waves-effect waves-light"><i class="mdi mdi-content-save-all"></i>Imprimir recibo</button>
                    @if(isIva())
                        <button type="button" id="cobrar_id_iva" onclick="form_pdf()" class="btn btn-success waves-effect waves-light"><i class="mdi mdi-content-save-all"></i>Imprimir recibo (IVA)</button>
                    @endif
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
