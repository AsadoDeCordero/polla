<?php 
$controller = new App\Http\Controllers\PollaController();
    $todos = $controller -> get_partidos()[0]->partidos;
    $terminados = [];
    $noTerminados = [];
    $partidos = [];
    foreach($todos as $partido) {
      if($partido->estadopartido_id==3)
      array_push($terminados,$partido);
      else
      array_push($noTerminados,$partido);
    }
    $partidos = array_merge($noTerminados,$terminados);
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
                                @if($partido->estadopartido_id==1)
                                <a id="boton_{{$partido->partido_id}}" onclick="enviar('{{$partido->partido_id}}')" class="btn @if($partido->pronostico==1) btn-warning @else btn-success @endif" style="width:100%;color:white"> @if($partido->pronostico==1) Modificar pron??stico @else Enviar pron??stico @endif</a>
                                @else
                                <a onclick="return;" class="btn btn-secondary" style="width:100%;color:white;cursor: default;background: #6c757d;">Apuestas cerradas</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <!-- latest result end -->
@endsection