<?php


namespace App\Helpers;


class StringHelper
{
    const DAY_NAME = [
        0 => 'Воскресенье',
        1 => 'Понедельник',
        2 => 'Вторник',
        3 => 'Среда',
        4 => 'Четверг',
        5 => 'Пятница',
        6 => 'Суббота',
    ];

    const MONTHS = [
        '-',
        'января',
        'февраля',
        'марта',
        'aпреля',
        'мая',
        'июня',
        'июля',
        'августа',
        'сентября',
        'октября',
        'ноября',
        'декабря',
    ];

    public static function TransliterateURL(string $value): string
    {
        $converter = array(
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
            'е' => 'e', 'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
            'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
            'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'sch', 'ь' => '', 'ы' => 'y', 'ъ' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
        );

        $value = mb_strtolower($value);
        $value = strtr($value, $converter);
        $value = mb_ereg_replace('[^-0-9a-z]', '-', $value);
        $value = mb_ereg_replace('[-]+', '-', $value);
        $value = trim($value, '-');

        return $value;
    }

    public static function JsonDecode(string $json)
    {
        return json_decode($json);
    }

    public static function PhoneBeautifulFormat(string $phoneRawFormat)
    {
        $phoneBeautifulFormat = '';
        for ($i = 0; $i < strlen($phoneRawFormat); $i++) {
            if ($i === 0) {
                $phoneBeautifulFormat .= '+' . $phoneRawFormat[$i] . '(';
            } else if ($i === 4) {
                $phoneBeautifulFormat .= ')' . $phoneRawFormat[$i];
            }  else if ($i === 7 || $i === 9 || $i === 11) {
                $phoneBeautifulFormat .= '-' . $phoneRawFormat[$i];
            } else {
                $phoneBeautifulFormat .= $phoneRawFormat[$i];
            }

        }
        return $phoneBeautifulFormat;
    }

    public static function DateBeautifulFormat(string $dateRawFormat)
    {
        $dateRawFormat = strtotime($dateRawFormat);
        $month = date('m', $dateRawFormat);
        $monthText = self::MONTHS[(int)$month];
        return date('d ' . $monthText . ' Y' . ' H:i');
    }
}
