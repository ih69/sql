<?php

namespace Subapp\Sql\Ast\Condition;

use Subapp\Sql\Ast\AbstractNode;
use Subapp\Sql\Converter\ConverterInterface;

/**
 * Class LogicOperator
 * @package Subapp\Sql\Ast
 */
class LogicOperator extends AbstractNode
{
    
    const AND = 'AND';
    const OR  = 'OR';
    const XOR = 'XOR';
    
    /**
     * @var string
     */
    private $operator;
    
    /**
     * LogicOperator constructor.
     * @param string $operator
     */
    public function __construct($operator = self:: AND)
    {
        $this->operator = $operator;
    }
    
    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }
    
    /**
     * @param string $operator
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
    }
    
    /**
     * @inheritdoc
     */
    public function getConverter()
    {
        return ConverterInterface::CONVERTER_CONDITION_LOGIC_OPERATOR;
    }
    
}