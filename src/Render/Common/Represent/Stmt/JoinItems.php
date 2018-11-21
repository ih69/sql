<?php

namespace Subapp\Sql\Render\Common\Represent\Stmt;

use Subapp\Sql\Ast\NodeInterface;
use Subapp\Sql\Ast\Stmt\JoinItems as JoinItemsExpression;
use Subapp\Sql\Render\Common\Represent\Collection;
use Subapp\Sql\Render\RendererInterface;

/**
 * Class JoinItems
 * @package Subapp\Sql\Render\Common\Represent\Stmt
 */
class JoinItems extends Collection
{
    
    /**
     * @param NodeInterface|JoinItemsExpression $collection
     * @param RendererInterface                       $renderer
     * @return string
     */
    public function getSql(NodeInterface $collection, RendererInterface $renderer)
    {
        return $collection->count() > 0 ? sprintf(' %s', parent::getSql($collection, $renderer)) : null;
    }
    
}