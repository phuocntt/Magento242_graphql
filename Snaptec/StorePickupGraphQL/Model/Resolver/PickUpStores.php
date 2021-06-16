<?php

declare(strict_types = 1);

namespace Snaptec\StorePickUpGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Exception\ValidatorException;
use Snaptec\StorePickUpGraphQl\Model\Validator\Chain as ValidatorChain;

class PickUpStores implements ResolverInterface
{
    protected $validatorChain;

    public function __construct(ValidatorChain $validatorChain)
    {
        $this->validatorChain = $validatorChain;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, ?array $value = null, ?array $args = null)
    {
        $this->validatorChain->validated($args);
    }
}
