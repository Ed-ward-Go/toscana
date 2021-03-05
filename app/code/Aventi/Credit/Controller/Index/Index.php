<?php
/**
 * Quick order by parte equipos
 * Copyright (C) 2018
 *
 * This file is part of Aventi/QuickOrder.
 *
 * Aventi/QuickOrder is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Aventi\Credit\Controller\Index;

use Aventi\Credit\Api\Data\CreditInterface;
use Aventi\Credit\Model\Service\CreditService;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;
    /**
     * @var CreditService
     */
    private $creditService;
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;
    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    private $priceHelper;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param CreditService $creditService
     * @param CheckoutSession $checkoutSession
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        CreditService $creditService,
        CheckoutSession $checkoutSession,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        LoggerInterface $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
        $this->jsonHelper = $jsonHelper;
        $this->creditService = $creditService;
        $this->checkoutSession = $checkoutSession;
        $this->priceHelper = $priceHelper;
        $this->logger = $logger;
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $return = [
            CreditInterface::AVAILABLE => 0,
            'canPay' => false,
            'formatPrice' => $this->priceHelper->currency(0, true, false)
        ];
        try {
            $quote = $this->checkoutSession->getQuote();
            $grandTotal = $quote->getGrandTotal();
            $creditAvailable = $this->creditService->getCreditAvailableAmount($quote->getCustomerId());

            $return = [
                CreditInterface::AVAILABLE => $creditAvailable,
                'canPay' => ($grandTotal > $creditAvailable) ? false : true,
                'formatPrice' => $this->priceHelper->currency($creditAvailable, true, false)
            ];
        } catch (NoSuchEntityException $e) {
            $this->logger->debug("QUOTE NOT FOUND: " . $e->getMessage());
        } catch (LocalizedException $e) {
            $this->logger->debug("QUOTE NOT FOUND: " . $e->getMessage());
        }
        return $this->jsonResponse($return);
    }

    /**
     * Create json response
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function jsonResponse($response = [])
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }
}
