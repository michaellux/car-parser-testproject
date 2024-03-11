<?php

namespace Michaellux\CarParserTestproject;
use Exception;

require '../vendor/autoload.php';

class CsvExporter
{
    private $file;
    private string $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
        $this->file = fopen($filePath, 'w');
        if ($this->file === false) {
            throw new Exception('Не удалось открыть файл');
        }
    }

    public function writeHeaders($headers)
    {
        fputcsv($this->file, $headers);
    }

    public function writeRow($row)
    {
        fputcsv($this->file, $row);
    }

    public function close()
    {
        fclose($this->file);
    }
}
