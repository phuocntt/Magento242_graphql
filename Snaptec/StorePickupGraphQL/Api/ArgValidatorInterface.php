<?php

declare(strict_types=1);

namespace Snaptec\StorePickUpGraphQL\Api;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;

/**
 * @api
 */
interface ArgValidatorInterface
{
    /**
     * @param array $args
     * @throws GraphQlInputException
     */
    public function execute(array $args): void;
}
