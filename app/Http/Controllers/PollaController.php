<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use DB;
use Str;
use Hash;
use \App\Models\User;
use Mail;
use Crypt;
use Auth;
use Session;

class PollaController extends Controller
{

    public function token(){ return csrf_token(); }
    public function logout(){ Auth::logout(); }

    public function home(){

        if (!Auth::check()){ //NO ESTOY LOGUEADO

            return view('welcome');

        }else{

            $polla_id = Session::get('polla_id');

            if(DB::table('pollas')->where('id', Session::get('polla_id'))->count() == 0){

                Auth::logout();
                return view('welcome');

            }else{

                return view('panel.partidos');

            }

        }

         

    }

    public function pronostico(Request $request){

        if (!Auth::check())
            return response()->json(['data' => 'Usuario no esta logueado', 'ok'=>false, 'mensaje' => 'No ha iniciado sesión']);

        $validator = Validator::make($request->all(), [
            'partido_id'  => 'required|exists:partidos,id',
            'reslocal'  => 'required|numeric',
            'resvisita'  => 'required|numeric',
        ],$messages = [
            'partido_id.required' => 'El partido id es requerido',
            'reslocal.required' => 'El resultado del local es requerido',
            'resvisita.required' => 'El resultado de la visita es requerido',
        ]);

        if ($validator->fails()) {
            return response()->json(['data' => $validator->errors(),'ok'=>false, 'mensaje' => 'Error de validación']);
            return back()->withErrors($validator)->withInput();
        }

        if(DB::table('pollas')->where('id', Session::get('polla_id'))->count() == 0)
            return response()->json(['data' => 'Falta el id de la polla', 'ok'=>false, 'mensaje' => 'La polla no existe']);




        //VALIDAR QUE FALTEN MAS DE 5' PARA ENVIAR EL PRONOSTICO
            $fecha_partido = DB::table('partidos')->where('id', $request->partido_id)->value('fecha_completa');
            $fecha_now = date('Y-m-d H:i:s'); 

            $differenceInSeconds = strtotime($fecha_partido) - strtotime($fecha_now);

            //echo $differenceInSeconds; 

            if($differenceInSeconds <= 300 ){ //QUEDAN MENOS DE 5' PARA QUE EMPIECE EL PARTIDO

                if($differenceInSeconds <= 0)
                    $mje = "El partido ya comenzó,";
                else
                    $mje = 'El partido esta a menos de 5\' ( '.$differenceInSeconds.' segundos ) de empezar,';

                return response()->json(['data' => $validator->errors(),'ok'=>false, 'mensaje' => $mje.' ya no puede ingresar este pronostico']);

            }

            /*
            $fecha1 = date_create($fecha_partido);
            $fecha2 = date_create($fecha_now);

            $dif = date_diff($fecha2, $fecha1);
            dd($dif);
            $paso_el_partido = $dif->invert; //1-0
            $minutos = $dif->i;

            return $minutos;

            return $paso_el_partido;
            */
            

        DB::beginTransaction();

        try{

            $pronostico = DB::table('pronosticos')
                        ->where('user_id', Auth::user()->id)
                        ->where('partido_id', $request->partido_id)
                        ->where('polla_id', Session::get('polla_id'))
                        ->get();

            if(count($pronostico) > 0){ //EXISTE EL PRONOSTICO, UPDATEAR

                DB::table('pronosticos')
                        ->where('id', $pronostico[0]->id)
                        ->update(array(     'res_local'=> $request->reslocal,
                                            'res_visita'=> $request->resvisita,
                                        )
                        );

            }else{ //NO EXISTE EL PRONOSTICO, CREAR

                DB::table('pronosticos')->insert(
                  array(    'partido_id' => $request->partido_id,
                            'user_id' => Auth::user()->id, 
                            'res_local'=> $request->reslocal,
                            'res_visita'=> $request->resvisita,
                            'polla_id'=> Session::get('polla_id')
                        )
                  );

            }

            DB::commit();
            return response()->json(['data' => 'Pronostico enviado con exito','ok'=>true, 'mensaje' => 'Pronóstico enviado con éxito']);
            //return back()->with('success','Usuario creado con éxito');

        } catch (Throwable $e) {

            report($e);
            DB::rollback();
            return response()->json(['data' => ''.$e,'ok'=>false, 'mensaje' => 'Ha ocurrido un error inesperado, intente nuevamente más tarde']);

        }


    }

