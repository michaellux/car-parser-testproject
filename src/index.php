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
$allNodeValues = [];

$cardLinkSelector = 'article.listing-item .listing-title > a';

$finishPage = $crawler->filter('.pagination li:nth-last-child(2) > a.page-numbers')->text(); 
$expectedQuantity = preg_match('/(\d+)\D*$/', $crawler->filter('.results-count')->text(), $m);
$expectedQuantityNum = $m[1];

for ($currentPage = $startPage; $currentPage <= $finishPage; $currentPage++) { 
  $response = $client->request('GET', getUrl($currentPage));
  $html = ''.$response->getBody();

  $crawler = new Crawler($html);
  $nodeValues = $crawler->filter($cardLinkSelector)->each(function (Crawler $node, $i): string {
    // search for values that I want
    return $node->attr('href');
  });
  $allNodeValues = array_merge($nodeValues, $allNodeValues);
}

if ($expectedQuantityNum == count($allNodeValues))
{
  print_r($allNodeValues);
}
else
{
  echo "Возможно получили не все карточки машин";
}












// echo back out to screen