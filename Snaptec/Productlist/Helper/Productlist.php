<?php

namespace Snaptec\Productlist\Helper;

class Productlist extends Data
{

    public function getListTypeId()
    {
        return [
            1 => __('Custom Product List'),
            2 => __('Best Seller'),
            3 => __('Most View'),
            4 => __('Newly Updated'),
            5 => __('Recently Added'),
            6 => __('Category Products'),
        ];
    }

    public function getTypeOption()
    {
        return [
            ['value' => 1, 'label' => __('Custom Product List')],
            ['value' => 2, 'label' => __('Best Seller')],
            ['value' => 3, 'label' => __('Most View')],
            ['value' => 4, 'label' => __('Newly Updated')],
            ['value' => 5, 'label' => __('Recently Added')],
            ['value' => 6, 'label' => __('Category Products')],
        ];
    }

    public function getListLocation()
    {
        return [
            ['value' => 0, 'label' => __('None')],
            ['value' => 1, 'label' => __('Left')],
            ['value' => 2, 'label' => __('Top')],
            ['value' => 3, 'label' => __('Right')],
            ['value' => 4, 'label' => __('Bottom')],
        ];
    }

    public function getProductCollection($listModel)
    {
        $collection = $this->snaptecObjectManager
            ->create('Snaptec\Productlist\Model\ResourceModel\Productlist\ProductlistCollection')
            ->getProductCollection($listModel, $this->snaptecObjectManager);
        return $collection;
    }
}
