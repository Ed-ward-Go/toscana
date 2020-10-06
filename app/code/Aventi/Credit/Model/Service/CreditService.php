<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aventi\Credit\Model\Service;

use Aventi\Credit\Api\CreditManagementInterface;
use Aventi\Credit\Api\CreditRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Psr\Log\LoggerInterface;

/**
 * Class CreditService
 * @package Aventi\Credit\Model\Service
 */
class CreditService implements CreditManagementInterface
{
    /**
     * @var CreditRepositoryInterface
     */
    private $creditRepository;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * CreditService constructor.
     * @param CreditRepositoryInterface $creditRepository
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        CreditRepositoryInterface $creditRepository,
        PriceCurrencyInterface $priceCurrency,
        LoggerInterface $logger
    ) {
        $this->creditRepository = $creditRepository;
        $this->priceCurrency = $priceCurrency;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function getCreditAvailableAmount($customerId)
    {
        $summary = $this->getCreditLimitSummary($customerId);
        if (!$summary) {
            return 0;
        }
        return $this->priceCurrency->round($summary->getAvailable());
    }

    /**
     * Get credit limit summary
     *
     * @param int $customerId
     * @return CreditInterface|null
     */
    private function getCreditLimitSummary($customerId)
    {
        try {
            $summary = $this->creditRepository->getByCustomerId($customerId);
            return $summary;
        } catch (NoSuchEntityException $noSuchEntityException) {
            return null;
        }
    }

    public function managementCreateOrder($customerId, $total)
    {
        $credit = $this->getCreditLimitSummary($customerId);

        if (!$credit) {
            return 0;
        }
        $balance = $credit->getBalance();
        $available = $credit->getAvailable();
        $credit->setBalance($balance - $total);
        $credit->setAvailable($available - $total);

        try {
            $this->creditRepository->save($credit);
        } catch (NoSuchEntityException $noSuchEntityException) {
            $this->logger->error("Error actualizando el credito cuando se haga la compra: " . $noSuchEntityException->getMessage());
            return null;
        }
    }
}
