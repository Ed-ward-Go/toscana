<?php
declare(strict_types=1);

namespace Aventi\LocationPopup\Api\Data;

interface LocationPopupInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const LOCATIONPOPUP_ID = 'locationpopup_id';
    const STORE = 'store';
    const CITY = 'city';

    /**
     * Get locationpopup_id
     * @return string|null
     */
    public function getLocationpopupId();

    /**
     * Set locationpopup_id
     * @param string $locationpopupId
     * @return \Aventi\LocationPopup\Api\Data\LocationPopupInterface
     */
    public function setLocationpopupId($locationpopupId);

    /**
     * Get store
     * @return string|null
     */
    public function getStore();

    /**
     * Set store
     * @param string $store
     * @return \Aventi\LocationPopup\Api\Data\LocationPopupInterface
     */
    public function setStore($store);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Aventi\LocationPopup\Api\Data\LocationPopupExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Aventi\LocationPopup\Api\Data\LocationPopupExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aventi\LocationPopup\Api\Data\LocationPopupExtensionInterface $extensionAttributes
    );

    /**
     * Get city
     * @return string|null
     */
    public function getCity();

    /**
     * Set city
     * @param string $city
     * @return \Aventi\LocationPopup\Api\Data\LocationPopupInterface
     */
    public function setCity($city);
}