    public function get_partidos(){

        if (!Auth::check())
            return response()->json(['data' => 'Usuario no esta logueado', 'ok'=>false]);

        $polla_id = Session::get('polla_id');
        $num_pollas = DB::table('polla_user')->where('user_id', Auth::user()->id)->count();

        if($num_pollas == 0){

            return response()->json(['mensaje' => 'No existen partidos', 'data' => '','ok'=>false]);

        }else{

            $pollas = DB::table('polla_user')->where('user_id', Auth::user()->id)->where('polla_id', $polla_id)->get();

            foreach($pollas as $aux){

                $torneo_id = DB::table('pollas')->where('id', $aux->polla_id)->value('torneo_id');

                $partidos = DB::table('partidos')
                                ->join('equipos as local', 'local.id', '=', 'partidos.local_id')
                                ->join('equipos as visita', 'visita.id', '=', 'partidos.visita_id')
                                ->join('estadopartidos', 'estadopartidos.id', '=', 'partidos.estadopartido_id')
                                ->join('tipopartidos', 'tipopartidos.id', '=', 'partidos.tipopartido_id')
                                ->join('tipofinal', 'tipofinal.id', '=', 'partidos.tipofinal_id')
                                ->join('torneo_equipo as torneoequipolocal', function ($join) {
                                            $join->on('torneoequipolocal.equipo_id', '=', 'local.id')
                                                 ->on('torneoequipolocal.torneo_id', '=', 'partidos.torneo_id');
                                        })
                                ->join('torneo_equipo as torneoequipovisita', function ($join) {
                                            $join->on('torneoequipovisita.equipo_id', '=', 'visita.id')
                                                 ->on('torneoequipovisita.torneo_id', '=', 'partidos.torneo_id');
                                        })
                                ->where('partidos.torneo_id', $torneo_id)
                                ->select('local.nombre as local', 'visita.nombre as visita', 'partidos.id as partido_id', 'local.id as local_id', 'visita.id as visita_id', 'partidos.fecha', 'partidos.hora', 'partidos.fecha_completa',
                                          'local.logo as logo_local', 'visita.logo as logo_visita', 'partidos.res_local', 'partidos.res_visita', 'estadopartidos.estado',
                                            'torneoequipolocal.grupo as grupo_local', 'torneoequipovisita.grupo as grupo_visita', 'partidos.tipopartido_id', 'tipopartidos.tipo as tipo_partido', 'partidos.estadopartido_id', 
                                            'partidos.tipofinal_id',  'partidos.res_local_penales', 'partidos.res_visita_penales', 'tipofinal.tipo_final', 
                                            db::raw('CASE WHEN partidos.tipopartido_id = 1 then CONCAT(tipopartidos.tipo, " - GRUPO ", torneoequipolocal.grupo) else tipopartidos.tipo end as titulo')
                                        )
                                ->get();

                foreach($partidos as $aux2){

                    $pronostico = DB::table('pronosticos')
                        ->where('user_id', Auth::user()->id)
                        ->where('partido_id', $aux2->partido_id)
                        ->where('polla_id', $polla_id)
                        ->get();

                    $aux2->pronostico = count($pronostico) > 0 ? 1 : 0 ;
                    $aux2->res_local = count($pronostico) > 0 ? $pronostico[0]->res_local : 0;
                    $aux2->res_visita = count($pronostico) > 0 ? $pronostico[0]->res_visita : 0;

                }

                $aux->partidos = $partidos;

            }

            return $pollas;
        }

    }

    public function login(Request $request){

        $existe_codigo = DB::table('polla_user')->where('codigouser', $request->codigo)->count();
        
        if($existe_codigo == 0)
            return response()->json(['data' => 'No existe el codigo', 'ok'=>false]);

        $codigo = $this->desencriptar($request->codigo);

        //return $codigo;

        $porciones = explode(".", $codigo);
        
        $polla_id = $porciones[0];
        $user_id = $porciones[1];

        //$value = Session::get('jefe');

        $request->session()->put('polla_id', $polla_id);

        Auth::loginUsingId($user_id);

        return view('panel.partidos');

    }

