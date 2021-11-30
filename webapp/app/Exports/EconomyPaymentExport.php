<?php

namespace App\Exports;

use App\Models\Economy;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class EconomyPaymentExport extends BarAppExport implements FromQuery, WithColumnFormatting, WithMapping {

    use Exportable;

    private $economy_id;
    private $from_date;
    private $to_date;

    /**
     * Constructor.
     *
     * @param bool $headers Show headers.
     */
    public function __construct(bool $headers = true, int $economy_id, $from_date, $to_date) {
        parent::__construct($headers);

        $this->economy_id = $economy_id;
        $this->from_date = new Carbon($from_date);
        $this->to_date = new Carbon($to_date);
    }

    public function query() {
        $economy = Economy::findOrFail($this->economy_id);

        // Build payments query
        $query = $economy->payments()->oldest('payment.created_at');

        // Filter to date range
        if(!empty($this->from_date))
            $query = $query->where('payment.created_at', '>=', $this->from_date->copy()->startOfDay());
        if(!empty($this->to_date))
            $query = $query->where('payment.created_at', '<', $this->to_date->copy()->startOfDay()->addDay());

        return $query;
    }

    public function headerNames(): array {
        return [
            'ID',
            'Reference',
            'State',
            'Settled',
            'Amount',
            'User',
            'Service',
            'Started (UTC)',
            'Last change (UTC)',
        ];
    }

    /**
     * @var Payment $payment
     */
    public function map($payment): array {
        return [
            $payment->id,
            $payment->getReference(),
            $payment->stateName(),
            $payment->isCompleted(),
            $payment->money,
            $payment->user?->name,
            $payment->service?->displayName(true),
            Date::dateTimeToExcel($payment->created_at),
            Date::dateTimeToExcel($payment->updated_at),
        ];
    }

    public function columnFormats(): array {
        return [
            'E' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
            'H' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'I' => NumberFormat::FORMAT_DATE_YYYYMMDD,
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

        // Numeric amount field
        if ($cell->getColumn() == 'E') {
            $cell->setValueExplicit($value, DataType::TYPE_NUMERIC);
            return true;
        }

        return parent::bindValue($cell, $value);
    }
}
