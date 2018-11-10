<?php

namespace Subapp\Sql\Ast\Stmt;

use Subapp\Sql\Ast\Collection;

/**
 * Class GroupBy
 * @package Subapp\Sql\Ast
 */
class GroupBy extends Collection
{
    
    /**
     * @return string
     */
    public function getSqlizerName()
    {
        return 'stmt.group_by';
    }
    
}