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


namespace Snaptec\StorePickUpGraphQL\Model\Validator\Query;

use Snaptec\StorePickupGraphQL\Api\ArgValidatorInterface;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;

class Page implements ArgValidatorInterface
{
    /**
     * @inheritDoc
     */
    public function execute(array $args): void
    {
        if (!isset($args['pageSize']) || $args['pageSize'] < 0) {
            throw new GraphQlInputException(__('pageSize must be greate then 0'));
        }

        if (!isset($args['currentPage']) || $args['currentPage'] < 0) {
            throw new GraphQlInputException(__('pageSize must be greate then 0'));
        }

        if (!isset($args['latitude']) || $args['latitude'] < -90 || $args['latitude'] > 90) {
            throw new GraphQlInputException(__('latitude must be a number between -90 and 90'));
        }

        if (!isset($args['longitude']) || $args['longitude'] < -180 || $args['longitude'] > 180) {
            throw new GraphQlInputException(__('longitude must be a number between -180 and 180'));
        }
    }
}