    public function crear_usuario(Request $request){

        $validator = Validator::make($request->all(), [
            'nombre'     => 'required',
            'email'  => 'required|email|unique:users,email',
        ],$messages = [
            'nombre.required' => 'El nombre es requerido.',
            'email.required' => 'El email es requerido.',
            'email.unique' => 'El email ingresado ya existe en nuestros registros',
        ]);

        if ($validator->fails()) {
            return response()->json(['data' => $validator->errors(),'ok'=>false]);
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try{

            $rndm = strtolower(Str::random(8));
            $pass =  Hash::make($rndm);

            $user = new User;

            $user->nombre = $request->nombre;
            $user->email = $request->email;
            $user->password = $pass;

            $user->save();

            //$this->enviarEmail($rndm,$user);

            DB::commit();
            return response()->json(['data' => 'Usuario creado con éxito','ok'=>true]);
            //return back()->with('success','Usuario creado con éxito');

        } catch (Throwable $e) {

            report($e);
            DB::rollback();
            return response()->json(['data' => ''.$e,'ok'=>false]);

        }

    }

    public function crear_usuario_polla(Request $request, $codigo){

        $validator = Validator::make($request->all(), [
            'nombre'     => 'required',
            'email'  => 'required|email',
        ],$messages = [
            'nombre.required' => 'El nombre es requerido.',
            'email.required' => 'El email es requerido.',
        ]);

        if ($validator->fails()) {
            return response()->json(['data' => $validator->errors(),'ok'=>false]);
            return back()->withErrors($validator)->withInput();
        }


        $existe_polla = DB::table('pollas')->where('codigo', $codigo)->count();

        if($existe_polla == 0)
            return response()->json(['data' => 'No existe la polla', 'ok'=>false]);

        $existe_user = DB::table('users')->where('email', $request->email)->count();

        DB::beginTransaction();

        try{

            if($existe_user == 0){ //USUARIO NO EXISTE, HAY QUE CREARLO

                $rndm = strtolower(Str::random(8));
                $pass =  Hash::make($rndm);

                $user = new User;

                $user->nombre = $request->nombre;
                $user->email = $request->email;
                $user->password = $pass;

                $user->save();

                $user_id = $user->id;

            }else{ //USUARIO EXISTE

                $user_id = DB::table('users')->where('email', $request->email)->value('id');

                $existe_usuario_en_polla = DB::table('polla_user')
                                            ->where('polla_id', DB::table('pollas')->where('codigo', $codigo)->value('id'))
                                            ->where('user_id', $user_id)
                                            ->count();

                if($existe_usuario_en_polla > 0)
                    return response()->json(['data' => 'Usurio ya existe en polla', 'ok'=>false]);

            }

            $codigo_cryp = $this->encriptar(DB::table('pollas')->where('codigo', $codigo)->value('id').'.'.$user_id); //ID_POLLA.ID_USER
            //$msg_dec = Crypt::decrypt($msg_cryp);

             DB::table('polla_user')->insert(
                  array(    'polla_id' => DB::table('pollas')->where('codigo', $codigo)->value('id'),
                            'user_id' => $user_id, 
                            'codigouser'=> $codigo_cryp
                        )
                  );

             DB::commit();

             $this->enviarEmail($user_id, $codigo_cryp);

            return response()->json(['data' => 'Usuario creado con éxito','ok'=>true]);

        } catch (Throwable $e) {

            report($e);
            DB::rollback();
            return response()->json(['data' => ''.$e6¡,'ok'=>false]);

        }

    }

    public function enviarEmail($user_id, $codigo_cryp){

        //OJO, LOS CORREOS VAN COMO ARRAY
        $correo = DB::table('users')->where('id', $user_id)->value('email');

        $codigo = $this->desencriptar($codigo_cryp);

        $porciones = explode(".", $codigo);
        
        $polla_id = $porciones[0];
        $user_id = $porciones[1];

        $array = array();
        $array[] = array(   'nombre' => DB::table('users')->where('id', $user_id)->value('nombre'),
                            'codigo' => $codigo_cryp,
                            'nombre_polla' =>  DB::table('pollas')->where('id', $polla_id)->value('nombre'),
                            'texto' => 'ESTE ES UN TEXTO DE PRUEBA DE ENVÌO DEL CORREO',
                            'link' => url('/'),
        );

        $subject = 'Bienvenido a la Polla!!!';
        $array_final = $array;

        $data = ['data' => $array_final];

        Mail::send('emails.plantilla', $data, function($message) use ($correo, $subject)
            {
                $message->from('spielupchile@gmail.com'); //CAMBIAR POR EL CORREO CONTACTO???
                $message->subject($subject); //ASUNTO
                $message->to($correo, 'SPIELUP');
                //$message->cc($correo2, 'SPIELUP');
                
            });

        return "OK";

    }

    public function encriptar($simple_string){
  
        // Display the original string
        //echo "Original String: " . $simple_string;
          
        // Store the cipher method
        $ciphering = "AES-128-CTR";
          
        // Use OpenSSl Encryption method
        $iv_length = openssl_cipher_iv_length($ciphering);
        $options = 0;
          
        // Non-NULL Initialization Vector for encryption
        $encryption_iv = '1234567891011121';
          
        // Store the encryption key
        $encryption_key = "GeeksforGeeks";
          
        // Use openssl_encrypt() function to encrypt the data
        $encryption = openssl_encrypt($simple_string, $ciphering,
                    $encryption_key, $options, $encryption_iv);
          
        // Display the encrypted string
        //echo "Encrypted String: " . $encryption . "\n";
        return $encryption;
          
        // Non-NULL Initialization Vector for decryption
        $decryption_iv = '1234567891011121';
          
        // Store the decryption key
        $decryption_key = "GeeksforGeeks";
          
        // Use openssl_decrypt() function to decrypt the data
        $decryption=openssl_decrypt ($encryption, $ciphering, 
                $decryption_key, $options, $decryption_iv);
          
        // Display the decrypted string
        echo "Decrypted String: " . $decryption;


    }

    public function desencriptar($encryption){

        $ciphering = "AES-128-CTR";
        // Non-NULL Initialization Vector for decryption
        $decryption_iv = '1234567891011121';
          $options = 0;
        // Store the decryption key
        $decryption_key = "GeeksforGeeks";
          
        // Use openssl_decrypt() function to decrypt the data
        $decryption=openssl_decrypt ($encryption, $ciphering, 
                $decryption_key, $options, $decryption_iv);
        return $decryption;

        // Display the decrypted string
        echo "Decrypted String: " . $decryption;


    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
