<?php

namespace Michaellux\CarParserTestproject;
class CarInfo
{
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
    )
    {
        $this->brand = $brand;
        $this->model = $model;
        $this->year = $year;
        $this->color = $color;
        $this->mileage = $mileage;
        $this->price = (int)str_replace(',', '', $price);
        $this->VIN = $VIN;
        $this->imageLink = $imageLink;
        $this->linkTemplate = $linkTemplate . '?store=' . self::STORE_CODE;
    }

    public function toArray(): array
    {
        return [
            'Condition' => self::CONDITION,
            'google_product_category' => self::GOOGLE_PRODUCT_CATEGORY,
            'store_code' => self::STORE_CODE,
            'vehicle_fulfillment(option:store_code)' => self::VEHICLE_FULLFILLMENT_OPTION_STORE_CODE,
            'Brand' => $this->brand,
            'Model' => $this->model,
            'Year' => $this->year,
            'Color' => $this->color,
            'Mileage' => $this->mileage,
            'Price' => $this->price,
            'VIN' => $this->VIN,
            'image_link' => $this->imageLink,
            'link_template' => $this->linkTemplate
        ];
    }
}