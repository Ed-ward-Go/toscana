<?php
declare(strict_types=1);

namespace Aventi\PriceByCity\Api\Data;

interface StorePriceInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const POSTALCODE = 'postalcode';
    const STOREPRICE_ID = 'storeprice_id';
    const PRODUCT_ID = 'product_id';
    const PRICE = 'price';

    /**
     * Get storeprice_id
     * @return string|null
     */
    public function getStorepriceId();

    /**
     * Set storeprice_id
     * @param string $storepriceId
     * @return \Aventi\PriceByCity\Api\Data\StorePriceInterface
     */
    public function setStorepriceId($storepriceId);

    /**
     * Get postalCode
     * @return string|null
     */
    public function getPostalCode();

    /**
     * Set postalCode
     * @param string $postalCode
     * @return \Aventi\PriceByCity\Api\Data\StorePriceInterface
     */
    public function setPostalCode($postalCode);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Aventi\PriceByCity\Api\Data\StorePriceExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Aventi\PriceByCity\Api\Data\StorePriceExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aventi\PriceByCity\Api\Data\StorePriceExtensionInterface $extensionAttributes
    );

    /**
     * Get product_id
     * @return string|null
     */
    public function getProductId();

    /**
     * Set product_id
     * @param string $productId
     * @return \Aventi\PriceByCity\Api\Data\StorePriceInterface
     */
    public function setProductId($productId);

    /**
     * Get price
     * @return string|null
     */
    public function getPrice();

    /**
     * Set price
     * @param string $price
     * @return \Aventi\PriceByCity\Api\Data\StorePriceInterface
     */
    public function setPrice($price);
}

