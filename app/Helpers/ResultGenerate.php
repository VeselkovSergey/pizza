<?php


namespace App\Helpers;


class ResultGenerate
{
    /**
     * @param object|array $object
     * @param bool $status
     * @param string $message
     * @return bool|string
     */
    public static function Success(string $message = 'Успешно!', object|array $object = [], bool $status = true): string
    {
        return json_encode((object)[
            'status' => $status,
            'message' => $message,
            'result' => $object,
        ]);
    }

    /**
     * @param object|array $object
     * @param bool $status
     * @param string $message
     * @return bool|string
     */
    public static function Error(string $message = 'Ошибка!', object|array $object = [], bool $status = false): string
    {
        return json_encode((object)[
            'status' => $status,
            'message' => $message,
            'result' => $object,
        ]);
    }
}
