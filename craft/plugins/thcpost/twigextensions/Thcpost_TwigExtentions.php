<?php

namespace Craft;

class Thcpost_TwigExtensions extends \Twig_Extension
{
    protected $env;

    private static function effectsSort($a, $b){
        return ($a['percentage'] < $b['percentage']) ? 1 : -1;
    }

    public function getName()
    {
        return 'Custom THCPOST Twig extentions';
    }

    public function getFilters()
    {
        return array('thcsort' => new \Twig_Filter_Method($this, 'twig_sort'));
    }

    public function initRuntime(\Twig_Environment $env)
    {
        $this->env = $env;
    }

    public function twig_sort($array, $method='asort', $sort_flag='SORT_REGULAR')
    {
        settype($sort_flag, 'integer');

        switch ($method)
        {
            case 'effectssort':
                usort($results, 'self::effectsSort');
                break;

            case 'asort':
                asort($array, $sort_flag);
                break;

            case 'arsort':
                arsort($array, $sort_flag);
                break;

            case 'krsort':
                krsort($array, $sort_flag);
                break;

            case 'ksort':
                ksort($array, $sort_flag);
                break;

            case 'natcasesort':
                natcasesort($array);
                break;

            case 'natsort':
                natsort($array);
                break;

            case 'rsort':
                rsort($array, $sort_flag);
                break;

            case 'sort':
                sort($array, $sort_flag);
                break;
        }

        return $array;
    }

}