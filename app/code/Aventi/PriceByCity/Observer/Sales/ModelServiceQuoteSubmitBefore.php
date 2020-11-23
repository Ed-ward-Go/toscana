<?php
/**
 * Add file comment to orders
 * Copyright (C) 2018
 *
 * This file is part of Aventi/OrderComment.
 *
 * Aventi/OrderComment is free software: you can redistribute it and/or modify
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

namespace Aventi\PriceByCity\Observer\Sales;

class ModelServiceQuoteSubmitBefore implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $order = $observer->getEvent()->getOrder();
        /** @var $order \Magento\Sales\Model\Order **/

        $quote = $observer->getEvent()->getQuote();
        /** @var $quote \Magento\Quote\Model\Quote **/

        $order->setData('source_code', $quote->getData('source_code'));

    }
}
