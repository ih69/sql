<?php

namespace Subapp\Sql\Syntax\Common\Parser;

use Subapp\Lexer\LexerInterface;
use Subapp\Sql\Ast\ExpressionInterface;
use Subapp\Sql\Ast\Variable;
use Subapp\Sql\Ast\Arguments;
use Subapp\Sql\Lexer\Lexer;
use Subapp\Sql\Syntax\Common;
use Subapp\Sql\Syntax\ProcessorInterface;

/**
 * Class Variables
 * @package Subapp\Sql\Syntax\Common\Parser
 */
class Variables extends Common\Parser\AbstractDefaultParser
{

    /**
     * @param LexerInterface     $lexer
     * @param ProcessorInterface $processor
     * @return ExpressionInterface|Arguments
     */
    public function parse(LexerInterface $lexer, ProcessorInterface $processor)
    {
        $variables = new Arguments();
        $parser = $this->getComplexParser($processor);

        do {
            $expression = new Variable($parser->parse($lexer, $processor));
    
            if ($this->isAlias($lexer)) {
                $alias = $this->getAliasParser($processor)->parse($lexer, $processor);
                $expression->setAlias($alias);
            }

            $variables->append($expression);
        } while ($lexer->toToken(Lexer::T_COMMA));

        return $variables;
    }

}