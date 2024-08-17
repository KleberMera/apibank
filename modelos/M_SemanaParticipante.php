<?php
class M_SemanaParticipante extends \DB\SQL\Mapper{
    public function __construct(){
        parent::__construct(\Base::instance()->get('DB'), 'semana_participante');
    }
    
    

}
?>