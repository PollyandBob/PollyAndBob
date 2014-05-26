<?php

namespace Fenchy\UtilBundle\DQL;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;

/**
 * postGIS_KNN_OperatorFunction ::= "postGIS_KNN_Operator" "(" StringPrimary "," StringPrimary "," StringPrimary ")"
 * 
 * DQL function definition created to make use of PostGIS <-> operator.
 * 
 * Example resulting SQL:
 * pgisgeom <-> ST_GeometryFromText('POINT(22.014257 50.025891)')
 */
class PostGISKNNOperator extends FunctionNode
{
    public $lon = null;
    public $lat = null;
    public $point = null;

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->lon = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->lat = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->point = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return $this->point->dispatch($sqlWalker).' <-> ST_GeometryFromText(\'POINT('.
            $this->lon->dispatch($sqlWalker) 
            .' '.
            $this->lat->dispatch($sqlWalker)
            .')\')';        
    }
}

