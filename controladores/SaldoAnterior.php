<?php
class SaldoAnterior
{
    public $M_SaldoAnterior = null;

    public function __construct()
    {
        $this->M_SaldoAnterior = new M_SaldoAnterior();
    }

    public function obtenerSaldoAnterior($f3)
    {
        $saldoAnterior = 0;
        $tsa_semana = $f3->get('PARAMS.tsa_semana');
        
        // Verificar si existe la semana
        $cadenaSql = "SELECT * FROM tabla_saldoanterior WHERE tsa_semana = '$tsa_semana'";
        $result = $f3->DB->exec($cadenaSql);

        if (!$result) {
            // Si no existe la semana, crear las 50 semanas
            for ($i = 1; $i <= 50; $i++) {
                $cadenaSql = "INSERT INTO tabla_saldoanterior (tsa_semana, tsa_SaldoAnterior) VALUES ('Semana $i', 0)";
                $f3->DB->exec($cadenaSql);
            }
            
            // Como recién creamos las semanas, el saldo anterior es 0
            $msg = "No existían datos, se crearon las 50 semanas y el saldo anterior es 0";
        } else {
            $saldoAnterior = $result[0]['tsa_SaldoAnterior'];
            $msg = "Datos recuperados con éxito";
        }

        echo json_encode([
            'mensaje' => $msg,
            'cant' => count($result),
            'data' => $result,
            'saldo_anterior' => $saldoAnterior
        ]);
    }
    

    public function obtenerTablaSemanal($f3){
        
    }
}
?>

