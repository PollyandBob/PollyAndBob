<?php
namespace Fenchy\UtilBundle\DQL;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;

/**
 * ToTsVectorFunction ::= "to_tsvector" "(" StringPrimary "," StringPrimary ")"
 */
class ToTsVector extends FunctionNode
{
    public $locale = null;
    public $text = null;

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->locale = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->text = $parser->StringPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
        
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return 'to_tsvector(' .
            $this->locale->dispatch($sqlWalker) . ', ' .
            $this->text->dispatch($sqlWalker) .
        ')';
    }
}