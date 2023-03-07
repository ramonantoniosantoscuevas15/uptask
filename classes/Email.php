<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email{
    protected $email;
    protected $nombre; 
    protected $token;

    public function __construct($email, $nombre, $token)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;

    }

    public function enviarConfirmacion(){
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '6be2ad8a03e986';
        $mail->Password = '38a681164f3ed0';

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('cuentas@uptask.com', 'uptask.com');
        $mail->Subject = 'Confirma tu Cuenta';

        $mail->isHtml(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html>';
        $contenido.= "<p><strong>Hola " .  $this->nombre  . " </strong> Has Creado tu cuenta en UpTask, solo debes confirmarla 
        en el siquiente enlace  </p>";
        $contenido .= "<p>Presiona aqui: <a href='http://localhost:3000/confirmar?token=" . $this->token .
         "'>Confirmar Cuenta</a> </p>";
        $contenido .= "<p>Si tu no creaste esta cuenta puedes ignorar este mensaje</p>";
        $contenido .= '</html>';

        $mail->Body = $contenido;

        //enviar email
        $mail->send();
    }

    public function enviarInstrucciones(){
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '6be2ad8a03e986';
        $mail->Password = '38a681164f3ed0';

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('cuentas@uptask.com', 'uptask.com');
        $mail->Subject = 'Reestablece tu Password';

        $mail->isHtml(TRUE);
        $mail->CharSet = 'UTF-8';
        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " .$this->nombre . " </strong> Parece que has olvidado tu password sigue el siguente 
         enlace para recuperarlo.</p>";
        $contenido .= "<p>Preciona Aqui: <a href='http://localhost:3000/reestablecer?token=" . $this->token ."'>Reestablecer password</a> </p>";
        $contenido .= "<p>Si tu no solicitaste esta cuenta, puedes ignorar el mensaje </p>";
        $contenido .= "</html>";

        $mail->Body = $contenido;

        //enviar el mail
        $mail->send();


    }

}