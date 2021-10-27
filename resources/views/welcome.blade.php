@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="areaPartner">
            


            <section class="partner-beneficios auto_margin">
                <div class="head">
                    <h3>Beneficios</h3>
                </div>

                <div class="list-beneficios">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="item">
                                <figure><img src="images/ico-partner-01.svg" class="img-fluid" alt=""></figure>
                                <h3>Date a conocer</h3>
                                <p>Cuando eres ordenado tus clientes lo notan y tu lo disfrutas.</p>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="item">
                                <figure><img src="images/ico-partner-02.svg" class="img-fluid" alt=""></figure>
                                <h3>Trabaja desde cualquier lugar</h3>
                                <p>Deja atras las antiguas herramientas en donde estas amarrado a un solo dispositivo, con nosotros puedes estar atento de tu negocio desde cualquier lugar</p>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="item">
                                <figure><img src="images/ico-partner-03.svg" class="img-fluid" alt=""></figure>
                                <h3>Aumenta tu productividad</h3>
                                <p>Cuando conoces tus gastos y tus ingresos puedes planear mucho mejor que hacer con tu dinero.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    @include('desktop.login.modal_login')
    <!---->
    <p class="height_20"></p>
    <!---->
@endsection
@section('scripts')
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        var app_e = new Vue({
            el: "#modal_login",
        });

        function openModal(){
            $('#modal_login').modal('show');
        }
    </script>
@endsection
