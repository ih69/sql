<?php

namespace Subapp\Sql\Converter\Common\Stmt;

use Subapp\Sql\Ast\NodeInterface;
use Subapp\Sql\Ast\Literal;
use Subapp\Sql\Ast\Stmt\Limit as LimitNode;
use Subapp\Sql\Converter\AbstractConverter;
use Subapp\Sql\Converter\ProviderInterface;

/**
 * Class Limit
 * @package Subapp\Sql\Converter\Common\Stmt
 */
class Limit extends AbstractConverter
{
    
    /**
     * @param NodeInterface|LimitNode $node
     * @param ProviderInterface                   $provider
     * @return string
     */
    public function toSql(NodeInterface $node, ProviderInterface $provider)
    {
        $offset = $node->getOffset();
        $length = $node->getLength();
        
        switch (true) {
            case ($length instanceOf Literal && !($offset instanceOf Literal)):
                return sprintf(' LIMIT %s', $provider->toSql($length));
            case ($length instanceOf Literal && $offset instanceOf Literal):
                return sprintf(' LIMIT %s, %s', $provider->toSql($offset), $provider->toSql($length));
        }
    
        return null;
    }

    /**
     * @inheritDoc
     *
     * @param NodeInterface|LimitNode $node
     */
    public function toArray(NodeInterface $node, ProviderInterface $provider)
    {
        $values = parent::toArray($node, $provider);

        $values['offset'] = $node->getOffset();
        $values['length'] = $node->getLength();

        return $values;
    }

    /**
     * @inheritDoc
     */
    public function toNode(array $ast, ProviderInterface $provider)
    {
        return new LimitNode((int)$ast['offset'], (int)$ast['length']);
    }

}