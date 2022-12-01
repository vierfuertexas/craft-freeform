<?php

namespace Solspace\Freeform\Library\Export;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use yii\base\ErrorException;

class ExportExcel extends ExportCsv
{
    public static function getLabel(): string
    {
        return 'Excel';
    }

    public function getMimeType(): string
    {
        return 'application/vnd.ms-excel';
    }

    public function getFileExtension(): string
    {
        return 'xlsx';
    }

    public function export(): mixed
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($this->getValuesAsArray());

        ob_start();

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

        $content = ob_get_clean();

        try {
            ob_end_clean();
        } catch (ErrorException $e) {
        }

        return $content;
    }
}
