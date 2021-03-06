<?php

namespace Subapp\Sql\Ast;

/**
 * Class AbstractFunction
 * @package Subapp\Sql\Ast
 */
abstract class AbstractFunction extends AbstractNode
{
    
    /**
     * @var Identifier
     */
    private $functionName;
    
    /**
     * @var Arguments
     */
    private $arguments;
    
    /**
     * AbstractFunction constructor.
     */
    public function __construct()
    {
        $this->arguments = new Arguments();
    }
    
    /**
     * @return Identifier
     */
    public function getFunctionName()
    {
        return $this->functionName;
    }
    
    /**
     * @param Identifier $name
     */
    public function setFunctionName(Identifier $name)
    {
        $this->functionName = $name;
    }
    
    /**
     * @return Arguments
     */
    public function getArguments()
    {
        return $this->arguments;
    }
    
    /**
     * @param Arguments $arguments
     */
    public function setArguments(Arguments $arguments)
    {
        $this->arguments = $arguments;
    }
    
    /**
     * @param $index
     * @return NodeInterface|null
     */
    public function get($index)
    {
        return $this->arguments->get($index);
    }
    
    /**
     * @param NodeInterface $expression
     */
    public function append(NodeInterface $expression)
    {
        $this->arguments->append($expression);
    }
    
    /**
     * @return void
     */
    public function clear()
    {
        $this->arguments->clear();
    }
    
}