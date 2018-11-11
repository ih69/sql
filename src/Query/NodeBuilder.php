<?php

namespace Subapp\Sql\Query;

use Subapp\Sql\Ast\Condition;
use Subapp\Sql\Ast;
use Subapp\Sql\Ast\ExpressionInterface;

/**
 * Class ExpressionBuilder
 * @package Subapp\Sql\Query
 */
class NodeBuilder
{
    
    /**
     * @var Recognizer
     */
    private $recognizer;
    
    /**
     * @param $sql
     * @return ExpressionInterface
     */
    public function recognize($sql)
    {
        switch (true) {
            case ($sql instanceOf ExpressionInterface):
                return $sql;
            
            case is_numeric($sql):
                $isFloat = (strpos($sql, '.') !== false);
                return $this->literal($sql, $isFloat ? Ast\Literal::FLOAT : Ast\Literal::INT);
            
            case is_array($sql):
                $sql = array_map(function ($value) {
                    return $this->recognize($value);
                }, $sql);
                return $this->arguments(...$sql);
            
            default:
                return $this->recognizer->recognize($sql);
        }
    }
    
    /**
     * @param                         $operator
     * @param Ast\ExpressionInterface ...$terms
     * @return Condition\Conditions
     */
    public function terms($operator, ...$terms)
    {
        $collection = new Condition\Conditions();
        $operator = $this->logic($operator);
        
        foreach ($terms as $term) {
            $collection->append(new Condition\Term($operator, $this->recognize($term)));
        }
        
        /** @var Condition\Term $last */
        $last = $collection->get($collection->count() - 1);
        $last->setOperator(null);
        
        return $collection;
    }
    
    /**
     * @param Ast\ExpressionInterface ...$terms
     * @return Condition\Conditions
     */
    public function and(...$terms)
    {
        return $this->terms(Condition\LogicOperator:: AND, ...$terms);
    }
    
    /**
     * @param Ast\ExpressionInterface ...$terms
     * @return Condition\Conditions
     */
    public function or(...$terms)
    {
        return $this->terms(Condition\LogicOperator:: OR, ...$terms);
    }
    
    /**
     * @param Ast\ExpressionInterface ...$terms
     * @return Condition\Conditions
     */
    public function xor(...$terms)
    {
        return $this->terms(Condition\LogicOperator:: XOR, ...$terms);
    }
    
    /**
     * @param $x
     * @param $operator
     * @param $y
     * @return Condition\Cmp
     */
    public function comparison($x, $operator, $y)
    {
        return new Condition\Cmp($this->recognize($x), $this->cmp($operator), $this->recognize($y));
    }
    
    /**
     * @param $x
     * @param $y
     * @return Condition\Cmp
     */
    public function eq($x, $y)
    {
        return $this->comparison($x, Condition\Operator::EQ, $y);
    }
    
    /**
     * @param $x
     * @param $y
     * @return Condition\Cmp
     */
    public function ne($x, $y)
    {
        return $this->comparison($x, Condition\Operator::NE, $y);
    }
    
    /**
     * @param $x
     * @param $y
     * @return Condition\Cmp
     */
    public function gt($x, $y)
    {
        return $this->comparison($x, Condition\Operator::GT, $y);
    }
    
    /**
     * @param $x
     * @param $y
     * @return Condition\Cmp
     */
    public function ge($x, $y)
    {
        return $this->comparison($x, Condition\Operator::GE, $y);
    }
    
    /**
     * @param $x
     * @param $y
     * @return Condition\Cmp
     */
    public function lt($x, $y)
    {
        return $this->comparison($x, Condition\Operator::LT, $y);
    }
    
    /**
     * @param $x
     * @param $y
     * @return Condition\Cmp
     */
    public function le($x, $y)
    {
        return $this->comparison($x, Condition\Operator::LE, $y);
    }
    
    /**
     * @param         $x
     * @param         $in
     * @param boolean $not
     * @return Condition\In
     */
    public function in($x, $in, $not = false)
    {
        return new Condition\In($not, $this->recognize($x), $this->recognize($in));
    }
    
