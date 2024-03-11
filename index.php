<?php

use Michaellux\CarParserTestproject\CarScraper;
use Michaellux\CarParserTestproject\CsvExporter;

require 'vendor/autoload.php';

$scraper = new CarScraper();
print_r('<pre>Получаем ссылки');
$allCarLinks = $scraper->getCarLinks();
if ($scraper->checkResultCount($allCarLinks)) {
    print_r('<pre>Получаем данные об автомобилях</pre>');
    $carInfos = [];
    foreach ($allCarLinks as $link) {
        $carInfo = $scraper->parseCarDetails($link);
        $carInfos[] = $carInfo;
    }
    print_r('<pre>Сохраняем данные в CSV-файл</pre>');
    $currentDateTime = date('Y-m-d_H-i');
    $filePath = 'data/carinfos__' . $currentDateTime . '.csv';
    $exporter = new CsvExporter($filePath);

    $columnHeaders = array_keys($carInfos[0]->toArray());
    $exporter->writeHeaders($columnHeaders);

    foreach ($carInfos as $carInfo) {
        $carInfoArray = $carInfo->toArray();
        $exporter->writeRow($carInfoArray);
    }

    $exporter->close();
    print_r('<pre>CSV создан</pre>');
} else {
    print_r("<pre>Возможно получили не все карточки машин</pre>");
}












// echo back out to screen