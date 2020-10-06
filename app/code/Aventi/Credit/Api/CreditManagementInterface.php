<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aventi\Credit\Api;

/**
 * Interface CustomerManagementInterface
 * @api
 */
interface CreditManagementInterface
{
    /**
     * Get credit available amount
     *
     * @param int $customerId
     * @return float
     */
    public function getCreditAvailableAmount($customerId);
}
