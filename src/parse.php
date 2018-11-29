<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Utils\CustomParser;


$parser = new CustomParser();

$postData = trim($_POST["urlList"]);
$urlList = preg_split('/[\s,]+/', $postData);
$fileName = __DIR__ . "/fileData/parsed.xlsx";


$parser
    ->parseUrlList($urlList)
    ->saveToXslxFile($fileName);

echo("Congratulations! URL parsed");
