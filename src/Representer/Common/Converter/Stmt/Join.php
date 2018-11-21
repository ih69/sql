<?php

namespace Subapp\Sql\Representer\Common\Converter\Stmt;

use Subapp\Sql\Ast\NodeInterface;
use Subapp\Sql\Ast\Stmt\Join as JoinExpression;
use Subapp\Sql\Representer\AbstractConverter;
use Subapp\Sql\Representer\RepresenterInterface;

/**
 * Class Join
 * @package Subapp\Sql\Representer\Common\Converter\Stmt
 */
class Join extends AbstractConverter
{
    
    /**
     * @param NodeInterface|JoinExpression $node
     * @param RepresenterInterface                  $renderer
     * @return string
     */
    public function toSql(NodeInterface $node, RepresenterInterface $renderer)
    {
        return sprintf('%s JOIN %s %s (%s)',
            $node->getJoinType(),
            $renderer->toSql($node->getLeft()),
            $node->getConditionType(),
            $renderer->toSql($node->getCondition())
        );
    }

    /**
     * @inheritDoc
     *
     * @param NodeInterface|JoinExpression $node
     */
    public function toArray(NodeInterface $node, RepresenterInterface $renderer)
    {
        return [
            'type' => $node->getJoinType(),
            'conditionType' => $node->getConditionType(),
            'left' => $renderer->toArray($node->getLeft()),
            'condition' => $renderer->toArray($node->getCondition()),
        ];
    }

    /**
     * @inheritDoc
     */
    public function toNode(array $ast, RepresenterInterface $renderer)
    {
        // TODO: Implement fromArray() method.
    }

}