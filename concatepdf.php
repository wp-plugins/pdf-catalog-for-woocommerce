<?php

include('mergePDF.php');

$archivePath = $_POST['archivePath'];
$outputFileName = $_POST['outputFileName'];

$inputFiles = explode(',', $_POST['inputFilesList']);

$merger = new mergePDF();
$result = $merger->merge($inputFiles, $archivePath, $outputFileName);

if ($result)
{
    echo 'success';
}
else
{
    echo 'error';
}