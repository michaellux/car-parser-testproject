<?php

require '../vendor/autoload.php';

use Symfony\Component\DomCrawler\Crawler;

function getUrl($page = 1) {
  return 'https://premiumcarsfl.com/listing-list-full' . '/page/' . $page;
}

// url
$startPage = 1;

// go get data from url
$client = new \GuzzleHttp\Client();
$response = $client->request('GET', getUrl());
$html = ''.$response->getBody();

$crawler = new Crawler($html);

$crawler->filter('body');

// loop through the data
$allLinks = [];

$cardLinkSelector = 'article.listing-item .listing-title > a';

$finishPage = $crawler->filter('.pagination li:nth-last-child(2) > a.page-numbers')->text(); 
$expectedQuantity = preg_match('/(\d+)\D*$/', $crawler->filter('.results-count')->text(), $m);
$expectedQuantityNum = $m[1];

for ($currentPage = $startPage; $currentPage <= $finishPage; $currentPage++) { 
  $response = $client->request('GET', getUrl($currentPage));
  $html = ''.$response->getBody();

  $crawler = new Crawler($html);
  $links = $crawler->filter($cardLinkSelector)->each(function (Crawler $node, $i): string {
    return $node->attr('href');
  });
  $allLinks = array_merge($links, $allLinks);
}

//class Car
class CarInfo {
    const CONDITION = 'Used';
    const GOOGLE_PRODUCT_CATEGORY = '123';
    const STORE_CODE = 'xpremium';
    const VEHICLE_FULLFILLMENT_OPTION_STORE_CODE = 'in_store:premium';

    private string $brand;
    private string $model;
    private string $year;
    private string $color;
    private string $mileage;
    private int $price;
    private string $VIN;
    private string $imageLink;
    private string $linkTemplate;

    public function __construct(
        string $brand,
        string $model,
        string $year,
        string $color,
        string $mileage,
        string $price,
        string $VIN,
        string $imageLink,
        string $linkTemplate
    ) {
        $this->brand = $brand;
        $this->model = $model;
        $this->year = $year;
        $this->color = $color;
        $this->mileage = $mileage;
        $this->price = (int) str_replace(',', '', $price);
        $this->VIN = $VIN;
        $this->imageLink = $imageLink;
        $this->linkTemplate = $linkTemplate . '?store=' . self::STORE_CODE;
    }
}


if ($expectedQuantityNum == count($allLinks))
{
  $carInfos = [];
  foreach ($allLinks as $key => $link) {
    $response = $client->request('GET', $link);
    $html = ''.$response->getBody();
    echo $link;
    $crawler = new Crawler($html);
    $descriptionSelector = '#listing-detail-detail';
    $description = $crawler->filter($descriptionSelector);

    $priceSelector = 'article[id^="post-"]';
    $priceBlock = $crawler->filter($priceSelector);

    $brand = $description->filterXPath('//div[contains(text(), "Make")]')->nextAll('.value')->text();
    
    $model = $description->filterXPath('//div[contains(text(), "Model")]');
    if ($model->text('') != '') {
     $model = $model->nextAll('.value')->text();
    }
    else {
      $model = '';
    }
    
    echo $model;
    $year = $description->filterXPath('//div[contains(text(), "Year")]');
    if ($year->text('') != '') {
      $year = $year->nextAll('.value')->text();
    }
    else {
      $year = '';
    }
    echo $year;

    $color = $description->filterXPath('//div[contains(text(), "Color")]');
    if ($color->text('') != '') {
      $color = $color->nextAll('.value')->text();
    }
    else {
      $color = '';
    }
    echo $color;

    $mileage = $description->filterXPath('//div[contains(text(), "Mileage")]');
    if ($mileage->text('') != '') {
      $mileage = $mileage->nextAll('.value')->text();
    }
    else {
      $mileage = '';
    }
    echo $mileage;

    $price = $priceBlock->filterXPath('//span[contains(text(), "price balance US")]')->nextAll('.value')->text();
    $VIN = $description->filterXPath('//div[contains(text(), "VIN")]')->nextAll('.value')->text();

    $imageLinkSelector = '.listing-detail-gallery .right-images div:nth-child(2) > a';
    $imageLink = $crawler->filter($imageLinkSelector);
    if ($imageLink->text('') != '') {
      $imageLink = $imageLink->attr('href');
    }
    else {
      $imageLink = '';
    }
    echo $imageLink;

    $carInfo = new CarInfo($brand, $model, $year, $color, $mileage, $price, $VIN, $imageLink, $link);

    $carInfos[] = $carInfo;
  }
}
else
{
  echo "Возможно получили не все карточки машин";
}












// echo back out to screen