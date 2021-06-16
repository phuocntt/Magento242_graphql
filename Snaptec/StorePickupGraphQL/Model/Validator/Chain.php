<?php

declare(strict_types=1);

namespace Snaptec\StorePickUpGraphQl\Model\Validator;

use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;

class Chain
{
    /** @var array */
    private $validatorList;

    public function __construct(array $validatorList = [])
    {
        $this->validatorList = $validatorList;
    }

    /**
    * @param array $args
    * @throws ArgValidatorInterface
    */
    public function validated(array $args)
    {

    }
}
