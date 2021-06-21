<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Snaptec\ProductlistGraphQl\Model\Resolver;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\CatalogGraphQl\Model\Resolver\Products\Query\ProductQueryInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Catalog\Model\Layer\Resolver;


/**
 * Products field resolver, used for GraphQL request processing.
 */
class GetProductList implements ResolverInterface
{
    protected $_productCollectionFactory;
    protected $productListFactory;
    protected $category;

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Category $category
     * @param \Snaptec\Productlist\Model\ProductlistFactory $productListFactory
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Category $category,
        \Snaptec\Productlist\Model\ProductlistFactory $productListFactory
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->category = $category;
        $this->productListFactory = $productListFactory;
    }

    /**
     * Resolve product list
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     *
     * @return array[]|Value|mixed
     *
     * @throws GraphQlAuthorizationException
     * @throws GraphQlNoSuchEntityException
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productListId = $args['input']['product_list_id'];
        if (isset($args['input']['pageSize'])) {
            $pageSize = $args['input']['pageSize'];
        } else {
            $pageSize = null;
        }

        if (isset($args['input']['currentPage'])) {
            $currentPage = $args['input']['currentPage'];
        } else {
            $currentPage = null;
        }

        $productList = $this->productListFactory->create()->load($productListId);
        $collection = $this->_productCollectionFactory->create();
        $collection->addFieldToFilter('status', Status::STATUS_ENABLED);
        $collection->addAttributeToSelect('*');
        if (!$productList->getId()) {
            throw new GraphQlAuthorizationException(__('Product list not available!'));
        }

        if (!$productList->getListStatus()) {
            throw new GraphQlAuthorizationException(__('Product list not active!'));
        }

        // Custom product list
        if ($productList->getListType() == 1) {
            $productIdString = $productList->getListProducts();
            $ids = explode(",", $productIdString);
            $collection->addFieldToFilter('entity_id', ['in' => $ids]);
        }

        // Best seller
        if ($productList->getListType() == 2) {
            $orderItemTable   = $objectManager->create('\Magento\Framework\App\ResourceConnection')
                ->getTableName('sales_order_item');
            $collection       = $objectManager->create('Magento\Catalog\Model\Product')->getCollection();
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
                ->addAttributeToSelect($objectManager->get('Magento\Catalog\Model\Config')
                    ->getProductAttributes())
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addUrlRewrite();
        }

        //Most Viewed
        if ($productList->getListType() == 3) {
            $productViewTable = $objectManager->create('\Magento\Framework\App\ResourceConnection')
                ->getTableName('report_viewed_product_aggregated_yearly');
            $collection       = $objectManager
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
                ->addAttributeToSelect($objectManager->get('Magento\Catalog\Model\Config')
                    ->getProductAttributes())
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addUrlRewrite();
        }

        //New Updated
        if ($productList->getListType() == 4) {
            $collection->setOrder('updated_at', 'DESC');
        }

        //Recently Added
        if ($productList->getListType() == 5) {
            $collection->setOrder('created_at', 'desc');
        }

        // Category ID
        if ($productList->getListType() == 6) {
            $id = $productList->getCategoryId();
            $categoryModel = $this->category->load($id);
            if (!$categoryModel->getId()){
                throw new GraphQlAuthorizationException(__('The category not available'));
            }
            $collection->addCategoriesFilter(['in' => $id]);
        }

        $collection->setOrder('entity_id','DESC');
        $collection
            ->setPageSize($pageSize)
            ->setCurPage($currentPage)
            ->load();
        $productsData = [];
        foreach ($collection as $product) {
            $productData = $product->getData();
            $productData['model'] = $product;
            $productsData[] = $productData;
        }
        $data = [
            'total_count' => count($collection),
            'items' => $productsData,
        ];

        return array(
            'productlist_id'    =>  $productListId,
            'list_image'    =>  $productList->getData('list_image'),
            'list_image_tablet'    =>  $productList->getData('list_image_tablet'),
            'list_type'    =>  $productList->getData('list_type'),
            'location'    =>  $productList->getData('sort_order'),
            'list_products'    =>  $productList->getData('list_products'),
            'category_id'    =>  $productList->getData('category_id'),
            'products'    =>  $data,
        );
    }
}
