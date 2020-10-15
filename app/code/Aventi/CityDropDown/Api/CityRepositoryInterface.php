<?php


namespace Aventi\CityDropDown\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface CityRepositoryInterface
{

    /**
     * Save City
     * @param \Aventi\CityDropDown\Api\Data\CityInterface $city
     * @return \Aventi\CityDropDown\Api\Data\CityInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Aventi\CityDropDown\Api\Data\CityInterface $city
    );

    /**
     * Retrieve City
     * @param string $cityId
     * @return \Aventi\CityDropDown\Api\Data\CityInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($cityId);

    /**
     * Retrieve City matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aventi\CityDropDown\Api\Data\CitySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete City
     * @param \Aventi\CityDropDown\Api\Data\CityInterface $city
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Aventi\CityDropDown\Api\Data\CityInterface $city
    );

    /**
     * Delete City by ID
     * @param string $cityId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($cityId);
}
