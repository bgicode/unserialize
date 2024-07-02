<?php
include_once('formHandler.php');
?>

<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" type="text/css" href="style.css">
        <script src="./script.js" type="text/javascript"></script>
    </head>
    <body>
        <div class="wrap">
            <div class="formWraper">
                <form class="form" name="unserializeForm" method="POST" action="<?php $_SERVER['REQUEST_URI'] ?>" enctype="multipart/form-data">

                    <div>
                        <label for="DataUploads">Выбрать файл для десериализации</label>
                        <input type="file" class="FileData" id="DataUploads" name="FileData" accept=".data, .txt, .xml, .json" required>
                    </div>
                    <div class="preview">
                        <p>Файл не выбран</p>
                    </div>

                    <div class="btnWrap">
                        <input class="submitBtn" type="submit" name="submit_btn" value="Десереализовать">
                    </div>
                </form>
            </div>
            <div class="result">
                <?php
                    if ($arResult) {
                        echo "<span>" . $message . "</span>";
                        echo "<span>файл: " . $fileName['basename'] . "</span>";
                        echo "<pre>";
                        print_r($arResult);
                        echo "</pre>";
                    }
                ?>
            </div>
        </div>
    </body>
</html>
