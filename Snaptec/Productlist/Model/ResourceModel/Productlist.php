<?php

namespace Snaptec\Productlist\Model\ResourceModel;

/**
 * Connector Resource Model
 */
class Productlist extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('snaptec_product_list', 'productlist_id');
    }
}
