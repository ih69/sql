<?php

namespace Subapp\Sql\Query;

use Subapp\Sql\Ast;
use Subapp\Sql\Converter\ProviderInterface;
use Subapp\Sql\Exception\UnsupportedException;

/**
 * Query Builder (Facade) based on AST
 *  - use Recognizer for parsing short-part sql
 *  - with compliance fluent interface
 *
 * Class QueryBuilder
 * @package Subapp\Sql\Query
 */
class QueryBuilder
{
    
    public const SELECT = 1;
    public const DELETE = 2;
    public const UPDATE = 4;
    public const INSERT = 8;
    
    /**
     * @var Node
     */
    private $node;
    
    /**
     * @var integer
     */
    private $type = QueryBuilder::SELECT;
    
    /**
     * @var Ast\Root
     */
    private $root;
    
    /**
     * QueryBuilder constructor.
     * @param Node $node
     */
    public function __construct(Node $node)
    {
        $this->node = $node;
        $this->root = new Ast\Root();
    }
    
    /**
     * @param int $type
     * @return $this
     */
    public function reset($type = QueryBuilder::SELECT)
    {
        $this->setRoot(new Ast\Root());
        
        return $this;
    }
    
    /**
     * @return $this
     */
    public function asSelect()
    {
        $this->setType(QueryBuilder::SELECT);
        
        return $this;
    }
    
    /**
     * @return $this
     */
    public function asDelete()
    {
        $this->setType(QueryBuilder::DELETE);
        
        return $this;
    }
    
    /**
     * @return $this
     */
    public function asUpdate()
    {
        $this->setType(QueryBuilder::UPDATE);
        
        return $this;
    }
    
    /**
     * @return $this
     */
    public function asInsert()
    {
        $this->setType(QueryBuilder::INSERT);
        
        return $this;
    }
    
    /**
     * @param string|Ast\NodeInterface ...$arguments
     * @return $this
     */
    public function select(...$arguments)
    {
        $this->type = QueryBuilder::SELECT;
        
        $this->arguments(...$arguments);
        
        return $this;
    }
    
    /**
     * @param string|Ast\NodeInterface $table
     * @return $this
     */
    public function insert($table)
    {
        $this->type = QueryBuilder::INSERT;
        
        $this->root->setTable($this->node->recognize($table));
        
        return $this;
    }
    
    /**
     * @return $this
     */
    public function delete()
    {
        $this->type = QueryBuilder::DELETE;
        
        return $this;
    }
    
    /**
     * @param array|null $assignments
     * @return $this
     */
    public function update(array $assignments = null)
    {
        $this->type = QueryBuilder::UPDATE;
        
        $this->sets($assignments);
        
        return $this;
    }
    
    /**
     * @return $this
     */
    public function quick()
    {
        $this->root->modifiers()->add(Ast\Modifiers::MODIFIER_QUICK);
        
        return $this;
    }
    
    /**
     * @return $this
     */
    public function ignore()
    {
        $this->root->modifiers()->add(Ast\Modifiers::MODIFIER_IGNORE);
        
        return $this;
    }
    
    /**
     * @return $this
     */
    public function distinct()
    {
        $this->root->modifiers()->add(Ast\Modifiers::MODIFIER_DISTINCT);
        
        return $this;
    }
    
    /**
     * @return $this
     */
    public function delayed()
    {
        $this->root->modifiers()->add(Ast\Modifiers::MODIFIER_DELAYED);
        
        return $this;
    }
    
    /**
     * @return $this
     */
    public function noCache()
    {
        $this->root->modifiers()->add(Ast\Modifiers::MODIFIER_SQL_NO_CACHE);
        
        return $this;
    }
    
    /**
     * @param string|Ast\NodeInterface ...$arguments
     * @return $this
     */
    public function arguments(...$arguments)
    {
        /** @var Ast\Arguments $arguments */
        $arguments = $this->node->recognize($arguments);
        $this->root->setArguments($arguments);
        
        return $this;
    }
    
    /**
     * @param $source
     * @param $alias
     * @return $this
     */
    public function from($source, $alias = null)
    {
        $table = $this->root->tableReference();
        $variable = $this->node->variable($source, $alias);
        
        $table->setPrefix('FROM');
        $table->append($variable);
        
        return $this;
    }
    
    /**
     * @param             $name
     * @param null|string $alias
     * @return QueryBuilder
     */
    public function table($name, $alias = null)
    {
        return $this->from($name, $alias);
    }
    
    /**
     * @param string|Ast\Condition\AbstractPredicate $x
     * @param boolean                                $clear
     * @return $this
     */
    public function where($x, $clear = true)
    {
        return $this->condition($this->root->where(), $x, $clear);
    }
    
    /**
     * @param string|Ast\Condition\AbstractPredicate $e
     * @param boolean                                $clear
     * @return $this
     */
    public function having($e, $clear = true)
    {
        return $this->condition($this->root->having(), $e, $clear);
    }
    
    /**
     * @param Ast\Condition\Conditions $conditions
     * @param Ast\NodeInterface|string $e
     * @param bool                     $clear
     * @return $this
     */
    private function condition(Ast\Condition\Conditions $conditions, $e, $clear = true)
    {
        $outer = $conditions->getOperator();
        
        $predicates = $this->node()->recognize($e);
        
        if ($predicates instanceof Ast\Condition\Conditions) {
            $inner = $predicates->getOperator();
            $ifGreaterThanOne = ($predicates->count() > 1);
            $predicates->setWrapped(!($outer->getOperator() == $inner->getOperator()) && $ifGreaterThanOne);
        }
        
        if ($clear) {
            $conditions->clear();
        }
        
        $conditions->append($predicates);
        
        return $this;
    }
    
