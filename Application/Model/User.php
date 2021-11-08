<?php

namespace NatureAndStyle\CoreModule\Application\Model;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface;
use OxidEsales\Eshop\Core\Exception\CookieException;
use OxidEsales\Eshop\Core\Exception\UserException;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\PasswordServiceBridgeInterface;

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
        $isLoginAttemptToAdminBackend = $this->isAdmin();

        $cookie = Registry::getUtilsServer()->getOxCookie();
        if ($cookie === null && $isLoginAttemptToAdminBackend) {
            throw oxNew(CookieException::class, 'ERROR_MESSAGE_COOKIE_NOCOOKIE');
        }

        $config = Registry::getConfig();
        $shopId = $config->getShopId();

        /** New authentication mechanism */
        $passwordHashFromDatabase = $this->getPasswordHashFromDatabase($userName, $shopId, $isLoginAttemptToAdminBackend);
        $passwordServiceBridge = $this->getContainer()->get(PasswordServiceBridgeInterface::class);
        if ($password && !$this->isLoaded()) {
            $userIsAuthenticated = $passwordServiceBridge->verifyPassword($password, $passwordHashFromDatabase);
            if ($userIsAuthenticated) {
                $this->loadAuthenticatedUser($userName, $shopId);
            }
        }

        /** Old authentication + authorization */
        if ($password && !$this->isLoaded()) {
            $this->_dbLogin($userName, $password, $shopId);
        }

        /** If needed, store a rehashed password with the authenticated user */
        if ($password && $this->isLoaded()) {
            $passwordNeedsRehash = $this->isOutdatedPasswordHashAlgorithmUsed ||
                $passwordServiceBridge->passwordNeedsRehash($passwordHashFromDatabase);
            if ($passwordNeedsRehash) {
                $generatedPasswordHash = $this->hashPassword($password);
                $this->oxuser__oxpassword = new Field($generatedPasswordHash, Field::T_RAW);
                /** The use of a salt is deprecated and an empty salt will be stored */
                $this->oxuser__oxpasssalt = new Field('');
                $this->save();
            }
        }

        /** Event for alternative authentication and authorization mechanisms, or whatsoever */
        $this->onLogin($userName, $password);

        /**
         * If the user has not been loaded until this point, authentication & authorization is considered as failed.
         */
        if (!$this->isLoaded()) {
            throw oxNew(UserException::class, 'ERROR_MESSAGE_USER_NOVALIDLOGIN');
        }

        if (!$this->oxuser__oxactive->value) {
            throw oxNew(UserException::class, '[{oxcontent ident="ueber_un"}]');
        }

        //resetting active user
        $this->setUser(null);

        if ($isLoginAttemptToAdminBackend) {
            Registry::getSession()->setVariable('auth', $this->oxuser__oxid->value);
        } else {
            Registry::getSession()->setVariable('usr', $this->oxuser__oxid->value);
        }

        // cookie must be set ?
        if ($setSessionCookie && $config->getConfigParam('blShowRememberMe')) {
            Registry::getUtilsServer()->setUserCookie(
                $this->oxuser__oxusername->value,
                $this->oxuser__oxpassword->value,
                $config->getShopId(),
                31536000,
                static::USER_COOKIE_SALT
            );
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

    private function formQueryPartForUserName($user, DatabaseInterface $database): string
    {
        $condition = 'oxuser.oxusername = ' . $database->quote($user);

        return $condition;
    }

    protected function formQueryPartForAdminView($sShopID, $blAdmin)
    {
        $sShopSelect = '';

        // Admin view: can only login with higher than 'user' rights
        if ($blAdmin) {
            $sShopSelect = " and ( oxrights != 'user' ) ";
        }

        return $sShopSelect;
    }

    private function loadAuthenticatedUser(string $userName, int $shopId)
    {
        $isLoginToAdminBackend = $this->isAdmin();
        $userId = $this->getAuthenticatedUserId($userName, $shopId, $isLoginToAdminBackend);
        if (!$this->load($userId)) {
            throw oxNew(UserException::class, 'ERROR_MESSAGE_USER_NOVALIDLOGIN');
        }
    }

    private function hashPassword(string $password): string
    {
        $passwordServiceBridge = $this->getContainer()->get(PasswordServiceBridgeInterface::class);

        return $passwordServiceBridge->hash($password);
    }

    private function getAuthenticatedUserId(string $userName, int $shopId, bool $isLoginToAdminBackend)
    {
        $database = DatabaseProvider::getDb();
        $userNameCondition = $this->formQueryPartForUserName($userName, $database);
        $shopOrRightsCondition = $this->formQueryPartForAdminView($shopId, $isLoginToAdminBackend);

        $query = "SELECT `OXID`
                    FROM oxuser
                    WHERE 1
                    AND $userNameCondition
                    $shopOrRightsCondition
                    ";

        return $database->getOne($query);
    }
}