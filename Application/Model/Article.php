<?php

namespace NatureAndStyle\CoreModule\Application\Model;

class Article extends Article_parent
{

    public function getTitle()
    {
        return $this->oxarticles__oxtitle->value;
    }

    public function getPackagingUnit(): int
    {
        return $this->oxarticles__oxpackagingunit->value;
    }

    public function isUnique(): bool
    {
        return $this->oxarticles__oxunique->value || $this->_uniqueCategory();
    }

    public function isNew(): bool
    {
        return $this->oxarticles__oxnew->value;
    }

    private function _uniqueCategory(): bool
    {
        foreach ($this->getCategoryIds() as $id) {
            if (strpos($id, 'unique') !== false) {
                return true;
            }
        }
        return false;
    }
}