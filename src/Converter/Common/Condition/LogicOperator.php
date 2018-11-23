<?php

namespace Subapp\Sql\Converter\Common\Condition;

use Subapp\Sql\Ast\Condition\LogicOperator as LogicOperatorNode;
use Subapp\Sql\Ast\NodeInterface;
use Subapp\Sql\Converter\AbstractConverter;
use Subapp\Sql\Converter\ProviderInterface;

/**
 * Class LogicOperator
 * @package Subapp\Sql\Converter\Common\Condition
 */
class LogicOperator extends AbstractConverter
{
    
    /**
     * @param NodeInterface|LogicOperatorNode $node
     * @param ProviderInterface                           $provider
     * @return string
     */
    public function toSql(NodeInterface $node, ProviderInterface $provider)
    {
        return (string)$node->getOperator();
    }


    
}