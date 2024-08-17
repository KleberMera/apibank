<?php
class Participant_ctrl
{
    public $M_Participant = null;
    public function __construct()
    {
        $this->M_Participant = new M_Participant();
    }

    public function reg_participant($f3)
{
    $mensaje = "";
    $newId = 0;
    $retorno = 0;

    $part_nombre = $f3->get('POST.part_nombre');
    $part_telefono = $f3->get('POST.part_telefono');
    $part_cupos = $f3->get('POST.part_cupos');

    $this->M_Participant->load(['part_nombre=?', $part_nombre]);
    if ($this->M_Participant->loaded() > 0) {
        // Actualizar los datos del participante existente
        $this->M_Participant->set('part_telefono', $part_telefono);
        $this->M_Participant->set('part_cupos', $part_cupos);
        $this->M_Participant->save();

        $mensaje = "Participante actualizado correctamente";
        $newId = $this->M_Participant->get('part_id');
        $retorno = 1;
    } else {
        // Registrar un nuevo participante
        $this->M_Participant->set('part_nombre', $part_nombre);
        $this->M_Participant->set('part_telefono', $part_telefono);
        $this->M_Participant->set('part_cupos', $part_cupos);
        $this->M_Participant->save();

        $mensaje = "Participante registrado correctamente";
        $newId = $this->M_Participant->get('part_id');
        $retorno = 1;
    }

    // Devolver la respuesta en formato JSON
    echo json_encode([
        'mensaje' => $mensaje,
        'id' => $newId,
        'retorno' => $retorno
    ]);
}

    public function buscar_cupo_participante($f3){
        $mensaje = "";
        $retorno = 0;
        $cupos= 0;
       
        $part_id = $f3->get('POST.part_id');
        
        $mensajeSql = "SELECT * ";
        $mensajeSql .= "FROM participante ";
        $mensajeSql .= "WHERE part_id = '$part_id' ";

        $result = $f3->DB->exec($mensajeSql);

        echo json_encode([
            'mensaje' => $mensaje,
            'cant' => count($result),
            'data' => $result
        ]);
    }


    public function listar_participantes($f3){
        $mensaje = "";
        $retorno = 0;
       
        $CadenaSql = "";
        $CadenaSql .= "SELECT * ";
        $CadenaSql .= "FROM participante ";
        $CadenaSql .= "ORDER BY part_id DESC";
        $result = $f3->DB->exec($CadenaSql);
        $msg = "";
        if ($result) {
            $msg = "Datos recuperados con éxito";
        }
        echo json_encode([
            'mensaje' => $msg,
            'cant' => count($result),
            'data' => $result
        ]);
    }
    
   
}
