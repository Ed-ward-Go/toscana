<?php

namespace Aventi\Credit\Helper;

use Aventi\Credit\Model\CreditRepository;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    private $creditRepository;

    public function __construct(
        CreditRepository $creditRepository
    ) {
        $this->creditRepository = $creditRepository;
    }

    public function getTotalList($customerId)
    {
        $credit = $this->creditRepository->getByCustomerId($customerId);

        $data = [];

        if ($credit) {
            $data = [
                'Available' => $credit->getAvailable(),
                'Credit' => $credit->getCredit(),
                'Balance' => $credit->getBalance()
            ];
        }

        return $data;
    }
}
