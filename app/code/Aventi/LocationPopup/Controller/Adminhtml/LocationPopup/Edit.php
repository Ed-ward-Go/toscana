<?php
declare(strict_types=1);

namespace Aventi\LocationPopup\Controller\Adminhtml\LocationPopup;

class Edit extends \Aventi\LocationPopup\Controller\Adminhtml\LocationPopup
{

    protected $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('locationpopup_id');
        $model = $this->_objectManager->create(\Aventi\LocationPopup\Model\LocationPopup::class);
        
        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Locationpopup no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('aventi_locationpopup_locationpopup', $model);
        
        // 3. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Locationpopup') : __('New Locationpopup'),
            $id ? __('Edit Locationpopup') : __('New Locationpopup')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Locationpopups'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? __('Edit Locationpopup %1', $model->getId()) : __('New Locationpopup'));
        return $resultPage;
    }
}

