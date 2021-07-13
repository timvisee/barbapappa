<?php

namespace App\Exports;

use App\Models\Bar;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class BarPurchaseHistoryExport extends BarAppExport implements FromQuery, WithColumnFormatting, WithMapping {

    use Exportable;

    private $bar_id;
    private $from_date;
    private $to_date;

    /**
     * Constructor.
     *
     * @param bool $headers Show headers.
     */
    public function __construct(bool $headers = true, int $bar_id, $from_date, $to_date) {
        parent::__construct($headers);

        $this->bar_id = $bar_id;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
    }

    public function query() {
        $bar = Bar::findOrFail($this->bar_id);

        // Build payments query
        $query = $bar->productMutations()->oldest('mutation_product.created_at');

        // Filter to date range
        if(!empty($this->from_date))
            $query = $query->where('mutation_product.created_at', '>=', $this->from_date);
        if(!empty($this->to_date))
            $query = $query->where('mutation_product.created_at', '<=', $this->to_date);

        return $query;
    }

    public function headerNames(): array {
        return [
            'Transaction ID',
            'Mutation ID',
            'State',
            'Settled',
            'Amount',
            'Quantity',
            'Product',
            'User',
            'First seen (UTC)',
            'Last change (UTC)',
        ];
    }

    /**
     * @var Payment $payment
     */
    public function map($mutation): array {
        return [
            $mutation->mutation->transaction_id,
            $mutation->mutation->id,
            $mutation->mutation->stateName(),
            $mutation->mutation->isSettled(),
            -$mutation->mutation->amount,
            $mutation->quantity,
            $mutation->product != null ? $mutation->product->name : null,
            $mutation->mutation->owner != null ? $mutation->mutation->owner->name : null,
            Date::dateTimeToExcel($mutation->mutation->created_at),
            Date::dateTimeToExcel($mutation->mutation->updated_at),
        ];
    }

    public function columnFormats(): array {
        return [
            'E' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
            'I' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'J' => NumberFormat::FORMAT_DATE_YYYYMMDD,
        ];
    }

    public function bindValue($cell, $value) {
        // Skip headers
        if($cell->getRow() <= 1)
            return parent::bindValue($cell, $value);

        // Settled column
        if ($cell->getColumn() == 'D') {
            $cell->setValueExplicit($value, DataType::TYPE_BOOL);
            return true;
        }

        return parent::bindValue($cell, $value);
    }
}