    /**
     * @param $table
     * @param $a
     * @param $condition
     * @return QueryBuilder
     */
    public function crossJoin($table, $a, $condition)
    {
        return $this->join($table, $a, $condition, Ast\Stmt\Join::CROSS);
    }
    
    /**
     * @param string                   $table
     * @param string                   $a
     * @param string|Ast\NodeInterface $condition
     * @return QueryBuilder
     */
    public function rightJoin($table, $a, $condition)
    {
        return $this->join($table, $a, $condition, Ast\Stmt\Join::RIGHT);
    }
    
    /**
     * @param string                   $table
     * @param string                   $a
     * @param string|Ast\NodeInterface $condition
     * @return QueryBuilder
     */
    public function leftJoin($table, $a, $condition)
    {
        return $this->join($table, $a, $condition, Ast\Stmt\Join::LEFT);
    }
    
    /**
     * @param string                   $table
     * @param string                   $a
     * @param string|Ast\NodeInterface $condition
     * @return QueryBuilder
     */
    public function innerJoin($table, $a, $condition)
    {
        return $this->join($table, $a, $condition, Ast\Stmt\Join::INNER);
    }
    
    /**
     * @param string                   $table
     * @param string                   $a
     * @param string|Ast\NodeInterface $condition
     * @param string                   $type
     * @return $this
     */
    public function join($table, $a, $condition, $type = Ast\Stmt\Join::INNER)
    {
        $join = new Ast\Stmt\Join($type);
        $node = $this->getNode();
        
        $this->root->getJoins()->append($join);
        
        $condition = $node->recognize($condition);
        
        $join->setLeft($node->path($table, $a));
        $join->setConditionType(($condition instanceof Ast\Arguments) ? Ast\Stmt\Join::USING : Ast\Stmt\Join::ON);
        $join->setCondition($condition);
        
        return $this;
    }
    
    /**
     * @param string|Ast\NodeInterface $left
     * @param string|Ast\NodeInterface $value
     * @return $this
     */
    public function assignment($left, $value)
    {
        $this->root->assignment()->append(
            $this->node->assignment($left, $value)
        );
        
        return $this;
    }
    
    /**
     * @param array|string[]|Ast\NodeInterface[] $assignments
     * @return $this
     */
    public function sets(array $assignments)
    {
        foreach ($assignments as $left => $value) {
            $this->assignment($left, $value);
        }
        
        return $this;
    }
    
    /**
     * @param string|Ast\NodeInterface $left
     * @param string|Ast\NodeInterface $value
     * @return $this
     */
    public function set($left, $value)
    {
        return $this->assignment($left, $value);
    }
    
    /**
     * @param string|Ast\NodeInterface ...$variables
     * @return $this
     */
    public function group(...$variables)
    {
        /** @var Ast\Arguments $variables */
        $variables = $this->node->recognize($variables);
        $this->root->groupBy()->asBatch($variables->toArray());
        
        return $this;
    }
    
    /**
     * @param string|Ast\NodeInterface ...$arguments
     * @return $this
     */
    public function order(...$arguments)
    {
        $orderByNode = $this->root->orderBy();
        
        /** @var Ast\Arguments $arguments */
        $arguments = $this->node->recognize($arguments);
        
        // @todo perhaps exist most elegant solution
        $arguments->each(function ($index, Ast\Stmt\OrderByItems $collection) use ($orderByNode) {
            $orderByNode->asBatch($collection->toArray());
            return true;
        });
        
        return $this;
    }
    
    /**
     * @param integer $length
     * @param integer $offset
     * @return $this
     */
    public function limit($length, $offset = 0)
    {
        $limit = $this->root->limit();
        
        $limit->setLength($this->node->int($length));
        $limit->setOffset($this->node->int($offset));
        
        return $this;
    }
    
    /**
     * @return Ast\Stmt\Delete|Ast\Stmt\Select|Ast\Stmt\Update|Ast\Stmt\Insert
     * @throws UnsupportedException
     */
    public function getAst()
    {
        /** @var Ast\Stmt\Select|Ast\Stmt\Delete|Ast\Stmt\Update $ast */
        $ast = null;
        
        switch ($this->type) {
            case QueryBuilder::SELECT:
                $ast = new Ast\Stmt\Select();
                break;
            case QueryBuilder::DELETE:
                $ast = new Ast\Stmt\Delete();
                break;
            case QueryBuilder::UPDATE:
                $ast = new Ast\Stmt\Update();
                break;
            case QueryBuilder::INSERT:
                $ast = new Ast\Stmt\Insert();
                break;
            default:
                throw new UnsupportedException(sprintf('Cannot create AST node for type: "%s"',
                    $this->getType()));
        }
        
        $ast->setRoot($this->root);
        
        return $ast;
    }
    
    /**
     * @param ProviderInterface $renderer
     * @return string
     */
    public function getQuery(ProviderInterface $renderer)
    {
        return $renderer->toSql($this->getAst());
    }
    
    
    /**
     * @return Node
     */
    public function getNode()
    {
        return $this->node;
    }
    
    /**
     * Alias: $this->getNode(): Query\NodeBuilder
     *
     * @return Node
     */
    public function node()
    {
        return $this->getNode();
    }
    
    /**
     * @return Ast\Root
     */
    public function getRoot()
    {
        return $this->root;
    }
    
    /**
     * @param Ast\Root $root
     */
    public function setRoot(Ast\Root $root)
    {
        $this->root = $root;
    }
    
    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
    
}