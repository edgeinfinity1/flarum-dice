<?php
namespace EdgeInfinity\Dice;
use InvalidArgumentException;
final class DiceConfig
{
    public static function defaults(): array { return json_decode(file_get_contents(__DIR__.'/../resources/defaults.json'),true,512,JSON_THROW_ON_ERROR); }
    public static function decode(mixed $value): array
    { try { return self::validate($value ?: self::defaults()); } catch (InvalidArgumentException $e) { return self::defaults(); } }
    public static function validate(mixed $value): array
    {
        if (is_string($value)) { if(strlen($value)>20000) throw new InvalidArgumentException('Configuration is too large.'); $value=json_decode($value,true); }
        if (!is_array($value)) throw new InvalidArgumentException('Configuration must be a JSON object.');
        $defaults=self::defaults();
        if(array_diff(array_keys($value),array_keys($defaults))) throw new InvalidArgumentException('Unknown configuration key.');
        $out=array_replace($defaults,$value);
        foreach (['maxDice'=>[1,100],'criticalMinFaces'=>[2,2000000001],'criticalPercent'=>[1,50],'digitsMin'=>[2,10]] as $k=>[$min,$max])
            if(!is_int($out[$k]) || $out[$k]<$min || $out[$k]>$max) throw new InvalidArgumentException($k.' is outside the allowed integer range.');
        foreach(['criticalEnabled','successHigh','signEnabled','repeatEnabled','sequenceEnabled','specialEnabled'] as $k)
            if(!is_bool($out[$k])) throw new InvalidArgumentException($k.' must be a boolean.');
        if(!is_array($out['specialNumbers']) || !array_is_list($out['specialNumbers']) || count($out['specialNumbers'])>100) throw new InvalidArgumentException('Use at most 100 special numbers.');
        foreach($out['specialNumbers'] as $n) if(!is_int($n) || abs($n)>DiceSyntax::LIMIT) throw new InvalidArgumentException('Special numbers must be supported integers.');
        $a=$out['priority']; $b=$defaults['priority'];
        if(!is_array($a)||!array_is_list($a)) throw new InvalidArgumentException('Priority must be an array.');
        sort($a);sort($b);if($a!==$b) throw new InvalidArgumentException('Priority must contain every effect exactly once.');
        if(!is_array($out['colors']) || array_diff(array_keys($out['colors']),array_keys($defaults['colors']))) throw new InvalidArgumentException('Unknown color category.');
        foreach ($out['colors'] as $palette) if (!is_array($palette)) throw new InvalidArgumentException('Color categories must be objects.');
        foreach($defaults['colors'] as $effect=>$themes) foreach($themes as $theme=>$properties) {
            $colors=$out['colors'][$effect][$theme]??[];
            if(!is_array($colors)||array_diff(array_keys($colors),array_keys($properties))) throw new InvalidArgumentException('Unknown color property.');
            foreach($properties as $property=>$fallback) {
                $c=$colors[$property]??$fallback;
                if(!is_string($c)||($c!==''&&!preg_match('/^#(?:[0-9a-f]{3}|[0-9a-f]{4}|[0-9a-f]{6}|[0-9a-f]{8})$/iD',$c))) throw new InvalidArgumentException('Colors must be hexadecimal or empty.');
                $out['colors'][$effect][$theme][$property]=$c;
            }
            if(array_diff(array_keys($out['colors'][$effect]),['light','dark'])) throw new InvalidArgumentException('Unknown color theme.');
        }
        return $out;
    }
}
