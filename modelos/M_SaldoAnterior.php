<?php
class M_SaldoAnterior extends \DB\SQL\Mapper{
    public function __construct(){
        parent::__construct(\Base::instance()->get('DB'), 'tabla_saldoanterior');
    }
    
    

}
?>