<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aventi\Credit\Model\Checkout;

use Aventi\Credit\Api\Data\CreditInterface;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ConfigProvider
 *
 * @package Aheadworks\CreditLimit\Model\Checkout
 */
class ConfigProvider implements ConfigProviderInterface
{
    /**
     * Payment method code
     */
    const METHOD_CODE = 'credit';

    /**
     * @var CheckoutSession
     */
    private $session;

    /**
     * @var PaymentHelper
     */
    private $paymentHelper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param CheckoutSession $session
     * @param PaymentHelper $paymentHelper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CheckoutSession $session,
        PaymentHelper $paymentHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->session = $session;
        $this->paymentHelper = $paymentHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function getConfig()
    {
        $paymentMethod = $this->paymentHelper->getMethodInstance(self::METHOD_CODE);
        $quote = $this->session->getQuote();
        return $paymentMethod->isAvailable($quote) ? [
            'payment' => [
                self::METHOD_CODE => $this->getPaymentData($quote)
            ]
        ] : [];
    }

    /**
     * Get payment data
     *
     * @param Quote $quote
     * @throws NoSuchEntityException
     * @return array
     */
    private function getPaymentData($quote)
    {
        $store = $this->storeManager->getStore($quote->getStoreId());

        return [
            CreditInterface::AVAILABLE => 50000
        ];
    }
}
