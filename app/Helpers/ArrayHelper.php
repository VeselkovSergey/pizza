<?php


namespace App\Helpers;


class ArrayHelper
{
    public static function ObjectToArray($obj)
    {
        if (is_object($obj)) {
            $obj = (array)$obj;
        }

        if (is_array($obj)) {
            $new = array();
            foreach ($obj as $key => $val) {
                $new[$key] = self::ObjectToArray($val);
            }
        } else {
            $new = $obj;
        }

        return $new;
    }
}
