<?php

namespace Aventi\ForceLogin\Plugin\Customer\Account;

/**
 * Class LoginPostPlugin
 * @package Aventi\ForceLogin\Plugin\Customer\Account
 */
class LoginPostPlugin
{

    /**
     * @param \Magento\Customer\Controller\Account\LoginPost $subject
     * @param $result
     * @return mixed
     */
    public function afterExecute(
        \Magento\Customer\Controller\Account\LoginPost $subject,
        $result
    ) {
        $result->setPath('');
        return $result;
    }
}
