<?php

namespace Model;

class Usuario extends ActiveRecord{
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'email', 'password', 'token', 'confirmado'];
    
    public $id;
    public $nombre;
    public $email;
    public $password;
    public $password2;
    public $password_actual;
    public $password_nuevo;
    public $token;
    public $confirmado;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->password_actual = $args['password_actual'] ?? '';
        $this->password_nuevo = $args['password_nuevo'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
        
    }
    //validar el login de usuarios
    public function validarLogin(){
        if(!$this->email){
            self::$alertas['error'][] = 'El Email del Usuario es Obligatorio';
        }
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'Email no Valido';
        }
        if(!$this->password){
            self::$alertas['error'][] = 'El Password no Puede ir Vacio';
        }
        return self::$alertas;

    }

    //validacion para cuentas nuevas
    public function validarNuevaCuenta(){
        if(!$this->nombre){
            self::$alertas['error'][] = 'El Nombre del Usuario es Obligatorio';
        }
        if(!$this->email){
            self::$alertas['error'][] = 'El Email del Usuario es Obligatorio';
        }
        if(!$this->password){
            self::$alertas['error'][] = 'El Password no Puede ir Vacio';
        }
        if(strlen( $this->password) < 6){
            self::$alertas['error'][] = 'El Password debe Contener al Menos 6 caracteres';
        }
        if($this->password !== $this->password2){
            self::$alertas['error'][] = 'Los Passwords son Diferentes';
        }
        return self::$alertas;
    }
    // valida el email
    public function validarEmail(){
        if(!$this->email){
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'Email no Valido';
        }
        return self::$alertas;
    }
    //valida el password
    public function validarPassword(){
        if(!$this->password){
            self::$alertas['error'][] = 'El Password no Puede ir Vacio';
        }
        if(strlen( $this->password) < 6){
            self::$alertas['error'][] = 'El Password debe Contener al Menos 6 caracteres';
        }
        
        return self::$alertas;
    }
    public function validar_perfil(){
        if(!$this->nombre){
            self::$alertas['error'][] = 'El Nombre es Obligatorio';
        }
        if(!$this->email){
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }
        return self::$alertas;
    }
    public  function nuevo_password() : array{
        if(!$this->password_actual){
            self::$alertas['error'][] = 'El Password Actual no debe ir vacio';
        }
        if(!$this->password_nuevo){
            self::$alertas['error'][] = 'El Password Nuevo no debe ir vacio';
        }
        if(strlen($this->password_nuevo) < 6){
            self::$alertas['error'][] = 'El Password debe tener al menos 6 caracteres';
        }
        return self::$alertas;
    }
    // comprobar Password
    public function comprobar_password() : bool{
        return password_verify($this->password_actual, $this->password);
    }


    //hashea el password
    public function hashPassword() : void{
        $this->password  = password_hash($this->password, PASSWORD_BCRYPT);

    }

    //general un token
    public function crearToken() : void{
        $this->token = uniqid();
    }
}