<?php

namespace Subapp\Sql\Syntax\MySQL\Parser;

use Subapp\Lexer\LexerInterface;
use Subapp\Sql\Ast\Arithmetic as ArithmeticExpression;
use Subapp\Sql\Ast\ExpressionInterface;
use Subapp\Sql\Lexer\Lexer;
use Subapp\Sql\Syntax\ProcessorInterface;

/**
 * Class Arithmetic
 * @package Subapp\Sql\Syntax\MySQL\Parser
 */
class Arithmetic extends AbstractMySQLParser
{
    
    /**
     * @param LexerInterface     $lexer
     * @param ProcessorInterface $processor
     * @return ArithmeticExpression|ExpressionInterface
     */
    public function parse(LexerInterface $lexer, ProcessorInterface $processor)
    {
        $parser = $this->getMathOperandParser($processor);
        
        $arithmetic = new ArithmeticExpression();
        $arithmetic->setOperandA($parser->parse($lexer, $processor));
        
        if (!$this->isMathOperator($lexer)) {
            $this->throwSyntaxError($lexer, '+', '-', '/', '*');
        }
    
        while ($this->isMathOperator($lexer)) {
            $lexer->next();
            $token = $lexer->getToken();
            
            var_dump($token);
            var_dump($parser->parse($lexer, $processor));
    
//            $arithmetic->setOperator($token->getToken());
//            $arithmetic->setOperandB($parser->parse($lexer, $processor));
        }
        
        die();
        
        return $arithmetic;
    }
    
}