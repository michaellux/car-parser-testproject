<?php

use Michaellux\CarParserTestproject\CarScraper;
use Michaellux\CarParserTestproject\CsvExporter;

require '../vendor/autoload.php';

$scraper = new CarScraper();
$allCarLinks = $scraper->getCarLinks();

if ($scraper->checkResultCount($allCarLinks)) {
    $carInfos = [];
    foreach ($allCarLinks as $link) {
        $carInfo = $scraper->parseCarDetails($link);
        $carInfos[] = $carInfo;
    }

    $currentDateTime = date('Y-m-d_H-i');
    $filePath = '../data/carinfos__' . $currentDateTime . '.csv';
    $exporter = new CsvExporter($filePath);

    $columnHeaders = array_keys($carInfos[0]->toArray());
    $exporter->writeHeaders($columnHeaders);

    foreach ($carInfos as $carInfo) {
        $carInfoArray = $carInfo->toArray();
        $exporter->writeRow($carInfoArray);
    }

    $exporter->close();
    print_r('CSV создан');
} else {
    print_r("Возможно получили не все карточки машин");
}












// echo back out to screen