<?php

namespace src\adminhub\model;
use src\client\model\AirtimeEPins as SrcAEP;
use zil\core\tracer\ErrorTracer;

class AirtimeEPins extends SrcAEP
{

    public function pinsByBatch(string $batch_tag, bool $desc = null) : array {
        try{
            if($desc == true)
                return $this->where( ['batch_tag', $batch_tag] )->key('id')->desc()->get('VERBOSE');

            return $this->where( ['batch_tag', $batch_tag] )->get('VERBOSE');
        }catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }

}
