<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

use App\Scopes\EnabledScope;

/**
 * Currency model.
 *
 * This defines the currencies available in an economy.
 *
 * @property int id
 * @property int economy_id
 * @property-read Economy economy
 * @property string name
 * @property string|null code
 * @property string symbol
 * @property string format
 * @property bool enabled
 * @property bool allow_wallet
 * @property Carbon deleted_at
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Currency extends Model {

    use SoftDeletes;

    protected $table = 'currency';

    protected $fillable = [
        'name',
        'code',
        'symbol',
        'format',
        'enabled',
        'allow_wallet',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    /**
     * Get dynamic properties.
     *
     * @param string $name Property name.
     *
     * @return mixed|string Result.
     */
    public function __get($name) {
        switch($name) {
            case 'displayName':
                return $this->name . ': ' . $this->symbol;
            default:
                return parent::__get($name);
        }
    }

    /**
     * Check whether dynamic properties exist.
     *
     * @param string $name Property name.
     *
     * @return bool True if exists, false if not.
     */
    public function __isset($name) {
        switch($name) {
            case 'displayName':
                return true;
            default:
                return parent::__isset($name);
        }
    }

    /**
     * Limit to enabled currencies.
     */
    public function scopeEnabled($query) {
        (new EnabledScope)->apply($query, $this);
    }

    /**
     * Get a relation to the economy this currency is in.
     *
     * @return Relation to the economy.
     */
    public function economy() {
        return $this->belongsTo(Economy::class);
    }

    /**
     * Format the given balance.
     *
     * @param decimal $value The value.
     * @param int [$format=BALANCE_FORMAT_PLAIN] The balance formatting rules.
     * @param array [$options=null] An array of options.
     *
     * @return string Formatted balance.
     */
    function format($value, $format = BALANCE_FORMAT_PLAIN, $options = []) {
        // Take parameters out of options, use defaults
        $prefix = $options['prefix'] ?? null;
        $neutral = $options['neutral'] ?? false;
        $color = $options['color'] ?? true;

        // If neutrally formatting, always show positive number
        if($neutral)
            $value = abs($value);

        // Format the balance
        $out = $this->formatBasic($value);

        // Prefix
        if(!empty($prefix))
            $out = $prefix . $out;

        // Add color for negative values
        switch($format) {
            case null:
            case BALANCE_FORMAT_PLAIN:
                break;
            case BALANCE_FORMAT_COLOR:
                if(!$color) {}
                else if($neutral)
                    // TODO: style instead of giving an explicit neutral color
                    $out = '<span style="color: #2185d0;">' . $out . '</span>';
                else if($value < 0)
                    $out = '<span style="color: red;">' . $out . '</span>';
                else if($value > 0)
                    $out = '<span style="color: green;">' . $out . '</span>';
                break;
            case BALANCE_FORMAT_LABEL:
                // TODO: may want to add horizontal class to labels
                if(!$color)
                    $out = '<div class="ui label">' . $out . '</div>';
                else if(isset($options['label-color']) && !empty($options['label-color']))
                    $out = '<div class="ui ' . $options['label-color'] . ' label">' . $out . '</div>';
                else if($neutral)
                    $out = '<div class="ui blue label">' . $out . '</div>';
                else if($value < 0)
                    $out = '<div class="ui red label">' . $out . '</div>';
                else if($value > 0)
                    $out = '<div class="ui green label">' . $out . '</div>';
                else
                    $out = '<div class="ui label">' . $out . '</div>';
                break;
            default:
                throw new \Exception("Invalid balance format type given");
        }

        return $out;
    }

    /**
     * Format the given value as this currency.
     *
     * @param decimal $value The value to format.
     * @return string The formatted value.
     */
    public function formatBasic($value) {
        // Get the measurement format
        $format = $this->format;

        // Value Regex
        $valRegex = '/([0-9].*|)[0-9]/';

        // Match decimal and thousand separators
        preg_match_all('/[\s\',.!]/', $format, $separators);
        if($thousand = Arr::get($separators, '0.0', null))
            if($thousand == '!')
                $thousand = '';
        $decimal = Arr::get($separators, '0.1', null);

        // Match format for decimals count
        preg_match($valRegex, $format, $valFormat);
        $valFormat = Arr::get($valFormat, 0, 0);

        // Count decimals length
        $decimals = $decimal ? strlen(substr(strrchr($valFormat, $decimal), 1)) : 0;

        // Do we have a negative value?
        if($negative = $value < 0 ? '-' : '')
            $value = $value * -1;

        // Format the value
        $value = number_format($value, $decimals, $decimal, $thousand);

        // Apply the formatted measurement
        // TODO: get this from somewhere
        if($include_symbol ?? true)
            $value = preg_replace($valRegex, $value, $format);

        // Return value
        return $negative . $value;
    }

    /**
     * Get a list of known currency codes following ISO 4217.
     * These are stored in 'resources/data/currency-codes.txt'.
     *
     * @return array
     */
    public static function currencyCodeList() {
        return array_filter(explode(
                "\n",
                file_get_contents(resource_path('data/currency-codes.txt'))
            ),
            function($item) {
                return !empty(trim($item));
            }
        );
    }
}
