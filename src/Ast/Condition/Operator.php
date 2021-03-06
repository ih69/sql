<?php

namespace Subapp\Sql\Ast\Condition;

use Subapp\Sql\Ast\AbstractNode;
use Subapp\Sql\Converter\ConverterInterface;

/**
 * Class ComparisonOperator
 * @package Subapp\Sql\Ast\Condition
 */
class Operator extends AbstractNode
{
    
    const EQ = '=';
    const NE = '<>';
    const GT = '>';
    const LT = '<';
    const GE = '>=';
    const LE = '<=';
    
    /**
     * @var string
     */
    private $operator;
    
    /**
     * ComparisonOperator constructor.
     * @param string $operator
     */
    public function __construct($operator = self::EQ)
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
        return ConverterInterface::CONVERTER_CONDITION_CMP_OPERATOR;
    }
    
}