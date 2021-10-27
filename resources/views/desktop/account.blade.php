<div class="row" v-show="nav == 'account'">
    @php($user = Auth::user())
    @php($parking = App\Parking::find($user->parking_id))
    <div class="col-sm-4 padding_20">
        <div class="Mods">
            <div class="head">
                <h3>Cuenta</h3>
            </div>
            <div class="body">
                <hr>
                <p>
                    {{ $user->name.' '.$user->last_name }} <br>
                    <strong>Email:</strong> {{ $user->email }}
                </p>
            </div>
        </div>
    </div>
    <div class="col-sm-8">
        <div class="mi-cuenta">
            <h2>Actualizar cuenta</h2>
            <hr>
            <form class="row">
                <div class="form-group col-sm-12">
                    <div class="head">
                        <h5>Cambiar Contraseña</h5>
                    </div>
                </div>
                <div class="form-group col-sm-6">
                    <label for="">Contraseña actual</label>
                    <input type="password" class="form-control validate[required,minSize[6]]" name="currentPassword" id="currentPassword" value="">
                </div>
                <div class="form-group col-sm-6">
                    <label for="">Contraseña</label>
                    <input type="password" class="form-control validate[required,minSize[6]]" name="password" id="password" value="">
                </div>
                <div class="form-group col-sm-6">
                    <label for="">Confirme Contraseña</label>
                    <input type="password" class="form-control validate[required,equals[password]]" name="confirm_password" value="">
                </div>

                <div class="form-group col-sm-12">
                    <div class="head">
                        <h5>Informacion Personal</h5>
                    </div>
                </div>
                <div class="form-group col-sm-6">
                    <label for="">Nombre</label>
                    <input type="text" class="form-control validate[required]" name="new_name" value="{{ $user->name }}">
                </div>
                <div class="form-group col-sm-6">
                    <label for="">Apellidos</label>
                    <input type="text" class="form-control validate[required]" name="new_last_name" value="{{ $user->last_name }}">
                </div>
                <div class="form-group col-sm-6">
                    <label for="">Email</label>
                    <input type="text" class="form-control validate[required]" name="new_email" value="{{ $user->email }}">
                </div>
                <div class="col-sm-12">
                    <span class="height_10"></span>
                    <button type="button" onclick="actualizarCuenta()" class="btn btn-success waves-effect waves-light"><i class="mdi mdi-content-save-all"></i> Actualizar</button>
                </div>
                <span class="height_30"></span>
            </form>
        </div>
        @if($user->type==1)
        <div class="mi-cuenta">
            <span class="height_30"></span><br>
            <h2>Actualizar cuenta empresa</h2>
            <hr>
            <form class="row">
                <div class="form-group col-sm-12">
                    <div class="head">
                        <h5>Precio carro</h5>
                    </div>
                </div>
                <div class="form-group col-sm-6">
                    <label for="">Minuto</label>
                    <input type="number" class="form-control validate[ onlyNumber]"  id="min_cars_price" min="0" value="{{ $parking->min_cars_price }}">
                </div>
                <div class="form-group col-sm-6">
                    <label for="">Hora</label>
                    <input type="number" class="form-control validate[required, onlyNumber]"  id="hour_cars_price" min="0" value="{{ $parking->hour_cars_price }}">
                </div>
                <div class="form-group col-sm-6">
                    <label for="">Dia</label>
                    <input type="number" class="form-control validate[required, onlyNumber]"  id="day_cars_price" min="0" value="{{ $parking->day_cars_price }}">
                </div>
                <div class="form-group col-sm-6">
                    <label for="">Mensualidad</label>
                    <input type="number" class="form-control validate[required, onlyNumber]" id="monthly_cars_price" min="0" value="{{ $parking->monthly_cars_price }}">
                </div>

                <div class="form-group col-sm-12">
                    <div class="head">
                        <h5>Precio Moto</h5>
                    </div>
                </div>
                <div class="form-group col-sm-6">
                    <label for="">Minuto</label>
                    <input type="number" class="form-control validate[onlyNumber]" id="min_motorcycles_price" min="0" value="{{ $parking->min_motorcycles_price }}">
                </div>
                <div class="form-group col-sm-6">
                    <label for="">Hora</label>
                    <input type="number" class="form-control validate[required, onlyNumber]" id="hour_motorcycles_price" min="0" value="{{ $parking->hour_motorcycles_price }}">
                </div>
                <div class="form-group col-sm-6">
                    <label for="">Dia</label>
                    <input type="number" class="form-control validate[required, onlyNumber]" id="day_motorcycles_price" min="0" value="{{ $parking->day_motorcycles_price }}">
                </div>
                <div class="form-group col-sm-6">
                    <label for="">Mensualidad</label>
                    <input type="number" class="form-control validate[required, onlyNumber]" id="monthly_motorcycles_price"  min="0" value="{{ $parking->monthly_motorcycles_price }}">
                </div>
                <div class="form-group col-sm-12">
                    <div class="head">
                        <h5>Precio {{ isBici()?'Bicicletas':'Camionetas' }}</h5>
                    </div>
                </div>
                <div class="form-group col-sm-6">
                    <label for="">Minuto</label>
                    <input type="number" class="form-control validate[onlyNumber]" id="min_van_price" min="0" value="{{ $parking->min_van_price }}">
                </div>
                <div class="form-group col-sm-6">
                    <label for="">Hora</label>
                    <input type="number" class="form-control validate[required, onlyNumber]" id="hour_van_price" min="0" value="{{ $parking->hour_van_price }}">
                </div>
                <div class="form-group col-sm-6">
                    <label for="">Dia</label>
                    <input type="number" class="form-control validate[required, onlyNumber]" id="day_van_price" min="0" value="{{ $parking->day_van_price }}">
                </div>
                <div class="form-group col-sm-6">
                    <label for="">Mensualidad</label>
                    <input type="number" class="form-control validate[required, onlyNumber]" id="monthly_van_price"  min="0" value="{{ $parking->monthly_van_price }}">
                </div>
                <div class="form-group col-sm-12">
                    <div class="head">
                        <h5>Otros</h5>
                    </div>
                </div>
                <div class="form-group col-sm-6">
                    <label for="">Tiempo muerto</label>
                    <input type="number" class="form-control validate[required, onlyNumber]" id="free_time" min="0" max="20" value="{{ $parking->free_time }}">
                </div>
                <div class="form-group col-sm-6">
                    <label for="">Numero de carros</label>
                    <input type="number" class="form-control validate[required, onlyNumber]" min="0" id="cars_num" value="{{ $parking->cars_num }}">
                </div>
                <div class="form-group col-sm-6">
                    <label for="">Numero de motos</label>
                    <input type="number" class="form-control validate[required, onlyNumber]" min="0" id="motorcycles_num" value="{{ $parking->motorcycles_num }}">
                </div>

                <div class="col-sm-12">
                    <span class="height_10"></span>
                    <button type="button" onclick="actualizarCuentaParking()" class="btn btn-success waves-effect waves-light"><i class="mdi mdi-content-save-all"></i>Actualizar</button>
                </div>
                <span class="height_30"></span>
            </form>
        </div>
            @endif
    </div>
</div>