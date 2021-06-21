<?php

namespace Snaptec\Productlist\Model;

/**
 * Productlist Model
 *
 * @method \Snaptec\Productlist\Model\Resource\Page _getResource()
 * @method \Snaptec\Productlist\Model\Resource\Page getResource()
 */
class Productlist extends \Magento\Framework\Model\AbstractModel
{
    const ENABLE = 1;
    const DISABLE = 0;
    public $snaptecObjectManager;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $snaptecObjectManager
     * @param \Magento\Framework\Registry $registry
     * @param \Snaptec\Productlist\Model\ResourceModel\Productlist $resource
     * @param \Snaptec\Productlist\Model\ResourceModel\Productlist\Collection $resourceCollection
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\ObjectManagerInterface $snaptecObjectManager,
        \Magento\Framework\Registry $registry,
        \Snaptec\Productlist\Model\ResourceModel\Productlist $resource,
        \Snaptec\Productlist\Model\ResourceModel\Productlist\Collection $resourceCollection
    ) {
        $this->snaptecObjectManager = $snaptecObjectManager;

        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Snaptec\Productlist\Model\ResourceModel\Productlist');
    }

    /**
     * @return array Status
     */
    public function toOptionStatusHash()
    {
        $status = [
            self::ENABLE => __('Enable'),
            self::DISABLE => __('Disabled'),
        ];
        return $status;
    }
}
