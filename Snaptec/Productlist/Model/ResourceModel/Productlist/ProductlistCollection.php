<?php

/**
 * Connector Resource Collection
 */

namespace Snaptec\Productlist\Model\ResourceModel\Productlist;

class ProductlistCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Resource initialization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Snaptec\Productlist\Model\Productlist', 'Snaptec\Productlist\Model\ResourceModel\Productlist');
    }

    public function getProductCollection($listModel, $snaptecObjectManager)
    {
        return $this->getProductCollectionByType(
            $listModel->getData('list_type'),
            $snaptecObjectManager,
            $listModel->getData('list_products'),
            $listModel
        );
    }

    public function getProductCollectionByType($type, $snaptecObjectManager, $listProduct = '', $listModel = null)
    {
        $collection = $snaptecObjectManager->create('Magento\Catalog\Model\Product')->getCollection()
            ->addAttributeToSelect($snaptecObjectManager->get('Magento\Catalog\Model\Config')
                ->getProductAttributes())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addUrlRewrite();
        switch ($type) {
            //Product List
            case 1:
                $collection->addFieldToFilter(
                    'entity_id',
                    ['in' => explode(',', $listProduct)]
                );
                break;
            //Best seller
            case 2:
                $orderItemTable   = $snaptecObjectManager->create('\Magento\Framework\App\ResourceConnection')
                    ->getTableName('sales_order_item');
                $collection       = $snaptecObjectManager->create('Magento\Catalog\Model\Product')->getCollection();
                $select           = $collection->getSelect()
                    ->join(
                        ['order_item' => $orderItemTable],
                        'order_item.product_id = entity_id',
                        ['order_item.product_id', 'order_item.qty_ordered']
                    )
                    ->columns('SUM(qty_ordered) as total_ordered');
                $groupFunction = 'group';
                $select->$groupFunction('order_item.product_id');
                $select->order(['total_ordered DESC']);
                $collection
                    ->addAttributeToSelect($snaptecObjectManager->get('Magento\Catalog\Model\Config')
                        ->getProductAttributes())
                    ->addMinimalPrice()
                    ->addFinalPrice()
                    ->addTaxPercents()
                    ->addUrlRewrite();
                break;
            //Most Viewed
            case 3:
                $productViewTable = $snaptecObjectManager->create('\Magento\Framework\App\ResourceConnection')
                    ->getTableName('report_viewed_product_aggregated_yearly');
                $collection       = $snaptecObjectManager
                    ->create('Magento\Catalog\Model\Product')->getCollection();
                $select           = $collection->getSelect()
                    ->join(
                        ['product_viewed' => $productViewTable],
                        'product_viewed.product_id = entity_id',
                        ['product_viewed.product_id', 'product_viewed.views_num']
                    )
                    ->columns('SUM(views_num) as total_viewed');
                $groupFunction = 'group';
                $select->$groupFunction('product_viewed.product_id');
                $select->order(['total_viewed DESC']);
                $collection
                    ->addAttributeToSelect($snaptecObjectManager->get('Magento\Catalog\Model\Config')
                        ->getProductAttributes())
                    ->addMinimalPrice()
                    ->addFinalPrice()
                    ->addTaxPercents()
                    ->addUrlRewrite();
                break;
            //New Updated
            case 4:
                $collection->setOrder('updated_at', 'desc');
                break;
            //Recently Added
            case 5:
                $collection->setOrder('created_at', 'desc');
                break;
            //Recently Added
            case 6:
                if ($listModel && $cateId = $listModel->getData('category_id')) {
                    $categoryModel = $snaptecObjectManager->create('\Magento\Catalog\Model\Category')->load($cateId);
                    if ($categoryModel->getId())
                        $collection->addCategoryFilter($categoryModel);
                    $collection->setOrder('cat_index_position', 'asc');
                }
                break;
            default:
                break;
        }
        $collection->setVisibility(['2', '4']);
        if (!$snaptecObjectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface')
            ->getValue('cataloginventory/options/show_out_of_stock')) {
            $snaptecObjectManager->get('Magento\CatalogInventory\Helper\Stock')
                ->addInStockFilterToCollection($collection);
        }
        return $collection;
    }
}
