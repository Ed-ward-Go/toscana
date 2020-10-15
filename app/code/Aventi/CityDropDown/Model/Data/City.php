<?php


namespace Aventi\CityDropDown\Model\Data;

use Aventi\CityDropDown\Api\Data\CityInterface;

class City extends \Magento\Framework\Api\AbstractExtensibleObject implements CityInterface
{

    /**
     * Get city_id
     * @return string|null
     */
    public function getCityId()
    {
        return $this->_get(self::CITY_ID);
    }

    /**
     * Set city_id
     * @param string $cityId
     * @return \Aventi\CityDropDown\Api\Data\CityInterface
     */
    public function setCityId($cityId)
    {
        return $this->setData(self::CITY_ID, $cityId);
    }

    /**
     * Get name
     * @return string|null
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * Set name
     * @param string $name
     * @return \Aventi\CityDropDown\Api\Data\CityInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Aventi\CityDropDown\Api\Data\CityExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Aventi\CityDropDown\Api\Data\CityExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aventi\CityDropDown\Api\Data\CityExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get region_id
     * @return string|null
     */
    public function getRegionId()
    {
        return $this->_get(self::REGION_ID);
    }

    /**
     * Set region_id
     * @param string $regionId
     * @return \Aventi\CityDropDown\Api\Data\CityInterface
     */
    public function setRegionId($regionId)
    {
        return $this->setData(self::REGION_ID, $regionId);
    }

    /**
     * Get postalCode
     * @return string|null
     */
    public function getPostalCode()
    {
        return $this->_get(self::POSTALCODE);
    }

    /**
     * Set postalCode
     * @param string $postalCode
     * @return \Aventi\CityDropDown\Api\Data\CityInterface
     */
    public function setPostalCode($postalCode)
    {
        return $this->setData(self::POSTALCODE, $postalCode);
    }
}
