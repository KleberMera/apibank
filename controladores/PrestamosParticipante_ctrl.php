<?php
class PrestamosParticipante_ctrl
{
    public $M_PrestamosParticipante = null;
    public function __construct()
    {
        $this->M_PrestamosParticipante = new M_PrestamosParticipante();
    }

    public function registrarPrestamoPart($f3){
        $mensaje='';
        
        $pp_partId = $f3->get('POST.pp_partId');
        $pp_semana = $f3->get('POST.pp_semana');
        $pp_pagos = $f3->get('POST.pp_pagos');
        $pp_fecha = $f3->get('POST.pp_fecha');

        $this->M_PrestamosParticipante->set('pp_partId', $pp_partId);
        $this->M_PrestamosParticipante->set('pp_semana', $pp_semana);
        $this->M_PrestamosParticipante->set('pp_pagos', $pp_pagos);
        $this->M_PrestamosParticipante->set('pp_fecha', $pp_fecha);
        $this->M_PrestamosParticipante->save();

        $mensaje = "Registro correcto";
        echo json_encode([
            'mensaje' => $mensaje
        ]);

    }
   
}