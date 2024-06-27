<?php
session_start();

include_once('functions.php');

if ($_POST['submit_btn']) {

    if (isset($_FILES['FileData'])) {

        $dounloadFile = file_get_contents($_FILES['FileData']['tmp_name']);

        $patternN = '/""/';
        $replacements = '"empty_string"';
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
            $arResult = $arUnser;
        } else {
            $dounloadFile = str_replace('~', '', $dounloadFile);
            $dounloadFile = str_replace('`', '', $dounloadFile);
            $dounloadFile = str_replace('|', '', $dounloadFile);
            $dounloadFile = str_replace('^', '', $dounloadFile);


            // $patternN = '/;N;/';
            // $replacements = ';s:1:"N";';
            // $dounloadFile = preg_replace($patternN, $replacements, $dounloadFile);

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

            $patternIdentify = '/"?;?[a-zA-Z]?:\d+:"?({)?/';
            $count = 0;
            $dounloadFile = preg_replace_callback($patternIdentify, function($matches) use (&$count) {
                $count++;
                if ($count % 2 == 0) {
                        return "|" . $matches[1];
                } else {
                    if($matches[1] == '{'){
                        $count++;
                        return "~Aray|`";
                    } else {
                        return "~" . $matches[1];
                    }
                }
            }, $dounloadFile);

            $patternOpenBreacket = '/(?<!["]){(?!["])/';
            $replacements = '`';
            $dounloadFile = preg_replace($patternOpenBreacket, $replacements, $dounloadFile);

            $patternCloseBreacket = '/(?<!["])}(?!["])/';
            $replacements = '^';
            $dounloadFile = preg_replace($patternCloseBreacket, $replacements, $dounloadFile);

            $patternCloseBreacket = '/\^/';
            $replacements = '^~NULL|NULL';
            $dounloadFile = preg_replace($patternCloseBreacket, $replacements, $dounloadFile);

            $arResult = (dessrial($dounloadFile));

        }

    }
}
