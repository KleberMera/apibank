<?php
class Semana_ctrl
{
    public $M_Semana = null;
    public function __construct()
    {
        $this->M_Semana = new M_Semana();
    }

    public function reg_semana($f3)
    {
        $mensaje = "";
        $newId = 0;
        $retorno = 0;
    
        $sem_nombre = $f3->get('POST.sem_nombre');
        $sem_partId_P = $f3->get('POST.sem_partId_P');
        $sem_prestamos = $f3->get('POST.sem_prestamos');
        $sem_SaldoA = $f3->get('POST.sem_SaldoA');
        $sem_partId_D = $f3->get('POST.sem_partId_D');
        $sem_total = $f3->get('POST.sem_total');
    
        // Buscar la semana por nombre
        $this->M_Semana->load(['sem_nombre=?', $sem_nombre]);
        
        if ($this->M_Semana->loaded()) {
            // La semana ya existe, actualizar los datos
            $this->M_Semana->set('sem_partId_P', $sem_partId_P);
            $this->M_Semana->set('sem_prestamos', $sem_prestamos);
            $this->M_Semana->set('sem_SaldoA', $sem_SaldoA);
            $this->M_Semana->set('sem_partId_D', $sem_partId_D);
            $this->M_Semana->set('sem_total', $sem_total);
            $this->M_Semana->save();
    
            $mensaje = "Semana actualizada correctamente";
            $newId = $this->M_Semana->get('sem_id');
            $retorno = 1;
        } else {
            // La semana no existe, crear nueva
            $this->M_Semana->set('sem_nombre', $sem_nombre);
            $this->M_Semana->set('sem_partId_P', $sem_partId_P);
            $this->M_Semana->set('sem_prestamos', $sem_prestamos);
            $this->M_Semana->set('sem_SaldoA', $sem_SaldoA);
            $this->M_Semana->set('sem_partId_D', $sem_partId_D);
            $this->M_Semana->set('sem_total', $sem_total);
            $this->M_Semana->save();
    
            $mensaje = "Semana registrada correctamente";
            $newId = $this->M_Semana->get('sem_id');
            $retorno = 1;
        }
    
        // Devolver la respuesta en formato JSON
        echo json_encode([
            'mensaje' => $mensaje,
            'id' => $newId,
            'retorno' => $retorno
        ]);
    }
    
}