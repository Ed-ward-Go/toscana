<?php

namespace Aventi\Credit\Helper;

use Aventi\Credit\Model\CreditRepository;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var CreditRepository
     */
    private $creditRepository;
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    public function __construct(
        CreditRepository $creditRepository,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->creditRepository = $creditRepository;
        $this->priceCurrency = $priceCurrency;
    }

    public function getTotalList($customerId)
    {
        $data = [];
        try {
            $credit = $this->creditRepository->getByCustomerId($customerId);

            if ($credit) {
                $data = [
                    'Total Credit' => $this->getFormatedPrice($credit->getCredit()),
                    'Used Credit' => $this->getFormatedPrice($credit->getBalance()),
                    'Available Credit' => $this->getFormatedPrice($credit->getAvailable())
                ];
            }
        }catch (\Exception $e){
            return false;
        }

        return $data;
    }

    /**
     * @param $amount
     * @return string
     */
    public function getFormatedPrice($amount)
    {
        return $this->priceCurrency->convertAndFormat($amount);
    }
}
