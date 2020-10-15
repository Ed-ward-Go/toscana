<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\PriceByCity\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface PriceByCityRepositoryInterface
{

    /**
     * Save PriceByCity
     * @param \Aventi\PriceByCity\Api\Data\PriceByCityInterface $priceByCity
     * @return \Aventi\PriceByCity\Api\Data\PriceByCityInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Aventi\PriceByCity\Api\Data\PriceByCityInterface $priceByCity
    );

    /**
     * Retrieve PriceByCity
     * @param string $pricebycityId
     * @return \Aventi\PriceByCity\Api\Data\PriceByCityInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($pricebycityId);

    /**
     * Retrieve PriceByCity matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aventi\PriceByCity\Api\Data\PriceByCitySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete PriceByCity
     * @param \Aventi\PriceByCity\Api\Data\PriceByCityInterface $priceByCity
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Aventi\PriceByCity\Api\Data\PriceByCityInterface $priceByCity
    );

    /**
     * Delete PriceByCity by ID
     * @param string $pricebycityId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($pricebycityId);
}