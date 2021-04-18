<?php

namespace NatureAndStyle\CoreModule\Core;

class Price extends Price_parent{

    public function getPrice(){
        if(($oxcmp_user && $oxcmp_user->inGroup('oxiddealer')) || $this->isNettoMode()){
            return $this->getNettoPrice();
        }else{
            return $this->getBruttoPrice();
        }
    }
}
