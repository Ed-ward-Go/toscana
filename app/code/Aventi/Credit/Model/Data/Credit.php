<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\Credit\Model\Data;

use Aventi\Credit\Api\Data\CreditInterface;

class Credit extends \Magento\Framework\Api\AbstractExtensibleObject implements CreditInterface
{

    /**
     * Get credit_id
     * @return string|null
     */
    public function getCreditId()
    {
        return $this->_get(self::CREDIT_ID);
    }

    /**
     * Set credit_id
     * @param string $creditId
     * @return \Aventi\Credit\Api\Data\CreditInterface
     */
    public function setCreditId($creditId)
    {
        return $this->setData(self::CREDIT_ID, $creditId);
    }

    /**
     * Get available
     * @return string|null
     */
    public function getAvailable()
    {
        return $this->_get(self::AVAILABLE);
    }

    /**
     * Set available
     * @param string $available
     * @return \Aventi\Credit\Api\Data\CreditInterface
     */
    public function setAvailable($available)
    {
        return $this->setData(self::AVAILABLE, $available);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Aventi\Credit\Api\Data\CreditExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Aventi\Credit\Api\Data\CreditExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aventi\Credit\Api\Data\CreditExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get credit
     * @return string|null
     */
    public function getCredit()
    {
        return $this->_get(self::CREDIT);
    }

    /**
     * Set credit
     * @param string $credit
     * @return \Aventi\Credit\Api\Data\CreditInterface
     */
    public function setCredit($credit)
    {
        return $this->setData(self::CREDIT, $credit);
    }

    /**
     * Get balance
     * @return string|null
     */
    public function getBalance()
    {
        return $this->_get(self::BALANCE);
    }

    /**
     * Set balance
     * @param string $balance
     * @return \Aventi\Credit\Api\Data\CreditInterface
     */
    public function setBalance($balance)
    {
        return $this->setData(self::BALANCE, $balance);
    }

    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->_get(self::CUSTOMER_ID);
    }

    /**
     * Set customer_id
     * @param string $customerId
     * @return \Aventi\Credit\Api\Data\CreditInterface
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }
}

