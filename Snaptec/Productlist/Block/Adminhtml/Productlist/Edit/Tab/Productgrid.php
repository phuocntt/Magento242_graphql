<?php

namespace Snaptec\Productlist\Block\Adminhtml\Productlist\Edit\Tab;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Productgrid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Magento\Framework\Registry|null
     */
    public $coreRegistry = null;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    public $productFactory;
    public $snaptecObjectManager   = null;
    public $productlistFactory = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Snaptec\Productlist\Model\ProductlistFactory $productlistFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\ObjectManagerInterface $snaptecObjectManager,
        array $data = []
    ) {

        $this->snaptecObjectManager   = $snaptecObjectManager;
        $this->productFactory     = $productFactory;
        $this->productlistFactory = $productlistFactory;
        $this->coreRegistry       = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * init construct
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('product_grid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function _prepareCollection()
    {
        $collection = $this->productFactory->create()->getCollection()
                ->addAttributeToSelect('entity_id')
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('sku');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function _prepareColumns()
    {

        $this->addColumn(
            'in_products',
            [
            'type'             => 'checkbox',
            'html_name'        => 'products_id',
            'required'         => true,
            'values'           => $this->_getSelectedProducts(),
            'align'            => 'center',
            'index'            => 'entity_id',
            'header_css_class' => 'col-select',
            'column_css_class' => 'col-select',
            'renderer'         => '\Snaptec\Productlist\Block\Adminhtml\Productlist\Edit\Tab\Productrender',
                ]
        );

        $this->addColumn(
            'entity_id',
            [
            'header'           => __('ID'),
            'index'            => 'entity_id',
            'width'            => '20px',
            'header_css_class' => 'col-name',
            'column_css_class' => 'col-name'
                ]
        );

        $this->addColumn(
            'name',
            [
            'header'           => __('Name'),
            'index'            => 'name',
            'header_css_class' => 'col-name',
            'column_css_class' => 'col-name'
                ]
        );

        $this->addColumn(
            'type_id',
            [
            'header'           => __('Type'),
            'type'             => 'options',
            'index'            => 'type_id',
            'options'          => $this->snaptecObjectManager
                ->get('\Magento\Catalog\Model\Product\Type')->getOptionArray(),
            'header_css_class' => 'col-type',
            'column_css_class' => 'col-type'
                ]
        );

        $this->addColumn(
            'sku',
            [
            'header'           => __('SKU'),
            'index'            => 'sku',
            'header_css_class' => 'col-sku',
            'column_css_class' => 'col-sku'
                ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return mixed|string
     */
    public function getGridUrl()
    {
        return $this->_getData(
            'grid_url'
        ) ? $this->_getData(
            'grid_url'
        ) : $this->getUrl(
            'productlist/*/productgrid',
            ['_current' => true]
        );
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return false;
    }

    /**
     * @return array
     */
    public function _getSelectedProducts()
    {
        $products = array_keys($this->getSelectedProducts());
        return $products;
    }

    /**
     * @return array
     */
    public function getSelectedProducts()
    {
        $productlist_id = $this->getRequest()->getParam('productlist_id');
        if (!isset($productlist_id)) {
            $productlist_id = 0;
        }

        $productlist = $this->productlistFactory->create()->load($productlist_id);
        $products    = [];
        if ($productlist->getId()) {
            $products = explode(',', str_replace(' ', '', $productlist->getData('list_products')));
        }

        $proIds = [];

        foreach ($products as $product) {
            $proIds[$product] = ['id' => $product];
        }
        return $proIds;
    }
}
