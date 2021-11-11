<?php

namespace NatureAndStyle\CoreModule\Application\Model;

class DeliverySet extends DeliverySet_parent
{

    public function hasTelAvis()
    {
        return $this->oxdeliveryset__oxtelavis->value;
    }

}