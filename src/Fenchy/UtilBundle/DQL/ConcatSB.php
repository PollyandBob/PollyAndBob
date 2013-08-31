<?php
namespace Fenchy\UtilBundle\DQL;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;

/**
 * Concat space between
 * ConcatSBFunction ::= "concatsb" "(" StringPrimary "," StringPrimary ")"
 */
class ConcatSB extends FunctionNode
{
    public $string1 = null;
    public $string2 = null;

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->string1 = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->string2 = $parser->StringPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return 'concat(' .
            $this->string1->dispatch($sqlWalker) . ', \' \', ' .
            $this->string2->dispatch($sqlWalker) .
        ')';
    }
}