<?php

namespace Subapp\Sql\Ast;

/**
 * Class AbstractExpression
 * @package Subapp\Sql\Ast
 */
abstract class AbstractExpression implements ExpressionInterface
{
    
    /**
     * @return array
     */
    public function toArray()
    {
        return $this->jsonSerialize();
    }
    
}