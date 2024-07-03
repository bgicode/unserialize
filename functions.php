<?php

function dessrial($string)
{
    $bracketsDel = false;
    $bracketsOpenCount = 0;
    $brackets = false;
    $firstBrackets = false;
    $arUnseria = array();
    $key = '';
    $len = strlen($string);

    for ($i = 0; $i <= $len; $i++) {

        if ($string[$i]  == '~' && !$brackets) {
            $i = $i + 1;
            $keyflag = false;
            $key = '';
        }

        if ($string[$i] != '|' && !$keyflag) {
            $key = $key . $string[$i];
        } else {
            $keyflag = true;
        }

        if ($keyflag && $string[$i] != '|') {
            if(($string[$i] == '`') && !$firstBrackets && !$bracketsDel) {
                $brackets = true;
                $firstBrackets = true;
                $bracketsOpenCount = $bracketsOpenCount + 1;
            } elseif ($string[$i] == '`' && $bracketsDel){
                $bracketsOpenCount = $bracketsOpenCount + 1;
            }

            if ($firstBrackets) {
                $firstBrackets = false;
                $bracketsDel =true;
                continue;
            }

            if ($string[$i] == '^') {
                $bracketsOpenCount = $bracketsOpenCount - 1;
                if ($bracketsOpenCount == 0) {
                    $firstBrackets = false;
                    $brackets = false;
                    $bracketsDel = false;
                    $arUnseria[$key] = dessrial($arUnseria[$key]);
                    continue;
                }
            }

            $arUnseria[$key] = $arUnseria[$key] . $string[$i];

        } elseif ($keyflag && $brackets) {
            $arUnseria[$key] = $arUnseria[$key] . $string[$i];
        }

    }
    return $arUnseria;
}

function rusTranslit($string)
{
    $converter = [
        'а' => 'a',   'б' => 'b',   'в' => 'v',
        'г' => 'g',   'д' => 'd',   'е' => 'e',
        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
        'и' => 'i',   'й' => 'y',   'к' => 'k',
        'л' => 'l',   'м' => 'm',   'н' => 'n',
        'о' => 'o',   'п' => 'p',   'р' => 'r',
        'с' => 's',   'т' => 't',   'у' => 'u',
        'ф' => 'f',   'х' => 'h',   'ц' => 'c',
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
        'ь' => '',    'ы' => 'y',   'ъ' => '',
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

        'А' => 'A',   'Б' => 'B',   'В' => 'V',
        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
        'И' => 'I',   'Й' => 'Y',   'К' => 'K',
        'Л' => 'L',   'М' => 'M',   'Н' => 'N',
        'О' => 'O',   'П' => 'P',   'Р' => 'R',
        'С' => 'S',   'Т' => 'T',   'У' => 'U',
        'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
        'Ь' => '',    'Ы' => 'Y',   'Ъ' => '',
        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
    ];
    
    return strtr($string, $converter);
}

function removeElementByKey($array, $key) {
    foreach ($array as $arrayKey => $value) {
        if ($arrayKey === $key) {
            unset($array[$key]);
        }
        if (is_array($value)) {
            $array[$arrayKey] = removeElementByKey($value, $key);
        }
    }
    return $array;
}

function margeDuble($array)
{
    if (is_array($array)) {
        $newArray = array();
        $prevKey = '';
        
        foreach ($array as $key => $value) {
            if (preg_match('/^(Bitrix[^\d]+)(\d+)$/', $key, $matches)) {
                if (!empty($prevKey)) {
                    $newArray[$prevKey] = [];
                    $newArray[$prevKey][$matches[1]] = margeDuble($value);
                }
            } else {
                $newArray[$key] = margeDuble($value);
                $prevKey = $key;
            }
        }

        return $newArray;

    } else {

        return $array;
    }
}
