<?php
namespace Fenchy\UtilBundle\DQL;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;

/**
 * SimilarityFunction ::= "similarity" "(" StringPrimary "," StringPrimary ")"
 */
class Similarity extends FunctionNode
{
    public $string = null;
    public $text = null;

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->text = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->string = $parser->StringPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return 'similarity(' .
            $this->text->dispatch($sqlWalker) . ', ' .
            $this->string->dispatch($sqlWalker) .
        ')';
    }
}