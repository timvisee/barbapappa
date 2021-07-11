<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

abstract class BarAppExport extends DefaultValueBinder implements WithCustomValueBinder, WithHeadings, WithStrictNullComparison, ShouldAutoSize {

    /**
     * Whether to show headers.
     * @var bool
     */
    protected $headers;

    /**
     * Constructor.
     *
     * @param bool $headers Show headers.
     */
    public function __construct(bool $headers = true) {
        $this->headers = $headers;
    }

    public function headings(): array {
        return $this->headers ? $this->headerNames() : [];
    }

    abstract public function headerNames(): array;

    /**
     * @return array
     */
    public function registerEvents(): array {
        return array(
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->setAutoFilter();
                // ->setAutoFilter('A2:' . $event->sheet->getDelegate()->getHighestColumn() .'2');
            }
        );
    }
}
