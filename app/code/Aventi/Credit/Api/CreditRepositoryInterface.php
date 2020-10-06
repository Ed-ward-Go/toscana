<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\Credit\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface CreditRepositoryInterface
{

    /**
     * Save Credit
     * @param \Aventi\Credit\Api\Data\CreditInterface $credit
     * @return \Aventi\Credit\Api\Data\CreditInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Aventi\Credit\Api\Data\CreditInterface $credit
    );

    /**
     * Retrieve Credit
     * @param string $creditId
     * @return \Aventi\Credit\Api\Data\CreditInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($creditId);

    /**
     * Retrieve credit limit summary by customer ID
     *
     * @param int $customerId
     * @param bool $reload
     * @return \Aventi\Credit\Api\Data\CreditInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByCustomerId($customerId, $reload = false);

    /**
     * Retrieve Credit matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aventi\Credit\Api\Data\CreditSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Credit
     * @param \Aventi\Credit\Api\Data\CreditInterface $credit
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Aventi\Credit\Api\Data\CreditInterface $credit
    );

    /**
     * Delete Credit by ID
     * @param string $creditId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($creditId);
}

