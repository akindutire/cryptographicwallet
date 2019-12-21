<?php

namespace src\adminhub\model;


use zil\core\tracer\ErrorTracer;

class DataCard extends \src\client\model\DataCard
{

    public function deleteCards()
    {

    }

    public function cardsByBatch(string $batch_tag, bool $desc = null): array
    {
        try {
            if ($desc == true)
                return $this->where(['batch_tag', $batch_tag])->key('id')->desc()->get('VERBOSE');

            return $this->where(['batch_tag', $batch_tag])->get('VERBOSE');
        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }


}
