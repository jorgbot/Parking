<div class="row" v-show="nav == 'convenios'">
    <div class="col-sm-4 padding_20">
        <div class="Mods">
            <div class="head">
                <h3>Convenios</h3>
            </div>
            @if(\Auth::user()->type == 1)
            <div class="body">
                <hr>
                <div class="col-md-12" style="text-align: center;">
                    <button type="button" onclick="openModalInConvenio()" class="btn btn-primary col-md-10 btn-lg">Agregar</button>
                </div>
            </div>
            @endif
        </div>
    </div>
    <div class="col-sm-8">
        <div class="mi-cuenta">
            <h2>Lista de convenios</h2>
            <hr>
            <div class="row" v-show="nav == 'convenios'">
                <div class="col-12" style="overflow:  auto;">
                    <table class="table responsive" id="convenios-table">
                        <thead>
                        <tr>
                            <th class="all">Nombre</th>
                            <th class="all">Carro</th>
                            <th class="min-tablet">Moto</th>
                            <th class="min-tablet">{{ isBici()?'Bicicleta':'Camioneta' }}</th>
                            <th class="all">acciones</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>