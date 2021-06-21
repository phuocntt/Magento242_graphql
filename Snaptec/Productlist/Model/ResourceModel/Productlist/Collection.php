<?php

/**
 * Connector Resource Collection
 */

namespace Snaptec\Productlist\Model\ResourceModel\Productlist;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
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
}
