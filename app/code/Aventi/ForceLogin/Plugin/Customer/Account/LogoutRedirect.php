<?php

namespace Aventi\ForceLogin\Plugin\Customer\Account;

use Magento\Customer\Controller\Account\Logout;

class LogoutRedirect
{
    public function afterExecute(
        Logout $subject,
        $result
    ) {
        $result->setPath('/customer/account/login');
        return $result;
    }
}
