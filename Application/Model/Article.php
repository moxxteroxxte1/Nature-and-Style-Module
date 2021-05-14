<?php

namespace NatureAndStyle\CoreModule\Application\Model;

class Article extends Article_parent
{
    public function getPackagingUnit(): int
    {
        return $this->oxarticles__oxpackagingunit->value;
    }

    public function isUnique(): bool
    {
        return $this->oxarticles__oxunique->value || $this->_uniqueCategory();
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