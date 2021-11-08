<?php

namespace NatureAndStyle\CoreModule\Application\Model;

use OxidEsales\Eshop\Core\Registry;

class User extends User_parent
{
    public function isPriceViewModeNetto()
    {
        return $this->inGroup("oxiddealer") || (Registry::getConfig()->getConfigParam('blShowNetPrice'));
    }

    public function getIslandSurcharge()
    {
        return $this->oxuser__oxislandsurcharge->value;
    }

    public function login($userName, $password, $setSessionCookie = false)
    {
        parent::login($userName, $password, $setSessionCookie);

        if(!$this->oxuser__oxactive->value){
            throw oxNew(UserException::class, 'ERROR_MESSAGE_USER_NOACTIVE');
        }

        return true;
    }

    protected function getPasswordHashFromDatabase(string $userName, int $shopId, bool $isLoginToAdminBackend)
    {
        $database = DatabaseProvider::getDb();
        $userNameCondition = $this->formQueryPartForUserName($userName, $database);
        $shopOrRightsCondition = $this->formQueryPartForAdminView($shopId, $isLoginToAdminBackend);

        $query = "SELECT `oxpassword`
                    FROM oxuser
                    WHERE 1
                    AND $userNameCondition
                    $shopOrRightsCondition
                    ";

        return $database->getOne($query);
    }
}