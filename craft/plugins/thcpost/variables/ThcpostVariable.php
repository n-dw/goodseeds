<?php


namespace Craft;

class ThcpostVariable
{
    public function hashValue($arr)
    {
        return craft()->thcpost_thcpost->hashValue($arr);
    }
}