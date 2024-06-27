<?php

function dessrial($str) {
    $BreckDel = false;
    $breckC = 0;
    $Brecerts = false;
    $firstBreck = false;
    $arr = array();
    $key = '';
    $len = strlen($str);
    for ($i = 0; $i <= $len; $i++) {
        if ($str[$i]  == '~' && !$Brecerts) {
            $i = $i + 1;
            $keyflag = false;
            $key = '';
        }

        if ($str[$i] != '|' && !$keyflag) {
            $key = $key . $str[$i];
        } else {
            $keyflag = true;
        }
        if ($keyflag && $str[$i] != '|') {
            if(($str[$i] == '`') && !$firstBreck && !$BreckDel) {
                $Brecerts = true;
                $firstBreck = true;
                $breckC = $breckC + 1;
            } elseif ($str[$i] == '`' && $BreckDel){
                $breckC = $breckC + 1;
            }

            if ($firstBreck) {
                $firstBreck = false;
                $BreckDel =true;
                continue;
            }
            if ($str[$i] == '^') {
                $breckC = $breckC - 1;
                if ($breckC == 0) {
                    $firstBreck = false;
                    $Brecerts = false;
                    $BreckDel = false;
                    $arr[$key] = dessrial($arr[$key]);
                    continue;
                }
            }
            $arr[$key] = $arr[$key] . $str[$i];
        } elseif ($keyflag && $Brecerts) {
            $arr[$key] = $arr[$key] . $str[$i];
        }
    }
    return $arr;
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
