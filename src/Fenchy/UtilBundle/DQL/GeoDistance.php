<?php
namespace Fenchy\UtilBundle\DQL;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;

/**
 * geoDistanceFunction ::= "geoDistance" "(" StringPrimary "," StringPrimary "," StringPrimary "," StringPrimary ")"
 */
class GeoDistance extends FunctionNode
{
    public $lon1 = null;
    public $lat1 = null;
    public $lon2 = null;
    public $lat2 = null;

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->lon1 = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->lat1 = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->lon2 = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->lat2 = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return "( 6371 * ACOS( 
                    COS( RADIANS( {$this->lat1->dispatch($sqlWalker)} ) ) * COS( RADIANS( {$this->lat2->dispatch($sqlWalker)} ) ) * COS( RADIANS( {$this->lon2->dispatch($sqlWalker)} ) - RADIANS( {$this->lon1->dispatch($sqlWalker)} ) ) + SIN( RADIANS( {$this->lat1->dispatch($sqlWalker)} ) ) * SIN( RADIANS( {$this->lat2->dispatch($sqlWalker)} ))
                ))";
        return 'to_tsquery(' .
            $this->locale->dispatch($sqlWalker) . ', ' .
            $this->text->dispatch($sqlWalker) .
        ')';
    }
}