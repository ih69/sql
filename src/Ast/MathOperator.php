<?php

namespace Subapp\Sql\Ast;

use Subapp\Sql\Converter\ConverterInterface;

/**
 * Class MathOperator
 * @package Subapp\Sql\Ast
 */
class MathOperator extends AbstractNode
{
    
    const NONE     = null;
    const MINUS    = '-';
    const PLUS     = '+';
    const MULTIPLY = '*';
    const DIVIDE   = '/';
    
    /**
     * @var string
     */
    private $operator;
    
    /**
     * MathOperator constructor.
     * @param null|string $operator
     */
    public function __construct($operator = self::NONE)
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
    public function setOperator(string $operator)
    {
        $this->operator = $operator;
    }
    
    /**
     * @return string
     */
    public function getConverter()
    {
        return ConverterInterface::CONVERTER_MATH_OPERATOR;
    }
    
}