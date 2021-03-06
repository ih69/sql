<?php

namespace Subapp\Sql\Syntax\Common\Parser;

use Subapp\Lexer\LexerInterface;
use Subapp\Sql\Ast\Arithmetic as ArithmeticExpression;
use Subapp\Sql\Ast\MathOperator;
use Subapp\Sql\Ast\NodeInterface;
use Subapp\Sql\Lexer\Lexer;
use Subapp\Sql\Syntax\ProcessorInterface;

/**
 * Class Arithmetic
 * @package Subapp\Sql\Syntax\Common\Parser
 */
class Arithmetic extends AbstractDefaultParser
{
    
    /**
     * @param LexerInterface     $lexer
     * @param ProcessorInterface $processor
     * @return NodeInterface|Arithmetic
     */
    public function parse(LexerInterface $lexer, ProcessorInterface $processor)
    {
        $arithmetic = new ArithmeticExpression();
        $parser = $this->getParserC($processor);
        
        do {
            
            // parse math expression
            $expression = $parser->parse($lexer, $processor);
            
            // collect / and * operands
            if ($this->isFactorMathOperator($lexer)) {
                $inner = new ArithmeticExpression();
                $inner->append($expression);
                
                // white next token is either / or *
                // loop it and collect into separated collection
                while ($this->isFactorMathOperator($lexer) && $lexer->next()) {
                    $operator = new MathOperator($lexer->getTokenValue());
                    $inner->append($operator);
                    $expression = $parser->parse($lexer, $processor);
                    $inner->append($expression);
                };
                
                // wrap into embrace expression
                $expression = $inner;
            }
            
            // append wrapped or raw literal expression
            $arithmetic->append($expression);
            
            // fetch plain operator either + or -
            $token = $lexer->peek();
            
            if ($token && ($token->is(Lexer::T_PLUS) || $token->is(Lexer::T_MINUS))) {
                $arithmetic->append(new MathOperator($token->getToken()));
            }
            
            // always reset peek position for correct view behind tokens...
            $lexer->resetPeek();
            
        } while ($this->isMathOperator($lexer) && $lexer->next());
        
        return $arithmetic;
    }
    
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return self::PARSER_ARITHMETIC;
    }
    
}