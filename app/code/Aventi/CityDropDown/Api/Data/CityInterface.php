<?php


namespace Aventi\CityDropDown\Api\Data;

interface CityInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const NAME = 'name';
    const POSTALCODE = 'postalCode';
    const CITY_ID = 'city_id';
    const REGION_ID = 'region_id';

    /**
     * Get city_id
     * @return string|null
     */
    public function getCityId();

    /**
     * Set city_id
     * @param string $cityId
     * @return \Aventi\CityDropDown\Api\Data\CityInterface
     */
    public function setCityId($cityId);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Aventi\CityDropDown\Api\Data\CityInterface
     */
    public function setName($name);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Aventi\CityDropDown\Api\Data\CityExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Aventi\CityDropDown\Api\Data\CityExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aventi\CityDropDown\Api\Data\CityExtensionInterface $extensionAttributes
    );

    /**
     * Get region_id
     * @return string|null
     */
    public function getRegionId();

    /**
     * Set region_id
     * @param string $regionId
     * @return \Aventi\CityDropDown\Api\Data\CityInterface
     */
    public function setRegionId($regionId);

    /**
     * Get postalCode
     * @return string|null
     */
    public function getPostalCode();

    /**
     * Set postalCode
     * @param string $postalCode
     * @return \Aventi\CityDropDown\Api\Data\CityInterface
     */
    public function setPostalCode($postalCode);
}
