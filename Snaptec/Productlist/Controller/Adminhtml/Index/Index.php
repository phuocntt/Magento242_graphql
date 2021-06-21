<?php

namespace Snaptec\Productlist\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{

    /**
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * @var \Snaptec\Productlist\Model\ResourceModel\Productlist\CollectionFactory
     */
    public $collectionFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Snaptec\Productlist\Model\ResourceModel\Productlist\CollectionFactory $collectionFactory,
        PageFactory $resultPageFactory
    ) {

        parent::__construct($context);
        $this->collectionFactory  = $collectionFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Product List action
     *
     * @return void
     */
    public function execute()
    {
//        $collection = $this->collectionFactory->create();
//        var_dump(count($collection));
//        var_dump($collection->getFirstItem()->debug());
//        die('2222222');
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Snaptec_Productlist::productlist_manage'
        )->addBreadcrumb(
            __('Productlist'),
            __('Productlist')
        )->addBreadcrumb(
            __('Manage Productlist'),
            __('Manage Productlist')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Product List'));
        return $resultPage;
    }
}
