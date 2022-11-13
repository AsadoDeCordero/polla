<?php 
    $partidos = App\Http\Controllers\PollaController::get_partidos()[0]->partidos;
    //dd($partidos);
?>
@extends('panel.template')
@section('body')
<!-- breadcrumb begin -->
        <div class="breadcrumb-bettix latest-result-page">
            <div class="container">
                <div class="row">
                    <div class="col-xl-7 col-lg-7">
                        <div class="breadcrumb-content">
                            <h2>Apuestas</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- breadcrumb end -->


        <div class="latest-result">
            <div class="container">
                <div class="row">
                <!--
                    <div class="col-xl-6 col-lg-6">
                        <div class="single-match">
                            <div class="part-head">
                                <h5 class="match-title">BBPL 2019 Semi Final</h5>
                                <span class="match-venue">Venue : Sher-e-Bangla National Stadium. Mirpur, Dhaka</span>
                            </div>
                            <div class="part-team">
                                <div class="single-team">
                                    <div class="logo">
                                        <img src="assets/img/team-1.png" alt="">
                                    </div>
                                    <span class="team-name">Khulna Tigers</span>
                                </div>
                                <div class="match-details">
                                    <div class="match-time">
                                        <span class="date">Fri 09 Oct 2019 || 09:00 am</span>
                                    </div>
                                    <div class="goal">
                                        <ul>
                                            <li>2</li>
                                            <li>3</li>
                                        </ul>
                                        <span class="text">full time</span>
                                    </div>
                                </div>
                                <div class="single-team win-team">
                                    <div class="logo">
                                        <span class="win">win</span>
                                        <img src="assets/img/team-2.png" alt="">
                                    </div>
                                    <span class="team-name">Dhaka Platoon</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6">
                        <div class="single-match">
                            <div class="part-head">
                                <h5 class="match-title">BBPL 2019 Semi Final</h5>
                                <span class="match-venue">Venue : Sher-e-Bangla National Stadium. Mirpur, Dhaka</span>
                            </div>
                            <div class="part-team">
                                <div class="single-team win-team">
                                    <div class="logo">
                                        <span class="win">win</span>
                                        <img src="assets/img/team-3.png" alt="">
                                    </div>
                                    <span class="team-name">Khulna Tigers</span>
                                </div>
                                <div class="match-details">
                                    <div class="match-time">
                                        <span class="date">Fri 09 Oct 2019 || 09:00 am</span>
                                    </div>
                                    <div class="goal">
                                        <ul>
                                            <li>2</li>
                                            <li>3</li>
                                        </ul>
                                        <span class="text">full time</span>
                                    </div>
                                </div>
                                <div class="single-team">
                                    <div class="logo">
                                        <img src="assets/img/team-4.png" alt="">
                                    </div>
                                    <span class="team-name">Dhaka Platoon</span>
                                </div>
                            </div>
                        </div>
                    </div> 
                    -->
                    @foreach($partidos as $partido)
                    <div class="col-xl-6 col-lg-6" id="div{{$partido->partido_id}}">
                        <div class="single-match">
                            <div class="part-head">
                                <h5 class="match-title">{{$partido->titulo}}</h5>
                            </div>
                            <div class="part-team">
                                <input type="hidden" name="partido_id" id="partido{{$partido->partido_id}}" value="{{$partido->partido_id}}">
                                <div class="single-team win-team">
                                    <div class="logo">
                                        <img src="{{$partido->logo_local}}" alt="">
                                    </div>
                                    <span class="team-name">{{$partido->local}}</span>
                                </div>
                                <div class="match-details">
                                    <div class="match-time">
                                        <span class="date">{{$partido->fecha_completa}}</span>
                                    </div>
                                    <div class="goal">
                                        <ul>
                                            <li><input type="text" class=" textoPrediccion form-control" id="res_local{{$partido->partido_id}}" @if($partido->pronostico==1) value="{{$partido->res_local}}"@endif></li>
                                            <li><input type="text" class=" textoPrediccion form-control" id="res_visita{{$partido->partido_id}}" @if($partido->pronostico==1) value="{{$partido->res_visita}}"@endif></li>
                                        </ul>
                                        <span class="text">{{$partido->estado}}</span>
                                    </div>
                                </div>
                                <div class="single-team">
                                    <div class="logo">
                                        <img src="{{$partido->logo_visita}}" alt="">
                                    </div>
                                    <span class="team-name">{{$partido->visita}}</span>
                                </div>
                            </div>
                            <div class="col-md-12" style="text-align:center;padding: 0px;">
                                <a id="boton_{{$partido->partido_id}}" onclick="enviar('{{$partido->partido_id}}')" class="btn @if($partido->pronostico==1) btn-warning @else btn-success @endif" style="width:100%;color:white"> @if($partido->pronostico==1) Modificar pronóstico @else Enviar pronóstico @endif</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <!-- latest result end -->
@endsection