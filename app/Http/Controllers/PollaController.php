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

    public function hora(){ return date('Y-m-d H:i:s'); }
    public function token(){ return csrf_token(); }
    public function logout(){ Auth::logout(); }

    public function cron1(){

        $this->actualizar_estado_partidos();
        $this->actualizar_pronosticos();
        $this->actualizar_tabla();
        return "ok";

    }

    public function home(){

        if (!Auth::check()){ //NO ESTOY LOGUEADO

            return view('welcome');

        }else{

            $polla_id = Session::get('polla_id');

            if(DB::table('pollas')->where('id', Session::get('polla_id'))->count() == 0){

                Auth::logout();
                return view('welcome');

            }else{

               return redirect('/apuestas');

            }
        }

    }

    public function actualizar_estado_partidos(){

        $partidos = DB::table('partidos')->wherein('estadopartido_id', [1,2,5])->get();

        DB::beginTransaction();

        try{

            foreach($partidos as $aux){

                $fecha_partido = $aux->fecha_completa;
                $fecha_now = date('Y-m-d H:i:s'); 

                $differenceInSeconds = strtotime($fecha_partido) - strtotime($fecha_now);

                if($differenceInSeconds > 0){// EL PARTIDO AUN NO COMIENZA

                    DB::table('partidos')->where('id', $aux->id)->update(array('estadopartido_id'=> 1));

                }elseif($differenceInSeconds >= -6600){ //EL PARTIDO SE ESTA JUGANDO (110')

                    DB::table('partidos')->where('id', $aux->id)->update(array('estadopartido_id'=> 2));


                }else{ //EL PARTIDO YA SE JUGO

                    DB::table('partidos')->where('id', $aux->id)->update(array('estadopartido_id'=> 5));

                }

            }

            DB::commit();

            return response()->json(['data' => 'Partidos actualizados con exito','ok'=>true]);

        }catch(\Exception $e){

            DB::rollBack();

            return response()->json(['data'=>'', 'errors'=>'error: '.$e, 'mensaje' => 'Ha ocurrido un error, intente nuevamente más tarde.'], 409);
            
        }

    }

    public function actualizar_pronosticos(){

        $partidos = DB::table('partidos')->wherein('estadopartido_id', [5])->get();

        DB::beginTransaction();

        try{

            foreach($partidos as $aux){

                if($aux->res_local > $aux->res_visita)
                    $ganador_real = 'L';
                elseif($aux->res_local < $aux->res_visita)
                    $ganador_real = 'V';
                else
                    $ganador_real = 'E';

                DB::table('partidos')->where('id', $aux->id)->update(array('ganador'=> $ganador_real));

                $pronosticos = DB::table('pronosticos')->where('partido_id', $aux->id)->get();

                foreach($pronosticos as $aux2){

                    if($aux2->res_local > $aux2->res_visita)
                        $ganador = 'L';
                    elseif($aux2->res_local < $aux2->res_visita)
                        $ganador = 'V';
                    else
                        $ganador = 'E';

                    if($ganador_real == $ganador){ //LE ACHUNTO

                        if($aux->res_local == $aux2->res_local && $aux->res_visita == $aux2->res_visita) //EXACTO!!!
                            $puntos = 3;
                        else
                            $puntos = 1;

                    }else{ // NO LE ACHUNTO

                        $puntos = 0;

                    }

                    DB::table('pronosticos')->where('id', $aux2->id)->update(array('puntos'=> $puntos));

                }

                if($aux->resultado_actualizado == 1)
                    DB::table('partidos')->where('id', $aux->id)->update(array('estadopartido_id' => 3));

            }

            DB::commit();

            return response()->json(['data' => 'Pronosticos actualizados con exito','ok'=>true]);

        }catch(\Exception $e){

            DB::rollBack();

            return response()->json(['data'=>'', 'errors'=>'error: '.$e, 'mensaje' => 'Ha ocurrido un error, intente nuevamente más tarde.'], 409);
            
        }

    }

    public function actualizar_tabla(){

        $pollas = DB::table('pollas')->get();

        DB::beginTransaction();

        try{

            foreach($pollas as $aux){

                //VER SI EXISTE LA TABLA
                $tabla_id = DB::table('tablaposiciones')->where('polla_id', $aux->id)->value('id');

                if($tabla_id == '') //NO EXISTE LA TABLA, HAY Q CREARLA 
                    $tabla_id = DB::table('tablaposiciones')->insertGetId(array('polla_id' => $aux->id));

                DB::table('detalletablaposiciones')->where('tablaposicion_id', $tabla_id)->delete();

                DB::commit();

                $usuarios_polla = DB::table('polla_user')->where('polla_id', $aux->id)->get();

                foreach($usuarios_polla as $aux2){

                     $string =  'insert into detalletablaposiciones (tablaposicion_id, user_id, fallidos, parciales, exactos, puntos)
                                select '.$tabla_id.' as tabla_id, a.*, b.fallidos, c.parciales, d.exactos, e.puntos
                                from
                                (
                                select user_id
                                from polla_user
                                where polla_id = '.$aux->id.' and user_id = '.$aux2->user_id.'
                                ) a
                                left join
                                (
                                select user_id, puntos, count(*) as fallidos
                                from pronosticos
                                where polla_id = '.$aux->id.' and user_id = '.$aux2->user_id.'
                                and puntos = 0
                                group by user_id, puntos
                                ) b on a.user_id = b.user_id
                                left join
                                (
                                select user_id, puntos, count(*) as parciales
                                from pronosticos
                                where polla_id = '.$aux->id.' and user_id = '.$aux2->user_id.'
                                and puntos = 1
                                group by user_id, puntos
                                ) c on a.user_id = c.user_id
                                left join
                                (
                                select user_id, puntos, count(*) as exactos
                                from pronosticos
                                where polla_id = '.$aux->id.' and user_id = '.$aux2->user_id.'
                                and puntos = 3
                                group by user_id, puntos
                                ) d on a.user_id = d.user_id
                                left join
                                (
                                select user_id, sum(puntos) as puntos
                                from pronosticos
                                where polla_id = '.$aux->id.' and user_id = '.$aux2->user_id.'
                                group by user_id
                                ) e on a.user_id = e.user_id;';

                    DB::statement($string);

                }

            }

            DB::commit();

            return response()->json(['data' => 'Pronosticos actualizados con exito','ok'=>true]);

        }catch(\Exception $e){

            DB::rollBack();

            return response()->json(['data'=>'', 'errors'=>'error: '.$e, 'mensaje' => 'Ha ocurrido un error, intente nuevamente más tarde.'], 409);
            
        }

    }

    public function get_tabla(){

        if (!Auth::check())
            return response()->json(['data' => 'Usuario no esta logueado', 'ok'=>false, 'mensaje' => 'No ha iniciado sesión']);

        $tabla = DB::table('tablaposiciones')
                            ->join('detalletablaposiciones', 'detalletablaposiciones.tablaposicion_id', '=', 'tablaposiciones.id')
                            ->join('users', 'users.id', '=', 'detalletablaposiciones.user_id')
                            ->where('polla_id', Session::get('polla_id'))
                            ->groupby('detalletablaposiciones.puntos', 'desc')
                            ->get();

        return $tabla;

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
            return response()->json(['data' => $validator->errors(),'ok'=>false, 'mensaje' => 'Debe ingresar solo numeros para ingresar su pronostico']);
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

    public function get_partidos($user_id = NULL){

        if (!Auth::check())
            return response()->json(['data' => 'Usuario no esta logueado', 'ok'=>false]);

        if($user_id == NULL)
            $user_id = Auth::user()->id;

        $polla_id = Session::get('polla_id');
        $num_pollas = DB::table('polla_user')->where('user_id', $user_id)->count();

        if($num_pollas == 0){

            return response()->json(['mensaje' => 'No existen partidos', 'data' => '','ok'=>false]);

        }else{

            $pollas = DB::table('polla_user')->where('user_id', $user_id)->where('polla_id', $polla_id)->get();

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
                                          'local.logo as logo_local', 'visita.logo as logo_visita', 'partidos.res_local as res_local_real', 'partidos.res_visita as res_visita_real', 'estadopartidos.estado',
                                            'torneoequipolocal.grupo as grupo_local', 'torneoequipovisita.grupo as grupo_visita', 'partidos.tipopartido_id', 'tipopartidos.tipo as tipo_partido', 'partidos.estadopartido_id', 
                                            'partidos.tipofinal_id',  'partidos.res_local_penales', 'partidos.res_visita_penales', 'tipofinal.tipo_final', 'partidos.equipo_continua',
                                            db::raw('CASE WHEN partidos.tipopartido_id = 1 then CONCAT(tipopartidos.tipo, " - GRUPO ", torneoequipolocal.grupo) else tipopartidos.tipo end as titulo'), 'partidos.ganador'
                                        )
                                ->orderby('partidos.fecha_completa', 'asc')
                                ->get();

                foreach($partidos as $aux2){

                    $pronostico = DB::table('pronosticos')
                        ->where('user_id', $user_id)
                        ->where('partido_id', $aux2->partido_id)
                        ->where('polla_id', $polla_id)
                        ->get();

                    $aux2->pronostico = count($pronostico) > 0 ? 1 : 0 ;
                    $aux2->res_local = count($pronostico) > 0 ? $pronostico[0]->res_local : 0;
                    $aux2->res_visita = count($pronostico) > 0 ? $pronostico[0]->res_visita : 0;
                    $aux2->puntos = count($pronostico) > 0 ? $pronostico[0]->puntos : 0;

                    $resultado_apuesta = '';
                    if(count($pronostico) > 0){

                        if($pronostico[0]->puntos == 3)
                            $resultado_apuesta = 'EXACTO';
                        elseif($pronostico[0]->puntos == 1)
                            $resultado_apuesta = 'PARCIAL';
                        else
                            $resultado_apuesta = 'FALLO';

                    }
                    $aux2->resultado_apuesta = $resultado_apuesta;

                }

                $aux->partidos = $partidos;

            }

            return $pollas;
        }

    }

    public function login(Request $request){

        $existe_codigo = DB::table('polla_user')->where('codigouser', $request->codigo)->count();
        
        if($existe_codigo == 0)
            return view('welcome')->with('error','El còdigo ingresado no existe');

        //return response()->json(['data' => 'No existe el codigo', 'ok'=>false]);

        $codigo = $this->desencriptar($request->codigo);

        //return $codigo;

        $porciones = explode(".", $codigo);
        
        $polla_id = $porciones[0];
        $user_id = $porciones[1];

        //$value = Session::get('jefe');

        $request->session()->put('polla_id', $polla_id);

        Auth::loginUsingId($user_id);

        return redirect('/apuestas');

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

    public function tablaDemo() {
        $data ='[
  {
    "id": "637050e8546293547d1263aa",
    "nombre": "Hutchinson Dominguez",
    "fallidos": 1,
    "parciales": 4,
    "exactos": 16,
    "puntos": 52
  },
  {
    "id": "637050e8dad9896e44af03e0",
    "nombre": "Shaw Ayers",
    "fallidos": 1,
    "parciales": 8,
    "exactos": 12,
    "puntos": 44
  },
  {
    "id": "637050e86402c44b8dd663ef",
    "nombre": "Angelina Dotson",
    "fallidos": 4,
    "parciales": 5,
    "exactos": 12,
    "puntos": 41
  },
  {
    "id": "637050e8fbefba6cd3a117de",
    "nombre": "Horn Beach",
    "fallidos": 1,
    "parciales": 10,
    "exactos": 10,
    "puntos": 40
  },
  {
    "id": "637050e802574fe95f445a3d",
    "nombre": "Christensen Powers",
    "fallidos": 6,
    "parciales": 4,
    "exactos": 11,
    "puntos": 37
  },
  {
    "id": "637050e8654a2a66263d4953",
    "nombre": "Gaspar Sepúlveda",
    "fallidos": 5,
    "parciales": 6,
    "exactos": 10,
    "puntos": 36
  },
  {
    "id": "637050e880760629d79ecef2",
    "nombre": "Donna Sosa",
    "fallidos": 6,
    "parciales": 8,
    "exactos": 7,
    "puntos": 29
  },
  {
    "id": "637050e827aca3a24383816c",
    "nombre": "Hess Norman",
    "fallidos": 2,
    "parciales": 14,
    "exactos": 5,
    "puntos": 29
  },
  {
    "id": "637050e8aeb55d8478fdbb57",
    "nombre": "Rhonda Merrill",
    "fallidos": 11,
    "parciales": 1,
    "exactos": 9,
    "puntos": 28
  },
  {
    "id": "637050e844c95ddfbc59808c",
    "nombre": "Johnnie Bradford",
    "fallidos": 8,
    "parciales": 6,
    "exactos": 7,
    "puntos": 27
  },
  {
    "id": "637050e827c6871bca75e2f4",
    "nombre": "Mcdowell Frye",
    "fallidos": 1,
    "parciales": 17,
    "exactos": 3,
    "puntos": 26
  },
  {
    "id": "637050e8018b73a70dd1f054",
    "nombre": "Shelley Golden",
    "fallidos": 10,
    "parciales": 4,
    "exactos": 7,
    "puntos": 25
  },
  {
    "id": "637050e81b643f2277125e46",
    "nombre": "Carver Warren",
    "fallidos": 11,
    "parciales": 3,
    "exactos": 7,
    "puntos": 24
  },
  {
    "id": "637050e8d5c2ce724503c8d8",
    "nombre": "Jennifer Mcdaniel",
    "fallidos": 15,
    "parciales": 0,
    "exactos": 6,
    "puntos": 18
  },
  {
    "id": "637050e8be814c140192ac9a",
    "nombre": "Vera Landry",
    "fallidos": 12,
    "parciales": 5,
    "exactos": 4,
    "puntos": 17
  },
  {
    "id": "637050e8cb2c57bc64ddfeb4",
    "nombre": "Bernice Massey",
    "fallidos": 15,
    "parciales": 2,
    "exactos": 4,
    "puntos": 14
  },
  {
    "id": "637050e8cfbd9ebfa93794ff",
    "nombre": "Denise Walters",
    "fallidos": 13,
    "parciales": 5,
    "exactos": 3,
    "puntos": 14
  },
  {
    "id": "637050e899823a19ca309137",
    "nombre": "Lucy Parks",
    "fallidos": 12,
    "parciales": 7,
    "exactos": 2,
    "puntos": 13
  },
  {
    "id": "637050e8537f5bc09b074817",
    "nombre": "Cathleen Hodge",
    "fallidos": 10,
    "parciales": 10,
    "exactos": 1,
    "puntos": 13
  },
  {
    "id": "637050e80cf15761172be176",
    "nombre": "Huber Ochoa",
    "fallidos": 14,
    "parciales": 5,
    "exactos": 2,
    "puntos": 11
  },
  {
    "id": "637050e8aac272d65eb31a36",
    "nombre": "Landry Osborne",
    "fallidos": 17,
    "parciales": 1,
    "exactos": 3,
    "puntos": 10
  },
  {
    "id": "637050e8fe70536f57113ce0",
    "nombre": "Bryan Gamble",
    "fallidos": 13,
    "parciales": 7,
    "exactos": 1,
    "puntos": 10
  },
  {
    "id": "637050e8fce937f38fac7140",
    "nombre": "Petra Small",
    "fallidos": 14,
    "parciales": 6,
    "exactos": 1,
    "puntos": 9
  },
  {
    "id": "637050e873c1da2e000da49c",
    "nombre": "Carroll Lindsay",
    "fallidos": 12,
    "parciales": 9,
    "exactos": 0,
    "puntos": 9
  },
  {
    "id": "637050e892951d6ce09bd8c1",
    "nombre": "Adkins Nicholson",
    "fallidos": 15,
    "parciales": 6,
    "exactos": 0,
    "puntos": 6
  },
  {
    "id": "637050e826cfcedb6ffdbd71",
    "nombre": "Nieves Stafford",
    "fallidos": 19,
    "parciales": 1,
    "exactos": 1,
    "puntos": 4
  },
  {
    "id": "637050e84243bbd395804861",
    "nombre": "Angel Avila",
    "fallidos": 19,
    "parciales": 1,
    "exactos": 1,
    "puntos": 4
  },
  {
    "id": "637050e874f51b664d59416d",
    "nombre": "Margery Knowles",
    "fallidos": 20,
    "parciales": 0,
    "exactos": 1,
    "puntos": 3
  },
  {
    "id": "637050e8ac7e50e1f909bd49",
    "nombre": "Cline Barrera",
    "fallidos": 18,
    "parciales": 3,
    "exactos": 0,
    "puntos": 3
  }
]';
return (object)json_decode($data);
    }
}
