<?php

namespace Snaptec\Productlist\Block\Adminhtml\Productlist;

/**
 * Adminhtml Productlist grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Snaptec\Productlist\Model\Productlist
     */
    public $productlistFactory;

    /**
     * @var \Snaptec\Productlist\Model\ResourceModel\Productlist\CollectionFactory
     */
    public $collectionFactory;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    public $moduleManager;

    /**
     * @var order model
     */
    public $resource;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Snaptec\Productlist\Model\ProductlistFactory $productlistFactory
     * @param \Snaptec\Productlist\Model\ResourceModel\Productlist\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Snaptec\Productlist\Model\ProductlistFactory $productlistFactory,
        \Snaptec\Productlist\Model\ResourceModel\Productlist\CollectionFactory $collectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        array $data = []
    ) {

        $this->collectionFactory  = $collectionFactory;
        $this->moduleManager       = $moduleManager;
        $this->resource           = $resourceConnection;
        $this->productlistFactory = $productlistFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('productlistGrid');
        $this->setDefaultSort('productlist_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare collection
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    public function _prepareCollection()
    {
        $collection = $this->collectionFactory->create();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    public function _prepareColumns()
    {
        $this->addColumn('snaptec_productlist_id', [
            'header' => __('ID'),
            'index'  => 'productlist_id',
        ]);

        $this->addColumn('list_title', [
            'header' => __('List Title'),
            'index'  => 'list_title',
        ]);

        $this->addColumn('sort_order', [
            'type'    => 'options',
            'header' => __('Location'),
            'index'  => 'sort_order',
            'options' => [
                0 => __('None'),
                1 => __('Left'),
                2 => __('Top'),
                3 => __('Right'),
                4 => __('Bottom'),
            ]

        ]);

        $this->addColumn('list_status', [
            'type'    => 'options',
            'header'  => __('Status'),
            'index'   => 'list_status',
            'options' => $this->productlistFactory->create()->toOptionStatusHash(),
        ]);

        $this->addColumn(
            'action',
            [
                'header'           => __('View'),
                'type'             => 'action',
                'getter'           => 'getId',
                'actions'          => [
                    [
                        'caption' => __('Edit'),
                        'url'     => [
                            'base'   => '*/*/edit',
                            'params' => ['store' => $this->getRequest()->getParam('store')]
                        ],
                        'field'   => 'productlist_id'
                    ]
                ],
                'sortable'         => false,
                'filter'           => false,
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action',
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Row click url
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', [
            'productlist_id' => $row->getId()
        ]);
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }
}
