<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

defined('BASEPATH') OR exit('No direct script access allowed');

class Importer extends CI_Model
{
    /**
     * Import data from specific data and convert to array.
     *
     * @param $source
     * @param bool $hasHeader
     * @return mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function importFrom($source, $hasHeader = true)
    {
        $inputFileType = IOFactory::identify($source);
        $reader = IOFactory::createReader($inputFileType);
        $reader->setReadDataOnly(true);

        $spreadsheet = $reader->load($source);

        $data = [];

        if($reader instanceof \PhpOffice\PhpSpreadsheet\Reader\Xls) {

        }

        $rowIterator = $spreadsheet->getActiveSheet()->getRowIterator();
        foreach($rowIterator as $row){
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            if($row->getRowIndex() == 1 && $hasHeader) {
                continue;
            }

            $row = [];
            foreach ($cellIterator as $cell) {
                if($hasHeader) {
                    $column = $cell->getColumn();
                    $coordinate = $cell->getCoordinate();

                    $header = $spreadsheet->getActiveSheet()
                        ->getCell($column . '1')
                        ->getValue();

                    if(empty($header)) {
                        $header = $coordinate;
                    }

                    $row[$header] = $cell->getValue();
                } else {
                    $row[] = $cell->getValue();
                }
            }

            array_push($data, $row);
        }

        return $data;
    }
}