<?php
class M_Name extends \DB\SQL\Mapper{
    public function __construct(){
        parent::__construct(\Base::instance()->get('DB'), '');
    }
}
?>