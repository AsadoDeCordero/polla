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
            .latest-result .single-match .part-team .single-team .logo span.win {
                background-color: #259d2e!important;
                margin-bottom: 5px;
            }
            .latest-result .single-match .part-team .single-team .logo span.win:after {
                background-color: #259d2e!important;
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
                                                <a class="nav-link" href="{{url('/')}}/apuestas">Apuestas</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{url('/')}}/resultados">Resultados</a>
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

        @yield('body')

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