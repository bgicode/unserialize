<?php
session_start();

include_once('functions.php');

if ($_POST['submit_btn']) {

    if (isset($_FILES['FileData'])) {

        $uploadFile = file_get_contents($_FILES['FileData']['tmp_name']);

        if ($arUnser = unserialize($uploadFile)) {
            $message = "Стандартный метод десериализация";
            $arResult = $arUnser;
        } else {
            $patternEptyString = '/""/';
            $replacements = '" "';
            $uploadFile = preg_replace($patternEptyString, $replacements, $uploadFile);
            $uploadFile = rusTranslit($uploadFile);
            $fileName = pathinfo($_FILES['FileData']['name']);

            $patternIdents = '/[s|O]:(\d+):"(.+?)"(;|:)/';

            $correct = preg_replace_callback(
                $patternIdents,
                function ($match) {
                    $length = strlen($match[2]);
                    if ($match[3] == ';') {
                        return "s:$length:\"$match[2]\";";
                    } else {
                        return "O:$length:\"$match[2]\":";
                    }
                    
                },
                $uploadFile);

            if($arUnser = unserialize($correct)) {
                $message = "Стандартный метод десериализация, в исходном файле исправлены ошибки,<br>кирилица транслитирована";
                $arResult = $arUnser;
            } else {
                $message = "Внимание: кастомная десериализация,<br>кирилица транслитирована";

                $uploadFile = str_replace('~', '', $uploadFile);
                $uploadFile = str_replace('`', '', $uploadFile);
                $uploadFile = str_replace('|', '', $uploadFile);
                $uploadFile = str_replace('^', '', $uploadFile);

                $patterns = array();
                $patterns[0] = '/;N;/';
                $patterns[1] = '/0:{}/';

                $replacements = array();
                $replacements[0] = ';s:1:"NULL";';
                $replacements[1] = '0:{s:6:"Array";s:6:"NULL";}';

                $uploadFile = preg_replace($patterns, $replacements, $uploadFile);

                $patternInt = '/i:(\d+);/';
                $uploadFile = preg_replace_callback($patternInt, function($matches) {
                    return 'i:1:"' . $matches[1] . '";';
                }, $uploadFile);

                $patternD = '/[d]:(\d+\.?\d+?);/';
                $uploadFile = preg_replace_callback($patternD, function($matches) {
                    return 'd:1:"' . $matches[1] . '";';
                }, $uploadFile);

                $patternIdentsAll = '/("?;?[a-zA-Z]?:\d+:("|({))?)(.*?)(?=(("?;?[a-zA-Z]?:\d+:"?({)?)|\z))/';
                $patternIdentsAll = '/(("?;?[a-zA-Z]?:\d+:")|("?;?[a-zA-Z]?:\d+:({)))(.*?)(?=(("?;?[a-zA-Z]?:\d+:")|("?;?[a-zA-Z]?:\d+:({))|\z))/';
                $count = 0;
                $uploadFile = preg_replace_callback($patternIdentsAll, function($matches) use (&$count, &$lastMatch) {
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
                    
                }, $uploadFile);

                $patternOpenBreacket = '/(?<!["]){(?!["])/';
                $replacements = '`';
                $uploadFile = preg_replace($patternOpenBreacket, $replacements, $uploadFile);

                $patternCloseBreacket = '/(?<!["|\d])}(?!["])/';
                $replacements = '^';
                $uploadFile = preg_replace($patternCloseBreacket, $replacements, $uploadFile);

                $patternCloseBreacket = '/\^/';
                $replacements = '^~NULL|NULL';
                $uploadFile = preg_replace($patternCloseBreacket, $replacements, $uploadFile);

                $arResult = dessrial($uploadFile);
                $arResult = removeElementByKey($arResult, "NULL");
                $arResult = margeDuble($arResult);
            }
        }
    }
}
