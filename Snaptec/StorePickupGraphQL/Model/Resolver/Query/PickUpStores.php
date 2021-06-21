<?php
/**
 * Copyright (c) 2018 TechDivision GmbH <info@techdivision.com> - TechDivision GmbH
 * All rights reserved
 *
 * This product includes proprietary software developed at TechDivision GmbH, Germany
 * For more information see http://www.techdivision.com/
 *
 * To obtain a valid license for using this software please contact us at
 * license@techdivision.com
 */
declare(strict_types=1);

namespace Snaptec\StorePickUpGraphQL\Model\Resolver\Query;

use Ideo\StoreLocator\Model\StoreFactory;
use Snaptec\StorePickupGraphQL\Model\Validator\Chain as ValidatorChain;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class PickUpStores implements ResolverInterface
{
    public const CURRENT_PAGE = 'currentPage';
    public const PAGE_SIZE = 'pageSize';

    /** @var ValidatorChain */
    private $validatorChain;

    private $storeFactory;

    public function __construct(
        StoreFactory $storeFactory,
        ValidatorChain $validatorChain
    ) {
        $this->storeFactory = $storeFactory;
        $this->validatorChain = $validatorChain;
    }

    /**
     * @inheritDoc
     * @throws ValidatorException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {

        $this->validatorChain->validated($args);

        $data = [];
        $storeModel = $this->storeFactory->create();
        $storeCollection = $storeModel->getCollection();

        $storeData = $storeCollection->addFieldToSelect("*")
        ->addFieldToFilter(
            ['name', 'address', 'city'],
            [
                ['like' => '%'.$args['search'].'%'],
                ['like' => '%'.$args['search'].'%'],
                ['like' => '%'.$args['search'].'%']
            ]
        )
        ->setPageSize($args[self::PAGE_SIZE])
        ->setCurPage($args[self::CURRENT_PAGE])
        ->load();

        foreach ($storeData as $store) {
            $data[] = [
                'store_id' => $store->getStoreId(),
                'category_id' => $store->getCategoryId(),
                'name' => $store->getName(),
                'address' => $store->getAddress(),
                'postcode' => $store->getPostCode(),
                'city' => $store->getCity(),
                'country' => $store->getCountry(),
                'phone' => $store->getPhone(),
                'email' => $store->getEmail(),
                'fax' => $store->getFax(),
                'website' => $store->getWebsite(),
                'lat' => $store->getLat(),
                'lng' => $store->getLng(),
                'zoom' => $store->getZoom(),
                'is_active' => $store->getIsActive(),
                'created_at' => $store->getCreatedAt(),
                'updated_at' => $store->getUpdatedAt(),
                'latitude' => $store->getLat(),
                'longitude' => $store->getLng(),
                'distance' => $this->getDistance($args['latitude'], $args['longitude'], $store->getLat(), $store->getLng())
            ];
        }

        return array(
            'totalCount' => count($data),
            'items' => $data
        );
    }

    function getDistance($lat1, $lon1, $lat2, $lon2){
        $R = 6371; // km
        $dLat = $this->toRad($lat2-$lat1);
        $dLon = $this->toRad($lon2-$lon1);
        $lat1 = $this->toRad($lat1);
        $lat2 = $this->toRad($lat2);

        $a = sin($dLat/2) * sin($dLat/2) +sin($dLon/2) * sin($dLon/2) * cos($lat1) * cos($lat2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $d = $R * $c;
        return $d;
    }

    // Converts numeric degrees to radians
    function toRad($Value)
    {
        return $Value * pi() / 180;
    }
}
