<?php
class users_ctrl
{
   public function login_users($f3)
   {
       $ci = $f3->get('PARAMS.ci');
       $clave = $f3->get('PARAMS.clave');
       $cadenaSql = "";
       $cadenaSql .= "SELECT * ";
       $cadenaSql .= "FROM usuario ";
       $cadenaSql .= "WHERE usr_correo = '$ci' AND usr_contraseña = '$clave'";
       $result = $f3->DB->exec($cadenaSql);
       $msg = "";
       if ($result) {
           $msg = "Datos recuperados con éxito";
       } else {
           $msg = "No existen datos";
       }
       echo json_encode([
           'mensaje' => $msg,
           'cant' => count($result),
           'data' => $result
       ]);
   }
}

