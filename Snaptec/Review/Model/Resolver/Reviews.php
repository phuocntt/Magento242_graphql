<?php
declare(strict_types=1);

namespace Snaptec\Review\Model\Resolver;

use Magento\Catalog\Model\Product;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\Builder as SearchCriteriaBuilder;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Review\Model\ReviewFactory;
use Magento\Review\Model\ResourceModel\Review\Summary\CollectionFactory as SummaryCollectionFactory;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Review\Model\Review\SummaryFactory;
/**
 * Class Reviews
 * @package Mageplaza\BetterProductReviewsGraphQl\Model\Resolver
 */
class Reviews implements ResolverInterface
{
     /**
     * @var SummaryFactory
     */
    private $summaryFactory;


    protected $logger;
    protected $_productloader;

    protected $_storeManager;
    /**
     * @var ReviewFactory
     */
    protected $reviewFactory;

    protected $reviewSummary;

    protected $voteFactory;

    protected $context;
    /**
     * Undocumented function
     * @param ContextInterface $context
     * @param [type] $context
     * @param \Psr\Log\LoggerInterface $logger
     * @param SummaryCollectionFactory $reviewSummary
     * @param ReviewFactory $reviewFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Product $_productloader
     */
    public function __construct(
        SummaryFactory $summaryFactory,
        \Psr\Log\LoggerInterface $logger,
        SummaryCollectionFactory $reviewSummary,
        ReviewFactory $reviewFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product $_productloader,
        \Magento\Review\Model\Rating\Option\VoteFactory $voteFactory
    ) {
        $this->logger = $logger;
        $this->reviewSummary = $reviewSummary;
        $this->reviewFactory = $reviewFactory;
        $this->_storeManager = $storeManager;
        $this->_productloader = $_productloader;
        $this->voteFactory = $voteFactory;
    }

    /**
     * Undocumented function
     *
     * @param Field $field
     * @param [type] $context
     * @param ResolveInfo $info
     * @param array $value
     * @param array $args
     * @return void
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        try {
            $blog = $this->getBlogArray($args['product_id']);

            return $blog;
        } catch (\Exception $exception) {
            throw new \Exception(__($exception->getMessage()));
        }
    }

    private function getBlogArray($productId)
    {
        $review = [];

        if($productId){
            $model = $this->reviewFactory->create();
            $collection = $model->getCollection();
            $data = $collection->addFieldToFilter('entity_pk_value',['eq' => $productId])->load();

            $summary = $this->reviewSummary->create()
            ->addFieldToFilter('entity_pk_value',['eq' => $productId]);

            foreach($summary->getData() as $key => $val) {
                $review['avg_value'][] = [
                    'primary_id' => $val['primary_id'],
                    'rating_summary' => $val['rating_summary'],
                    'reviews_count' => $val['reviews_count'],
                    'store_id' => $val['store_id']
                ];
            }

            $vote = $this->voteFactory->create()->getCollection();
            $voteByProduct = $vote->addFieldToFilter('entity_pk_value',['eq' => $productId]);

            foreach($data->getData() as $key => $val) {
                $review['item'][$key] = [
                    'entity_id' => $val['entity_id'],
                    'created_at' => $val['created_at'],
                    'review_id' => $val['review_id'],
                    'status_id' => $val['status_id'],
                    'detail_id' => $val['detail_id'],
                    'title' =>  $val['title'],
                    'detail' => $val['detail'],
                    'nickname' => $val['nickname'],
                    'customer_id' => $val['customer_id']
                ];
                foreach($voteByProduct->getData() as $k => $v) {
                    if($v['review_id'] == $val['review_id']) {
                        $review['item'][$key]['rating'][] = [
                            'percent' => $v['percent'],
                            'rating_id' => $v['rating_id'],
                            'review_id' => $v['review_id'],
                            'value' => $v['value'],
                            'vote_id' => $v['vote_id']
                        ];
                    }
                }
            }

            return $review;
        }
    }

    private function getRatingSummary($model, $product)
    {
       return $model->getEntitySummary($product, $this->_storeManager->getStore()->getId());
    }
}
