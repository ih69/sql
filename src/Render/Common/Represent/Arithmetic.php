<?php

namespace Subapp\Sql\Render\Common\Represent;

use Subapp\Sql\Ast\Arithmetic as ArithmeticExpression;
use Subapp\Sql\Ast\NodeInterface;
use Subapp\Sql\Render\RendererInterface;

/**
 * Class Arithmetic
 * @package Subapp\Sql\Render\Common\Represent
 */
class Arithmetic extends Collection
{
    
    /**
     * @param NodeInterface|ArithmeticExpression $collection
     * @param RendererInterface                        $renderer
     * @return string
     */
    public function getSql(NodeInterface $collection, RendererInterface $renderer)
    {
        return sprintf($collection->isBraced() ? '(%s)' : '%s', parent::getSql($collection, $renderer));
    }
    
}