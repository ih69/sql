<?php

namespace Subapp\Sql\Ast\Stmt;

use Subapp\Sql\Ast\AbstractExpression;
use Subapp\Sql\Ast\Collection;
use Subapp\Sql\Ast\ExpressionInterface;

/**
 * Class Join
 * @package Subapp\Sql\Ast
 */
class Join extends AbstractExpression
{
    
    const INNER = 'INNER';
    const LEFT = 'LEFT';
    const RIGHT = 'RIGHT';
    const CROSS = 'CROSS';
    const STRAIGHT_JOIN = 'STRAIGHT_JOIN';
    
    const CONDITION_ON = 'ON';
    const CONDITION_USING = 'USING';
    
    /**
     * @var ExpressionInterface
     */
    private $left;
    
    /**
     * @var Collection
     */
    private $condition;
    
    /**
     * @var string
     */
    private $conditionType;
    
    /**
     * @var string
     */
    private $joinType;
    
    /**
     * Join constructor.
     * @param string $joinType
     */
    public function __construct($joinType = Join::INNER)
    {
        $this->joinType = $joinType;
        $this->conditionType = Join::CONDITION_ON;
    }
    
    /**
     * @return ExpressionInterface
     */
    public function getLeft()
    {
        return $this->left;
    }
    
    /**
     * @param ExpressionInterface $left
     */
    public function setLeft(ExpressionInterface $left)
    {
        $this->left = $left;
    }
    
    /**
     * @return string
     */
    public function getJoinType()
    {
        return $this->joinType;
    }
    
    /**
     * @param string $joinType
     */
    public function setJoinType($joinType)
    {
        $this->joinType = $joinType;
    }
    
    /**
     * @return Collection
     */
    public function getCondition()
    {
        return $this->condition;
    }
    
    /**
     * @param Collection $collection
     */
    public function setCondition(Collection $collection)
    {
        $this->condition = $collection;
    }
    
    /**
     * @return string
     */
    public function getConditionType()
    {
        return $this->conditionType;
    }
    
    /**
     * @param string $conditionType
     */
    public function setConditionType($conditionType)
    {
        $this->conditionType = $conditionType;
    }
    
    /**
     * @return string
     */
    public function getSqlizerName()
    {
        return 'stmt.join';
    }
    
}