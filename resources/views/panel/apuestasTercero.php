<?php 
    $controller = new App\Http\Controllers\PollaController($id);
    $partidos = $controller->get_partidos()[0]->partidos;
    $nombre= User::find($id)->nombre;
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
                            <h2>Resultados</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="latest-result">
            <div class="container">
                <div class="row">
                    @foreach($partidos as $partido)
                    @if($partido->estadopartido_id!=1)
                    <div class="col-xl-6 col-lg-6" id="div{{$partido->partido_id}}">
                        <div class="single-match">
                            <div class="part-head">
                                <h5 class="match-title">{{$partido->titulo}}</h5>
                            </div>
                            <div class="part-team">
                                <div class="single-team win-team">
                                    <div class="logo">
                                      @if($partido->res_local_real>$partido->res_visita_real)
                                      <span class="win">Ganador</span>
                                      @endif
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
                                            <li>{{$partido->res_local_real}}</li>
                                            <li>{{$partido->res_visita_real}}</li>
                                        </ul>
                                        <span class="text">{{$partido->estado}}</span>
                                    </div>
                                </div>
                                <div class="single-team">
                                    <div class="logo">
                                      @if($partido->res_visita_real>$partido->res_local_real)
                                      <span class="win">Ganador</span>
                                      @endif
                                        <img src="{{$partido->logo_visita}}" alt="">
                                    </div>
                                    <span class="team-name">{{$partido->visita}}</span>
                                </div>
                            </div>
                            <div class="col-md-12" style="text-align:center;padding: 0px;">
                                <a id="boton_partido->partido_id" onclick="return null;" 
                                @if($partido->puntos==0)
                                class="btn btn-danger" 
                                @elseif($partido->puntos==1)
                                class="btn btn-warning"
                                @elseif($partido->puntos==3)
                                class="btn btn-success"
                                @endif
                                style="cursor: default;width:100%;color:white"
                                >
                                @if($partido->pronostico==0)
                                    No ha realizado apuesta!!!
                                @else
                                Apuesta de {{$nombre}}: 
                                <img src="{{$partido->logo_local}}" style="height: 15px;"> 
                                    {{$partido->res_local}} - {{$partido->res_visita}} 
                                
                                <img src="{{$partido->logo_visita}}" style="height: 15px;">
                                @endif
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
        <!-- latest result end -->
@endsection