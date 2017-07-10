<?php

use App\Models\Merchants\Merchant;
use Illuminate\Contracts\Routing\ResponseFactory;

if (! function_exists('is_admin')) {
    /**
     * @param \App\Models\Merchants\Merchant|null $merchant
     * @return bool
     */
    function is_admin(Merchant $merchant = null)
    {
        if (! $merchant) {
            return false;
        }

        return ! ! $merchant->is_admin;
    }
}

if (! function_exists('is_admin2')) {
    /**
     * @param \App\Models\Merchants\Merchant|null $merchant
     * @return bool
     */
    function is_admin2(Merchant $merchant = null)
    {
        if (! $merchant) {
            return false;
        }

        if($merchant->is_admin || session()->has('impersonated_by')) {
            return true;
        }
        
        return false;
    }
}

if (! function_exists('api')) {
    /**
     * @param string $content
     * @param int $status
     * @param array $headers
     * @return ResponseFactory|\Illuminate\Http\Response
     */
    function api($content = '', $status = 200, array $headers = [])
    {
        $factory = app(ResponseFactory::class);

        if (func_num_args() === 0) {
            return $factory;
        }

        $content = ['data' => $content];

        return $factory->make($content, $status, $headers);
    }
}

if (! function_exists('currency_to_usd')) {
    /**
     * @param $amount
     * @param $from
     * @return float
     */
    function currency_to_usd($amount, $from)
    {
        $url = "https://www.google.com/finance/converter?a=$amount&from=$from&to=USD";
        $data = file_get_contents($url);
        preg_match('/<span class=bld>(.*)<\/span>/', $data, $converted);
        $converted = preg_replace("/[^0-9.]/", "", $converted[1]);

        return round($converted, 3);
    }
}

if (! function_exists('convert_ebay_item_condition')) {
    /**
     * @param $level
     * @return string
     */
    function convert_ebay_item_condition($level)
    {
        if ($level >= 1000 && $level < 2000) {
            return 'new';
        }

        if ($level >= 2000 && $level < 3000) {
            return 'reconditioned';
        }

        if ($level >= 3000) {
            return 'used';
        }
    }
}

if (! function_exists('overlaps')) {
    /**
     * Check if number ranges overlap.
     *
     * @param float $a1
     * @param float $a2
     * @param float $b1
     * @param float $b2
     * @param bool $canTouch
     * @return bool
     */
    function overlaps(float $a1, float $a2, float $b1, float $b2, bool $canTouch = false)
    {
        if ($canTouch) {
            return $a1 < $b2 && $b1 <= $a2;
        }

        return $a1 <= $b2 && $b1 <= $a2;
    }
}

if (! function_exists('seconts_to_to')) {
    /**
     * Convert seconds into human readable format.
     *
     * @param $inputSeconds
     * @return string
     */
    function seconds_to_time($inputSeconds)
    {
        $secondsInAMinute = 60;
        $secondsInAnHour = 60 * $secondsInAMinute;
        $secondsInADay = 24 * $secondsInAnHour;

        // extract days
        $days = floor($inputSeconds / $secondsInADay);

        // extract hours
        $hourSeconds = $inputSeconds % $secondsInADay;
        $hours = floor($hourSeconds / $secondsInAnHour);

        // extract minutes
        $minuteSeconds = $hourSeconds % $secondsInAnHour;
        $minutes = floor($minuteSeconds / $secondsInAMinute);

        // extract the remaining seconds
        $remainingSeconds = $minuteSeconds % $secondsInAMinute;
        $seconds = ceil($remainingSeconds);

        $output = '';

        if ((int)$days) {
            $output .= ((int)$days).'d,';
        }

        if ((int)$hours) {
            $output .= ' '.((int)$hours).'h,';
        }

        if ((int)$minutes) {
            $output .= ' '.((int)$minutes).'m,';
        }

        if ((int)$seconds) {
            $output .= ' '.((int)$seconds).'s';
        }

        return trim($output);
//        // return the final array
//        $obj = array(
//            'd' => (int) $days,
//            'h' => (int) $hours,
//            'm' => (int) $minutes,
//            's' => (int) $seconds,
//        );
//        return $obj;
    }
}

if (! function_exists('parse_csv')) {
    /**
     * @param $filePath
     * @return array
     */
    function parse_csv(string $filePath) : array
    {
        return array_map('str_getcsv', file($filePath));
    }
}

if (! function_exists('array_trim_values')) {
    /**
     * @param $array
     * @param string $charlist
     * @return array
     */
    function array_trim_values($array, $charlist = " \t\n\r\0\x0B")
    {
        return array_map(function ($item) use ($charlist) {
            return trim($item, $charlist);
        }, $array);
    }
}

if (! function_exists('csv_to_keyed_array')) {
    /**
     * @param $csv
     * @return array
     */
    function csv_to_keyed_array(array $csv) : array
    {
        $columns = array_splice($csv, 0, 1)[0];

        foreach ($csv as $index => $row) {
            $new = [];

            foreach ($row as $i => $cell) {
                $new[$columns[$i]] = $cell;
            }

            $csv[$index] = $new;
        }

        $transformed = array_filter($csv, function ($product) {
            $isEmpty = true;

            foreach ($product as $value) {
                if ($value !== '') {
                    $isEmpty = false;
                    continue;
                }
            }

            if ($isEmpty) {
                return false;
            }
            else {
                return true;
            }
        });

        return $transformed;
    }
}

if (! function_exists('array_duplicates')) {
    /**
     * Get duplicate values with their indexes.
     *
     * @param $array
     * @return array
     */
    function array_duplicates($array)
    {
        $dupes = [];

        natcasesort($array);
        reset($array);

        $old_key = null;
        $old_value = null;

        foreach ($array as $key => $value) {
            if ($value === null) {
                continue;
            }

            if (strcasecmp($old_value, $value) === 0) {
                $dupes[$old_key] = $old_value;
                $dupes[$key] = $value;
            }

            $old_value = $value;
            $old_key = $key;
        }

        return $dupes;
    }
}
if (! function_exists('get_memory')) {
    /**
     * Get memory usage in units.
     *
     * @return string
     */
    function get_memory()
    {
        $size = memory_get_usage(true);
        $unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];

        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2).' '.$unit[(int)$i];
    }
}

if (! function_exists('in_string')) {
    /**
     * Check if string exists in other string.
     *
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    function in_string(string $haystack, string $needle)
    {
        return strpos($haystack, $needle) !== false;
    }
}
