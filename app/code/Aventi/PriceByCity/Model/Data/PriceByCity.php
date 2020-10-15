<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\PriceByCity\Model\Data;

use Aventi\PriceByCity\Api\Data\PriceByCityInterface;

class PriceByCity extends \Magento\Framework\Api\AbstractExtensibleObject implements PriceByCityInterface
{

    /**
     * Get storeprice_id
     * @return int|null
     */
    public function getStorepriceId()
    {
        return $this->_get(self::STOREPRICE_ID);
    }

    /**
     * Set storeprice_id
     * @param int|null $storepriceId
     * @return \Aventi\PriceByCity\Api\Data\PriceByCityInterface
     */
    public function setStorepriceId($storepriceId)
    {
        return $this->setData(self::STOREPRICE_ID, $storepriceId);
    }

    /**
     * Get product_id
     * @return string|null
     */
    public function getProductId()
    {
        return $this->_get(self::PRODUCT_ID);
    }

    /**
     * Set product_id
     * @param string $productId
     * @return \Aventi\PriceByCity\Api\Data\PriceByCityInterface
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Aventi\PriceByCity\Api\Data\PriceByCityExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Aventi\PriceByCity\Api\Data\PriceByCityExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aventi\PriceByCity\Api\Data\PriceByCityExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get price
     * @return string|null
     */
    public function getPrice()
    {
        return $this->_get(self::PRICE);
    }

    /**
     * Set price
     * @param string $price
     * @return \Aventi\PriceByCity\Api\Data\PriceByCityInterface
     */
    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
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
     * @return \Aventi\PriceByCity\Api\Data\PriceByCityInterface
     */
    public function setRegionId($regionId)
    {
        return $this->setData(self::REGION_ID, $regionId);
    }
}
