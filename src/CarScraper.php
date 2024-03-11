<?php
namespace Michaellux\CarParserTestproject;
require '../vendor/autoload.php';

use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;

class CarScraper {
    private $client;
    private $startPage = 1;

    public function __construct() {
        $this->client = new Client();
    }

    public function getCarLinks() {
        $allLinks = [];
        $finishPage = $this->getFinishPage();

        for ($currentPage = $this->startPage; $currentPage <= $finishPage; $currentPage++) {
            $response = $this->client->request('GET', $this->getUrl($currentPage));
            $html = $response->getBody();
            $crawler = new Crawler($html);
            $links = $crawler->filter('article.listing-item .listing-title > a')->each(function (Crawler $node, $i) {
                return $node->attr('href');
            });
            $allLinks = array_merge($links, $allLinks);
        }

        return $allLinks;
    }

    private function getUrl($page = 1) {
        return 'https://premiumcarsfl.com/listing-list-full' . '/page/' . $page;
    }

    private function getFinishPage() {
        $response = $this->client->request('GET', $this->getUrl());
        $html = $response->getBody();
        $crawler = new Crawler($html);
        return (int) $crawler->filter('.pagination li:nth-last-child(2) > a.page-numbers')->text();
    }

    private function getExpectedCarQuantity() {
      $response = $this->client->request('GET', $this->getUrl());
      $html = $response->getBody();
      $crawler = new Crawler($html);
      $expectedQuantity = preg_match('/(\d+)\D*$/', $crawler->filter('.results-count')->text(), $m);
      return $m[1];
    }

    public function checkResultCount($allCarLinks) {
      return $this->getExpectedCarQuantity() == count($allCarLinks);
    }

    public function parseCarDetails($link) {
      echo $link;
      $response = $this->client->request('GET', $link);
      $html = $response->getBody();
      $crawler = new Crawler($html);
  
      $description = $crawler->filter('#listing-detail-detail');
      $priceBlock = $crawler->filter('article[id^="post-"]');

      $brand = $description->filterXPath('//div[contains(text(), "Make")]')->nextAll('.value')->text();
      $model = $description->filterXPath('//div[contains(text(), "Model")]');
      if ($model->text('') != '') {
      $model = $model->nextAll('.value')->text();
      }
      else {
        $model = '';
      }
      $year = $description->filterXPath('//div[contains(text(), "Year")]');
      if ($year->text('') != '') {
        $year = $year->nextAll('.value')->text();
      }
      else {
        $year = '';
      }
      $color = $description->filterXPath('//div[contains(text(), "Color")]');
      if ($color->text('') != '') {
        $color = $color->nextAll('.value')->text();
      }
      else {
        $color = '';
      }
      $mileage = $description->filterXPath('//div[contains(text(), "Mileage")]');
      if ($mileage->text('') != '') {
        $mileage = $mileage->nextAll('.value')->text();
      }
      else {
        $mileage = '';
      }

      $price = $priceBlock->filterXPath('//span[contains(text(), "price balance US")]')->nextAll('.value')->text();
      $VIN = $description->filterXPath('//div[contains(text(), "VIN")]')->nextAll('.value')->text();

      $imageLinkSelector = '.listing-detail-gallery .right-images div:nth-child(2) > a';
      $imageLink = $crawler->filter($imageLinkSelector);
      if ($imageLink->count() > 0) {
          $imageLink = $imageLink->attr('href');
      } else {
          $imageLink = '';
      }

      return new CarInfo($brand, $model, $year, $color, $mileage, $price, $VIN, $imageLink, $link);
    }
}
