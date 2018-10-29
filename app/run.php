<?php

use Subapp\Sql\Lexer\Lexer;

include_once __DIR__ . '/../vendor/autoload.php';

$sqlVersion = '0003';

$sql = file_get_contents(sprintf('%s/sql/%s.sql', __DIR__, $sqlVersion));

$lexer = new Lexer();

$lexer->setInput($sql);

echo PHP_EOL;

//die(var_dump($lexer));

echo "====== SQL ======\n";
echo $sql;

echo "\n====== Tokens ======\n";

/** @var \Subapp\Lexer\TokenInterface $token */
foreach ($lexer as $token) {
    echo sprintf('%s ', $lexer->getConstantName($token->getType())) . PHP_EOL;
}

$lexer->rewind();

$processor = new \Subapp\Sql\Syntax\Processor($lexer, new Subapp\Sql\Platform\MySQLPlatform());
$processor->setup(new \Subapp\Sql\Syntax\MySQL\MySQLParserSetup());

/** @var \Subapp\Sql\Ast\Statement\Select $select */
$select = $processor->parse();

$renderer = new \Subapp\Sql\Represent\Renderer();
$renderer->setup(new \Subapp\Sql\Represent\MySQL\MySQLRendererSetup());

echo "\n====== SELECT AST Object ======\n";
var_dump($select);

echo "\n====== SELECT AST Render ======\n";
echo $renderer->render($select);

$query = new \Subapp\Sql\Ast\Statement\Select();
$query->setPrimaryTable('test');

echo "\n====== SELECT AST Render ======\n";
echo $renderer->render($query);

echo "\n\n\n";