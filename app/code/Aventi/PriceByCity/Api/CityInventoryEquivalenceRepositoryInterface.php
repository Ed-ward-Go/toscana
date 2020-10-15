<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\PriceByCity\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface CityInventoryEquivalenceRepositoryInterface
{

    /**
     * Save CityInventoryEquivalence
     * @param \Aventi\PriceByCity\Api\Data\CityInventoryEquivalenceInterface $cityInventoryEquivalence
     * @return \Aventi\PriceByCity\Api\Data\CityInventoryEquivalenceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Aventi\PriceByCity\Api\Data\CityInventoryEquivalenceInterface $cityInventoryEquivalence
    );

    /**
     * Retrieve CityInventoryEquivalence
     * @param string $cityinventoryequivalenceId
     * @return \Aventi\PriceByCity\Api\Data\CityInventoryEquivalenceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($cityinventoryequivalenceId);

    /**
     * Retrieve CityInventoryEquivalence matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aventi\PriceByCity\Api\Data\CityInventoryEquivalenceSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete CityInventoryEquivalence
     * @param \Aventi\PriceByCity\Api\Data\CityInventoryEquivalenceInterface $cityInventoryEquivalence
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Aventi\PriceByCity\Api\Data\CityInventoryEquivalenceInterface $cityInventoryEquivalence
    );

    /**
     * Delete CityInventoryEquivalence by ID
     * @param string $cityinventoryequivalenceId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($cityinventoryequivalenceId);
}
