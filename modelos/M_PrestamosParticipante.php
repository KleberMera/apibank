<?php
class M_PrestamosParticipante extends \DB\SQL\Mapper{
    public function __construct(){
        parent::__construct(\Base::instance()->get('DB'), 'prestamos_participante');
    }
}
?>