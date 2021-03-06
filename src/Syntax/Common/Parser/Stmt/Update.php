<?php

namespace Subapp\Sql\Syntax\Common\Parser\Stmt;

use Subapp\Lexer\LexerInterface;
use Subapp\Sql\Ast;
use Subapp\Sql\Lexer\Lexer;
use Subapp\Sql\Syntax\Common\Parser\AbstractDefaultParser;
use Subapp\Sql\Syntax\ProcessorInterface;

/**
 * Class Update
 * @package Subapp\Sql\Syntax\Common\Parser\Stmt
 */
class Update extends AbstractDefaultParser
{

    /**
     * @inheritDoc
     */
    public function parse(LexerInterface $lexer, ProcessorInterface $processor)
    {
        $root = new Ast\Root();

        // If lexer was executed like lexer->tokenize(sql, FALSE) - (without shifting with blank token)
        // that means next token is NOT T_UPDATE token
        $this->shiftIf(Lexer::T_UPDATE, $lexer);

        // parse three consecutive expressions
        // UPDATE [Modifiers] [TableReference] SET [SetAssignments]
        $root->setModifiers($this->getModifierStmtParser($processor)->parse($lexer, $processor));
        $root->setTableReference($this->getTableReferenceStmtParser($processor)->parse($lexer, $processor));
        $root->setAssignment($this->getSetStmtParser($processor)->parse($lexer, $processor));

        if ($this->isJoin($lexer)) {
            $root->setJoins($this->getJoinItemsParser($processor)->parse($lexer, $processor));
        }

        if ($this->isWhere($lexer)) {
            $root->setWhere($this->getWhereParser($processor)->parse($lexer, $processor));
        }

        if ($this->isOrderBy($lexer)) {
            $root->setOrderBy($this->getOrderByParser($processor)->parse($lexer, $processor));
        }

        if ($this->isLimit($lexer)) {
            $root->setLimit($this->getLimitParser($processor)->parse($lexer, $processor));
        }

        $update = new Ast\Stmt\Update();

        $update->setRoot($root);

        return $update;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return self::PARSER_STMT_UPDATE;
    }

}