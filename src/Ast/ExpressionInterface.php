<?php

namespace Subapp\Sql\Ast;

/**
 * Interface ExpressionInterface
 * @package Subapp\Sql\Ast
 */
interface ExpressionInterface
{
    
    /**
     * @return string
     */
    public function getRenderer();

    /**
     * @return string
     */
    public function getNodeName();

}