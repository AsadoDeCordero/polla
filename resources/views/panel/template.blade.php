<!DOCTYPE html>
<html lang="zxx">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title> {{config('app.name')}}</title>
        <!-- favicon -->
        <link rel="shortcut icon" href="{{url('/')}}/favicon.svg" type="image/x-icon">
        <!-- bootstrap -->
        <link rel="stylesheet" href="{{url('/')}}/assets/css/bootstrap.min.css">
        <!-- fontawesome icon  -->
        <link rel="stylesheet" href="{{url('/')}}/assets/css/fontawesome.min.css">
        <!-- flaticon css -->
        <link rel="stylesheet" href="{{url('/')}}/assets/fonts/flaticon.css">
        <!-- animate.css -->
        <link rel="stylesheet" href="{{url('/')}}/assets/css/animate.css">
        <!-- Owl Carousel -->
        <link rel="stylesheet" href="{{url('/')}}/assets/css/owl.carousel.min.css">
        <!-- magnific popup -->
        <link rel="stylesheet" href="{{url('/')}}/assets/css/magnific-popup.css">
        <link rel="stylesheet" href="{{url('/')}}/assets/css/odometer.min.css">
        <!-- stylesheet -->
        <link rel="stylesheet" href="{{url('/')}}/assets/css/style.css">
        <!-- responsive -->
        <link rel="stylesheet" href="{{url('/')}}/assets/css/responsive.css">
        <link rel="stylesheet" href="{{url('/')}}/assets/css/latest-result-responsive.css">
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
            .latest-result .single-match .part-team .single-team .logo span.win {
                background-color: #259d2e!important;
                margin-bottom: 5px;
            }
            .latest-result .single-match .part-team .single-team .logo span.win:after {
                background-color: #259d2e!important;
            }
            .table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
              background-color: #d9ebff;
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
                                                <a class="nav-link">Hola {{Auth::user()->nombre}}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="modal-37323" href="#modal-container-37323" role="button" class="btn" data-toggle="modal">Reglas</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{url('/')}}/apuestas">Apuestas</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{url('/')}}/resultados">Resultados</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{url('/')}}/tabla">Tabla de posiciones</a>
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
        <div class="modal fade" id="modal-container-37323" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="myModalLabel">
                            Reglas
                        </h5> 
                        <button type="button" class="close" data-dismiss="modal">
                                <span aria-hidden="true">×</span>
                            </button>
                    </div>
                    <div class="modal-body">
                        <?php
                                DB::table('pollas')->where('id', Session::get('polla_id'))->value('descripcion');
                        ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Cerrar
                        </button>
                    </div>
                </div>
                    
            </div>
                
        </div>
        @yield('body')

        <!-- notes begin -->
        <div class="notes">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-10 col-lg-10">
                        Plataforma desarrollada por <a href="https://www.spielup.cl" style="color:white;">Spielup Spa. </a>Todos los derechos reservados.
                    </div>
                </div>
            </div>
        </div>
        <!-- notes end -->
        
        <!-- jquery -->
        <!-- <script src="{{url('/')}}/assets/js/jquery.js"></script> -->
        <script src="{{url('/')}}/assets/js/jquery-3.4.1.min.js"></script>
        <!-- bootstrap -->
        <script src="{{url('/')}}/assets/js/bootstrap.min.js"></script>
        <!-- owl carousel -->
        <script src="{{url('/')}}/assets/js/owl.carousel.js"></script>
        <!-- magnific popup -->
        <script src="{{url('/')}}/assets/js/jquery.magnific-popup.js"></script>
        <!-- filterizr js -->
        <script src="{{url('/')}}/assets/js/jquery.filterizr.min.js"></script>
        <!-- wow js-->
        <script src="{{url('/')}}/assets/js/wow.min.js"></script>
        <!-- clock js -->
        <script src="{{url('/')}}/assets/js/clock.min.js"></script>
        <script src="{{url('/')}}/assets/js/jquery.appear.min.js"></script>
        <script src="{{url('/')}}/assets/js/odometer.min.js"></script>
        <!-- main -->
        <script src="{{url('/')}}/assets/js/main.js"></script>
        <script type="text/javascript">
            function enviar(id){
                var reslocal = $("#res_local"+id).val();
                var resvisita = $("#res_visita"+id).val();
                var token = "{{csrf_token()}}";

                $.ajax({
                     type: "POST",
                     url: "{{url('/')}}/pronostico",
                     data: {partido_id : id, reslocal:reslocal, resvisita:resvisita, _token:token},
                     success: function(agente) {
                    if(agente.ok){
                        $("#boton_"+id).text("Modificar pronóstico");
                        $('#boton_'+id).removeClass('btn-warning').removeClass('btn-success').css('background',"#28a745");
                        window.setTimeout(function(){$('#boton_'+id).css('background','#ffc107').addClass("btn-warning")}, 500);
                        
                      } else {
                        alert(agente.mensaje)
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