<?php

declare(strict_types=1);

namespace Snaptec\StorePickUpGraphQl\Api;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;

Interface ArgValidatorInteface
{
    public function execute(array $args) : void;
}
