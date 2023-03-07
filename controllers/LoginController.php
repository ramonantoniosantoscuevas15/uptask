<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController{
    public static function login(Router $router){
        $alertas = [];
        

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarLogin();
            if(empty($alertas)){
                // verificar que el usuario exista
                $usuario = Usuario::where('email', $usuario->email);
                if(!$usuario || !$usuario->confirmado){
                    Usuario::setAlerta('error','El Usuario no Existe o no esta Confirmado');
                }else{
                    //el usuario existe
                    if(password_verify($_POST['password'], $usuario->password)){
                        //inicial la sesion
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        //redireccionar
                        header('Location: /dashboard');

                    }else{
                        Usuario::setAlerta('error','Password Incorrecto');
                    }
                }
               
            }

        }
        $alertas = Usuario::getAlertas();
        //render a la vista
        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesión',
            'alertas' => $alertas
        ]);
    }

    public static function logout(){
        session_start();
        $_SESSION = [];
        header('Location: /');
    }

    public static function crear(Router $router){

        $usuario = new Usuario;
        $alertas = [];


        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario->sincronizar($_POST);
           

            $alertas = $usuario->validarNuevaCuenta();
           if(empty($alertas)){
            $existeUsuario = Usuario::where('email', $usuario->email);
            
            if($existeUsuario){
                Usuario::setAlerta('error','El Usuario ya esta Registrado');
                $alertas = Usuario::getAlertas();
            }else{
                //hashear el password
                $usuario->hashPassword();

                //eliminar password2
                unset($usuario->password2);

                //general el token
                $usuario->crearToken();
               
                //crear un nuevo usuario
                $resultado =  $usuario->guardar();

                //enviar email
                $email = new Email($usuario->email,$usuario->nombre,$usuario->token);

                $email->enviarConfirmacion();

                if($resultado){
                    header('Location: /mensaje');
                }

            }
           }

            
            
        }
        //render a la vista
        $router->render('auth/crear', [
            'titulo' => 'Crea tu Cuenta en UpTask',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function olvide(Router $router){
        $alertas=[];
        

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if(empty($alertas)){
                //Buscar el usuario
                $usuario = Usuario::where('email', $usuario->email);

                if($usuario && $usuario->confirmado){
                   //generar un nuevo token
                   $usuario->crearToken();
                   unset($usuario->password2);

                   //actualizar el usuario
                   $usuario->guardar();
                   
                   //enviar el email
                   $email = new Email($usuario->email,$usuario->nombre,$usuario->token);
                   $email->enviarInstrucciones();

                   

                   //imprimir la alerta
                   Usuario::setAlerta('exito','Hemos enviado las instrucciones a tu Email');
                   

                   
                }else{
                    Usuario::setAlerta('error','El usuario no existe o no esta confirmado');
                    
                }
            }

            
        }
        $alertas = Usuario::getAlertas();
        //render a la vista
        $router->render('auth/olvide', [
            'titulo' => 'Olvide mi Password',
            'alertas' => $alertas
        ]);
    }

    public static function reestablecer(Router $router){
        $token = s($_GET['token']);
        $mostrar = true;
        
        if(!$token) header('Location: /');

        //identificar el usuario con este token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            Usuario::setAlerta('error', 'Token no Valido');
            $mostrar = false;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            //añadir el nuevo password
            $usuario->sincronizar($_POST);
            //validar el password
           $alertas =  $usuario->validarPassword();
            
            if(empty($alertas)){
                //hashear el nuevo password
                $usuario->hashPassword();

                //eliminar el token
                $usuario->token = null;

                //guardar el usuario en la BD
                $resultado  = $usuario->guardar();

                //redireccionar
                if($resultado){
                    header('Location: /');
                }

                
            }
            
        }
        $alertas = Usuario::getAlertas();
        //render a la vista
        $router->render('auth/reestablecer', [
            'titulo' => 'Reestablecer Password',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }

    public static function mensaje(Router $router){
        //render a la vista
        $router->render('auth/mensaje', [
            'titulo' => 'Cuenta Creada Exitosamente'
        ]);

        
    }

    public static function confirmar(Router $router){
        $token = s($_GET['token']);
        
        if(!$token) header('Location: /');

        //encontrar al usuario con este token
        $usuario = Usuario::where('token', $token);
        if(empty($usuario)){
            //no se encontro un usuario con ese token
            Usuario::setAlerta('error','Token no valido');
        }else{
            // confirmar la cuenta
            $usuario->confirmado = 1;
            $usuario->token = null;
            unset($usuario->password2);
            // guardar en la BD
            $usuario->guardar();

            Usuario::setAlerta('exito','Cuenta Comprobada Correctamente');

        }

        $alertas = Usuario::getAlertas();
         //render a la vista
         $router->render('auth/confirmar', [
            'titulo' => 'Confirma tu Cuenta UpTask',
            'alertas' => $alertas
        ]);

        
    }
}