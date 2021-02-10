<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aventi\Imagen\Controller\Adminhtml\System\Config;

use Magento\Backend\App\Action;

class Collect extends Action
{

    protected $resultJsonFactory;
    /**
     * @var Data
     */
    protected $image;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Data $helper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Aventi\Imagen\Model\Process $image
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->image = $image;
        parent::__construct($context);
    }

    /**
     * Collect relations data
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $res = null;
        try {
            $res = $this->image->update();                        
        } catch (\Exception $e) {
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        }
        
        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultJsonFactory->create();

        return $result->setData(['success' => true, 'result' => $res]);
    }   
}
?>