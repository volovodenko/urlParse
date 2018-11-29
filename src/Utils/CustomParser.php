<?php

namespace App\Utils;

use Sunra\PhpSimple\HtmlDomParser;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class CustomParser
{

    private $spreadsheet;
    private $emailArray = [];


    /**
     * Parse url list
     * @param $urlList
     * @return $this
     */
    public function parseUrlList($urlList)
    {

        foreach ($urlList as $url) {
            if (!preg_match("/^https?\:\/\//", $url)) {
                continue;
            }

            $domContent = @HtmlDomParser::file_get_html($url, false, null, 0);

            if ($domContent) {
                $htmlContent = $domContent->plaintext;
                $emailPattern = "/[\w\-\.]+@[\w\-\.]+\.[a-zA-Z]{2,5}/";
                preg_match_all($emailPattern, $htmlContent, $emailMatches);

                $this->emailArray[$url] = array_unique($emailMatches[0]);
            }


        }

        return $this;
    }


    /**
     * Save parsed url array to xlsx file
     * @param $fileName
     * @return $this
     */
    public function saveToXslxFile($fileName)
    {

        $this->createSpreadsheet();

        $index = 2;
        foreach ($this->emailArray as $url => $emailList) {
            $this->appendToSpreadsheet($index, $url, $emailList);
            $index++;
        }

        $this->saveSpreadsheet($fileName);

        return $this;
    }


    private function createSpreadsheet()
    {
        $this->spreadsheet = new Spreadsheet();

        $this->spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'URL')
            ->setCellValue('B1', 'E-mail');
    }


    private function appendToSpreadsheet($row, $url, $emailList)
    {
        $this->spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A' . $row, $url)
            ->setCellValue('B' . $row, implode(", ", $emailList));
    }


    private function saveSpreadsheet($fileName)
    {
        $this->spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $this->spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(50);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($this->spreadsheet);

        $writer->save($fileName);

    }

}