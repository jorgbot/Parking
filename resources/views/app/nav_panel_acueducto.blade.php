@php($random = random_int (0,4))
<div id="Bread_top">
    <div class="container-fluid">
        <section class="areaBread auto_margin">
            <div class="row">
                <div class="col-9">
                    @if(\Auth::user()->parking_id != 5)
                        @if($random==1)
                        <h1>"¿Que, pues, diremos a esto? Si Dios es por nosotros, ¿Quién contra nosotros?" : Romanos 8:31</h1>
                        @elseif($random==2)
                        <h1>"Si puedes soñarlo, puedes hacerlo" : Walt Disney</h1>
                        @elseif($random==3)
                        <h1>"El éxito no es la clave de la felicidad. La felicidad es la clave del éxito" : Albert Schweitzer</h1>
                        @elseif($random==4)
                        <h1>"Si crees que puedes, ya estás a medio camino" : Theodore Roosevelt</h1>
                        @else
                        <h1>TEMM Soft te desea un excelente día</h1>
                        @endif
                    @else
                        <div class="row">
                            <div class="col-4">
                                <div class="logo"><a href=""><img src="images/jr5.jpeg" class="img-retina" alt="" style="max-width: 150px;"></a></div>
                            </div>
                            <div class="col-8">
                            <h1> Parqueadero JR la 5</h1>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="col-3 text-right">
                    <span class="member">Hola, {{ Auth::user()->name }}</span>
                </div>
            </div>
        </section>
    </div>
</div>
<nav class="nav_patner_panel">
    <div class="container-fluid">
        <ul class="auto_margin">
            <li id="nav_inicio" v-bind:class="{ active : nav == 'all' }"><a href="#!" v-on:click="all = true; nav = 'all'; month= false; loadTable()">Inicio</a></li>
            <li v-bind:class="{ active : nav == 'products' }"><a href="#!" v-on:click="all = false; month= true; nav = 'products'; loadTable('month')">Productos</a></li>
            <li v-bind:class="{ active : nav == 'account' }"><a href="#!" v-on:click="all = false; month= false; nav = 'account'">Mi cuenta</a></li>
        </ul>
    </div>
</nav>