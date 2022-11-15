<?php 
    $controller = new App\Http\Controllers\PollaController();
    $participantes = $controller->get_tabla();
    //dd($participantes);
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
        <!-- breadcrumb end -->


        <div class="latest-result">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead class="thead-dark">
                                <th>Nombre</th>
                                <th style="width:1%;background: #ff5722;">X</th>
                                <th style="width:1%;background:#ffeb3b">✓</th>
                                <th style="width:1%;background: #4caf50;">✓</th>
                                <th style="width:1%">Puntaje</th>
                            </thead>
                            <tbody>
                                @foreach($participantes as $participante)
                                <tr @if($participante->id ==Auth::user()->id) style="background:#c0ffab" @endif>
                                    <td>{{$participante->nombre}}</td>
                                    <td style="text-align:center">{{$participante->fallidos}}</td>
                                    <td style="text-align:center">{{$participante->parciales}}</td>
                                    <td style="text-align:center">{{$participante->exactos}}</td>
                                    <td style="text-align:center">{{$participante->puntos}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- latest result end -->
@endsection