    /**
     * @param         $x
     * @param boolean $not
     * @return Condition\IsNull
     */
    public function isNull($x, $not = false)
    {
        return new Condition\IsNull($not, $this->recognize($x));
    }
    
    /**
     * @param        $left
     * @param string $a
     * @param string $b
     * @return Condition\Between
     */
    public function between($left, $a, $b)
    {
        $between = new Condition\Between(false, $this->recognize($left));
        
        $between->setBetweenA($this->string($a));
        $between->setBetweenB($this->string($b));
        
        return $between;
    }
    
    /**
     * @param string $field
     * @return Ast\Identifier
     */
    public function field($field)
    {
        return $this->identifier($field);
    }
    
    /**
     * @param string $table
     * @return Ast\Identifier
     */
    public function table($table)
    {
        return $this->identifier($table);
    }
    
    /**
     * @param $table
     * @param $field
     * @return Ast\FieldPath
     */
    public function path($table, $field)
    {
        return new Ast\FieldPath($this->table($table), $this->field($field));
    }
    
    /**
     * @param ExpressionInterface ...$values
     * @return Ast\Arguments
     */
    public function arguments(ExpressionInterface ...$values)
    {
        return new Ast\Arguments($values);
    }
    
    /**
     * @param string $value
     * @param int    $type
     * @return Ast\Literal
     */
    public function literal($value, $type = Ast\Literal::STRING)
    {
        return new Ast\Literal($value, $type);
    }
    
    /**
     * @param string $value
     * @return Ast\Literal
     */
    public function string($value)
    {
        return $this->literal($value);
    }
    
    /**
     * @param $value
     * @return Ast\Literal
     */
    public function float($value)
    {
        return $this->literal((float)$value, Ast\Literal::FLOAT);
    }
    
    /**
     * @param $value
     * @return Ast\Literal
     */
    public function int($value)
    {
        return $this->literal((integer)$value, Ast\Literal::INT);
    }
    
    /**
     * @return Ast\Literal
     */
    public function true()
    {
        return $this->literal(true, Ast\Literal::BOOLEAN);
    }
    
    /**
     * @return Ast\Literal
     */
    public function false()
    {
        return $this->literal(false, Ast\Literal::BOOLEAN);
    }
    
    /**
     * @return Ast\Literal
     */
    public function null()
    {
        return $this->literal(null, Ast\Literal::NULL);
    }
    
    /**
     * @param string $identifier
     * @return Ast\Identifier
     */
    public function identifier($identifier)
    {
        return new Ast\Identifier($identifier);
    }
    
    /**
     * @param string $name
     * @return Ast\Parameter
     */
    public function named($name)
    {
        return new Ast\Parameter(Ast\Parameter::NAMED, $name);
    }
    
    /**
     * @return Ast\Parameter
     */
    public function unnamed()
    {
        return new Ast\Parameter();
    }
    
    /**
     * @param $x
     * @param $operator
     * @param $y
     * @return Ast\Arithmetic
     */
    public function arithmetic($x, $operator, $y)
    {
        $arithmetic = new Ast\Arithmetic();
        
        $arithmetic->append($this->int($x));
        $arithmetic->append($this->math($operator));
        $arithmetic->append($this->int($y));
        
        return $arithmetic;
    }
    
    /**
     * @param string $operator
     * @return Condition\Operator
     */
    public function cmp($operator)
    {
        return new Condition\Operator($operator);
    }
    
    /**
     * @param string $operator
     * @return Condition\LogicOperator
     */
    public function logic($operator)
    {
        return new Condition\LogicOperator($operator);
    }
    
    /**
     * @param $operator
     * @return Ast\MathOperator
     */
    public function math($operator)
    {
        return new Ast\MathOperator($operator);
    }
    
    /**
     * @param $string
     * @return Ast\Raw
     */
    public function raw($string)
    {
        return new Ast\Raw($string);
    }
    
    /**
     * @return Recognizer
     */
    public function getRecognizer()
    {
        return $this->recognizer;
    }
    
    /**
     * @param Recognizer $recognizer
     */
    public function setRecognizer(Recognizer $recognizer)
    {
        $this->recognizer = $recognizer;
    }
    
}