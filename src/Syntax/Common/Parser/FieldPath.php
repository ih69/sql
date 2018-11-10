<?php

namespace Subapp\Sql\Syntax\Common\Parser;

use Subapp\Lexer\LexerInterface;
use Subapp\Sql\Ast\ExpressionInterface;
use Subapp\Sql\Ast\FieldPath as FieldPathExpression;
use Subapp\Sql\Lexer\Lexer;
use Subapp\Sql\Syntax\ProcessorInterface;

/**
 * Class FieldPath
 * @package Subapp\Sql\Syntax\Common\Parser
 */
class FieldPath extends AbstractDefaultParser
{
    
    /**
     * @param LexerInterface     $lexer
     * @param ProcessorInterface $processor
     * @return ExpressionInterface|FieldPathExpression
     */
    public function parse(LexerInterface $lexer, ProcessorInterface $processor)
    {
        $parser = $this->getIdentifierParser($processor);
        
        $table = $parser->parse($lexer, $processor);
        $this->shift(Lexer::T_DOT, $lexer);
        $field = $parser->parse($lexer, $processor);
        
        $expression = new FieldPathExpression();
        
        $expression->setTable($table);
        $expression->setField($field);
        
        return $expression;
    }
    
}