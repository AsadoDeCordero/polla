<?php 
    $partidos = App\Http\Controllers\PollaController::get_partidos()[0]->partidos;
    //dd($partidos);
?>
<!DOCTYPE html>
<html lang="zxx">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title> {{config('app.name')}}</title>
        <!-- favicon -->
        <link rel="shortcut icon" href="favicon.svg" type="image/x-icon">
        <!-- bootstrap -->
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <!-- fontawesome icon  -->
        <link rel="stylesheet" href="assets/css/fontawesome.min.css">
        <!-- flaticon css -->
        <link rel="stylesheet" href="assets/fonts/flaticon.css">
        <!-- animate.css -->
        <link rel="stylesheet" href="assets/css/animate.css">
        <!-- Owl Carousel -->
        <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
        <!-- magnific popup -->
        <link rel="stylesheet" href="assets/css/magnific-popup.css">
        <link rel="stylesheet" href="assets/css/odometer.min.css">
        <!-- stylesheet -->
        <link rel="stylesheet" href="assets/css/style.css">
        <!-- responsive -->
        <link rel="stylesheet" href="assets/css/responsive.css">
        <link rel="stylesheet" href="assets/css/latest-result-responsive.css">
        <style type="text/css">
            .textoPrediccion{
                color: black!important;
                border: 0px!important;
                height: 100%!important;
                text-align: center;
            }
            .latest-result{
                padding: 55px 0!important;
            }
        </style>
    </head>

    <body>        
        <!-- header begin -->
        <div class="header">
            <div id="navbar" class="header-bottom" style="position: static!important;">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-3 col-lg-3 d-xl-flex d-lg-flex d-block align-items-center">
                            <div class="row">
                                <div class="col-xl-12 col-lg-12 col-6 d-xl-block d-lg-block d-flex align-items-center">
                                    <div class="logo">
                                        <a href="{{url('/')}}">
                                            <img src="favicon.svg" alt="logo" style="height: 35px;">
                                        </a>
                                    </div>
                                </div>
                                <div class="col-6 d-xl-none d-lg-none d-block">
                                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                                        <i class="fas fa-bars"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-9 col-lg-9">
                            <div class="mainmenu">
                                <nav class="navbar navbar-expand-lg">
                                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                                        <ul class="navbar-nav ml-auto">
                                            <li class="nav-item">
                                                <a class="nav-link" href="about.html">Partidos</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="statics.html">Resultados</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="promotions.html">Tabla de posiciones</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{url('/')}}/salir">Salir</a>
                                            </li>
                                        </ul>
                                    </div>
                                  </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- header end -->

        <!-- breadcrumb begin -->
        <div class="breadcrumb-bettix latest-result-page">
            <div class="container">
                <div class="row">
                    <div class="col-xl-7 col-lg-7">
                        <div class="breadcrumb-content">
                            <h2>Partidos</h2>
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
                                            <li><input type="text" class=" textoPrediccion form-control" id="res_local{{$partido->partido_id}}"></li>
                                            <li><input type="text" class=" textoPrediccion form-control" id="res_visita{{$partido->partido_id}}"></li>
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
                                <a onclick="enviar('{{$partido->partido_id}}')" class="btn btn-success" style="width:100%;color:white">Enviar pronóstico</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <!-- latest result end -->

        <!-- notes begin -->
        <div class="notes">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-10 col-lg-10">
                        {{config('app.name')}} tiene todos los derechos reservados y la wea.
                    </div>
                </div>
            </div>
        </div>
        <!-- notes end -->
        
        <!-- jquery -->
        <!-- <script src="assets/js/jquery.js"></script> -->
        <script src="assets/js/jquery-3.4.1.min.js"></script>
        <!-- bootstrap -->
        <script src="assets/js/bootstrap.min.js"></script>
        <!-- owl carousel -->
        <script src="assets/js/owl.carousel.js"></script>
        <!-- magnific popup -->
        <script src="assets/js/jquery.magnific-popup.js"></script>
        <!-- filterizr js -->
        <script src="assets/js/jquery.filterizr.min.js"></script>
        <!-- wow js-->
        <script src="assets/js/wow.min.js"></script>
        <!-- clock js -->
        <script src="assets/js/clock.min.js"></script>
        <script src="assets/js/jquery.appear.min.js"></script>
        <script src="assets/js/odometer.min.js"></script>
        <!-- main -->
        <script src="assets/js/main.js"></script>
        <script type="text/javascript">
            function enviar(id){
                var reslocal = $("#res_local"+id).val();
                var resvisita = $("#res_visita"+id).val();

                $.ajax({
                     type: "POST",
                     url: "{{url('/')}}/pronostico",
                     data: {partido_id : id, reslocal:reslocal, resvisita:resvisita},
                     success: function(agente) {
                    if(agente.ok){
                        alert("Apuesta ingresada");
                      } else {
                        alert("Error de apuesta")
                      }
                    }, 
                    error: function(XMLHttpRequest, textStatus, errorThrown) { 
                      alert("Error de conexión al servidor: "+errorThrown);
                     }   
                });
            }
        </script>
    </body>
</html>