<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\Credit\Api\Data;

interface CreditInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const CUSTOMER_ID = 'customer_id';
    const CREDIT = 'credit';
    const CREDIT_ID = 'credit_id';
    const BALANCE = 'balance';
    const AVAILABLE = 'available';

    /**
     * Get credit_id
     * @return string|null
     */
    public function getCreditId();

    /**
     * Set credit_id
     * @param string $creditId
     * @return \Aventi\Credit\Api\Data\CreditInterface
     */
    public function setCreditId($creditId);

    /**
     * Get available
     * @return string|null
     */
    public function getAvailable();

    /**
     * Set available
     * @param string $available
     * @return \Aventi\Credit\Api\Data\CreditInterface
     */
    public function setAvailable($available);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Aventi\Credit\Api\Data\CreditExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Aventi\Credit\Api\Data\CreditExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aventi\Credit\Api\Data\CreditExtensionInterface $extensionAttributes
    );

    /**
     * Get credit
     * @return string|null
     */
    public function getCredit();

    /**
     * Set credit
     * @param string $credit
     * @return \Aventi\Credit\Api\Data\CreditInterface
     */
    public function setCredit($credit);

    /**
     * Get balance
     * @return string|null
     */
    public function getBalance();

    /**
     * Set balance
     * @param string $balance
     * @return \Aventi\Credit\Api\Data\CreditInterface
     */
    public function setBalance($balance);

    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param string $customerId
     * @return \Aventi\Credit\Api\Data\CreditInterface
     */
    public function setCustomerId($customerId);
}

