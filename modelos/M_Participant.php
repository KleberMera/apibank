<?php
class M_Participant extends \DB\SQL\Mapper{
    public function __construct(){
        parent::__construct(\Base::instance()->get('DB'), 'participante');
    }
}
?>