<?php
declare(strict_types=1);

namespace Aventi\PriceByCity\Model\Data;

use Aventi\PriceByCity\Api\Data\StorePriceInterface;

class StorePrice extends \Magento\Framework\Api\AbstractExtensibleObject implements StorePriceInterface
{

    /**
     * Get storeprice_id
     * @return string|null
     */
    public function getStorepriceId()
    {
        return $this->_get(self::STOREPRICE_ID);
    }

    /**
     * Set storeprice_id
     * @param string $storepriceId
     * @return \Aventi\PriceByCity\Api\Data\StorePriceInterface
     */
    public function setStorepriceId($storepriceId)
    {
        return $this->setData(self::STOREPRICE_ID, $storepriceId);
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
     * @return \Aventi\PriceByCity\Api\Data\StorePriceInterface
     */
    public function setPostalCode($postalCode)
    {
        return $this->setData(self::POSTALCODE, $postalCode);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Aventi\PriceByCity\Api\Data\StorePriceExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Aventi\PriceByCity\Api\Data\StorePriceExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aventi\PriceByCity\Api\Data\StorePriceExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
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
     * @return \Aventi\PriceByCity\Api\Data\StorePriceInterface
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
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
     * @return \Aventi\PriceByCity\Api\Data\StorePriceInterface
     */
    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
    }
}

