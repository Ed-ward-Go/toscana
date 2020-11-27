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
     * @var \Aventi\LocationPopup\Helper\Data
     */
    private $locationHelper;

    public function __construct(
        \Aventi\LocationPopup\Helper\Data $locationHelper
    ) {
        $this->locationHelper = $locationHelper;
    }

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

        $source = $this->locationHelper->getValue();
        $order->setData('source_code', $source['id']);
    }
}
