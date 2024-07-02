<?php
session_start();

include_once('functions.php');

if ($_POST['submit_btn']) {

    if (isset($_FILES['FileData'])) {

        $dounloadFile = file_get_contents($_FILES['FileData']['tmp_name']);

        if ($arUnser = unserialize($dounloadFile)) {
            $message = "Стандартный метод десериализация";
            $arResult = $arUnser;
        } else {
            $patternN = '/""/';
            $replacements = '" "';
            $dounloadFile = preg_replace($patternN, $replacements,  $dounloadFile);
            $dounloadFile = rusTranslit($dounloadFile);
            $fileName = pathinfo($_FILES['FileData']['name']);

            $pattern = '/[s|O]:(\d+):"(.+?)"(;|:)/';

            $correct = preg_replace_callback(
                $pattern,
                function ($match) {
                    $length = strlen($match[2]);
                    if ($match[3] == ';') {
                        return "s:$length:\"$match[2]\";";
                    } else {
                        return "O:$length:\"$match[2]\":";
                    }
                    
                },
                $dounloadFile);

            if($arUnser = unserialize($correct)) {
                $message = "Стандартный метод десериализация, в исходном файле исправлены ошибки,<br>кирилица транслитирована";
                $arResult = $arUnser;
            } else {
                $message = "Внимание: кастомная десериализация,<br>кирилица транслитирована";

                $dounloadFile = str_replace('~', '', $dounloadFile);
                $dounloadFile = str_replace('`', '', $dounloadFile);
                $dounloadFile = str_replace('|', '', $dounloadFile);
                $dounloadFile = str_replace('^', '', $dounloadFile);

                $patterns = array();
                $patterns[0] = '/;N;/';
                $patterns[1] = '/0:{}/';

                $replacements = array();
                $replacements[0] = ';s:1:"NULL";';
                $replacements[1] = '0:{s:6:"Array";s:6:"NULL";}';

                $dounloadFile = preg_replace($patterns, $replacements, $dounloadFile);

                $patternInt = '/i:(\d+);/';

                $dounloadFile = preg_replace_callback($patternInt, function($matches) {
                    return 'i:1:"' . $matches[1] . '";';
                }, $dounloadFile);

                $patternD = '/[d]:(\d+\.?\d+?);/';

                $dounloadFile = preg_replace_callback($patternD, function($matches) {
                    return 'd:1:"' . $matches[1] . '";';
                }, $dounloadFile);

                $patternIdentify = '/("?;?[a-zA-Z]?:\d+:("|({))?)(.*?)(?=(("?;?[a-zA-Z]?:\d+:"?({)?)|\z))/';
                $patternIdentify = '/(("?;?[a-zA-Z]?:\d+:")|("?;?[a-zA-Z]?:\d+:({)))(.*?)(?=(("?;?[a-zA-Z]?:\d+:")|("?;?[a-zA-Z]?:\d+:({))|\z))/';
                $count = 0;
                $dounloadFile = preg_replace_callback($patternIdentify, function($matches) use (&$count, &$lastMatch) {
                    $count++;
                    
                    if ($count % 2 == 0) {
                            $lastMatch = $matches[5];
                            return "|" . $matches[5] . $matches[4];
                    } else {
                        if($matches[4] == '{'){
                            $count++;
                            return "~" . $lastMatch . $count . "|{";
                        } else {
                            $lastMatch = $matches[5];
                            return "~" . $matches[5]. $matches[4];
                        }
                    }
                    
                }, $dounloadFile);

                $patternOpenBreacket = '/(?<!["]){(?!["])/';
                $replacements = '`';
                $dounloadFile = preg_replace($patternOpenBreacket, $replacements, $dounloadFile);

                $patternCloseBreacket = '/(?<!["|\d])}(?!["])/';
                $replacements = '^';
                $dounloadFile = preg_replace($patternCloseBreacket, $replacements, $dounloadFile);

                $patternCloseBreacket = '/\^/';
                $replacements = '^~NULL|NULL';
                $dounloadFile = preg_replace($patternCloseBreacket, $replacements, $dounloadFile);

                $arResult = dessrial($dounloadFile);
                $arResult = removeElementByKey($arResult, "NULL");
                $arResult = margeDuble($arResult);
            }
        }
    }
}
