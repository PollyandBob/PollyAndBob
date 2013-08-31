<?php
namespace Fenchy\UtilBundle\DQL;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;

/**
 * TsRankCdFunction ::= "ts_rank_cd" "(" StringPrimary  "," StringPrimary ")" 
 */
class TsRankCd extends FunctionNode
{
    public $vector = null;
    public $query = null;

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->vector = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->query = $parser->StringPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return  'ts_rank_cd('.$this->vector->dispatch($sqlWalker) . ', ' .
            $this->query->dispatch($sqlWalker).')';
    }
}