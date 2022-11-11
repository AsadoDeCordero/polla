<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title></title>
      <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
      <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
      <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
      <style type="text/css">
         body {
         margin: 0;
         padding: 0;
         background: url("{{url('/')}}/img/fondo.jpg");
         background-size: cover;
         height: 100vh;
         }
         #login .container #login-row #login-column #login-box {
         margin-top: 120px;
         max-width: 600px;
         border: 1px solid #9C9C9C;
         background-color: #eaeaead1;
         }
         #login .container #login-row #login-column #login-box #login-form {
         padding: 20px;
         }
         #login .container #login-row #login-column #login-box #login-form #register-link {
         margin-top: -85px;
         }
      </style>
   </head>
   <body>
      <div id="login">
         <div class="container">
            <div id="login-row" class="row justify-content-center align-items-center">
               <div id="login-column" class="col-md-6">
                  <div id="login-box" class="col-md-12">
                     <form id="login-form" class="form" action="{{url('/')}}/login" method="post">
                        {{csrf_field()}}
                        <h3 class="text-center text-info" style="color:black!important">Bienvenido a {{config('app.name')}}</h3>
                        <div class="form-group">
                           <label for="codigo" class="text-info" style="color:black!important">Código:</label><br>
                           <input type="text" name="codigo" id="codigo" class="form-control">
                        </div>
                        <div class="form-group" style="text-align: right;">
                           <input type="submit" name="submit" class="btn btn-primary btn-md" value="Entrar">
                        </div>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </body>
</html>