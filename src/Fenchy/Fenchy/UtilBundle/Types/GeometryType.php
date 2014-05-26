<?php

namespace Fenchy\UtilBundle\Types;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Maping Type Class for PosgGIS GEOMETRY type.
 * Enables usage of PosgreSQL'a PostGIS extension GEOMETRY type to be used by:
 *  - Doctrine
 *  - Doctrine SchemaTool in particular
 * 
 * All methods are overrides of parent class Doctrine\DBAL\Types\Type
 * Go there to view method's specifications
 * 
 */
class GeometryType extends Type 
{
    const GEOMETRY = 'geometry';
    
    public function getName() 
    {
        return self::GEOMETRY;
    }
    
    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'GEOMETRY(Point)';
    }
    
    public function canRequireSQLConversion()
    {
        return true;
    }
    
    public function convertToPHPValueSQL($sqlExpr, $platform)
    {
        return "ST_AsGeoJSON({$sqlExpr})";
    }
    
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return array();
        }
        $value = (is_resource($value)) ? stream_get_contents($value) : $value;
        return json_decode($value, true);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if(!isset($value['coordinates'][0]) || !isset($value['coordinates'][1]))
            return 'POINT(0 0)';
        
        $dbValue = 'POINT('. 
            $value['coordinates'][0].' '.$value['coordinates'][1].')';
        return $dbValue;
    }

}