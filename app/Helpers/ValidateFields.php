<?php


namespace App\Helpers;


class ValidateFields
{
    public static function NullAndIsset($fields, $validateFields)
    {
        foreach ($fields as $key => $field) {
            if (in_array($key, $validateFields)) {
                if ($field === '' || $field === null || !isset($field)) {
                    return false;
                }
            }
        }

        return true;
    }
}
