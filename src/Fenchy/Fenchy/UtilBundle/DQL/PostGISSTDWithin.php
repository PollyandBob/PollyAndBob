<?php

namespace Fenchy\UtilBundle\DQL;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;


/**
 * postGIS_ST_DWithinFunction ::= "postGIS_ST_DWithin" "(" StringPrimary "," StringPrimary "," StringPrimary "," StringPrimary ")"
 * 
 * DQL function definition created to make use of PostGIS ST_DWithin function.
 * 
 * Example resulting SQL:
 * ST_DWithin( punkt_sph, ST_GeographyFromText('POINT(22.0154 50.025532)'), 5000 )
 */
class PostGISSTDWithin extends FunctionNode
{
    public $lon = null;
    public $lat = null;
    public $point = null;
    public $distance = null;

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->lon = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->lat = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->point = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->distance = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return 'ST_DWithin( '.$this->point->dispatch($sqlWalker).
            ', ST_GeographyFromText(\'POINT('.
            $this->lon->dispatch($sqlWalker)
            .' '.
            $this->lat->dispatch($sqlWalker)
            .')\'), '.
            $this->distance->dispatch($sqlWalker)
            .' )';
    }
}
