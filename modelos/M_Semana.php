<?php
class M_Semana extends \DB\SQL\Mapper{
    public function __construct(){
        parent::__construct(\Base::instance()->get('DB'), 'semana');
    }
}
?>