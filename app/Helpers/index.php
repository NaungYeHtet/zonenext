<?php

if (! function_exists('get_stepped_random_number')) {
    function get_stepped_random_number(
        int $min,
        int $max,
        int $step = 1,
    ): int {
        if ($min >= $max) {
            throw new Exception('Min value cannot be greater than or equal max value');
        }

        if ($step <= 0) {
            throw new Exception('Step value cannot be less than or equal to zero');
        }

        if ($step > $max) {
            throw new Exception('Step value cannot be greater than max value');
        }

        // Calculate the range of possible values
        $range = ($max - $min) / $step;

        // Generate a random index within the range
        $randomIndex = rand(0, $range);

        // Calculate the random number based on the index and step
        return $min + ($randomIndex * $step);
    }
}

if (! function_exists('number_format_tran')) {
    function number_format_tran(float $number, $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return NumberFormatter::create($locale, NumberFormatter::DECIMAL)->format($number);
    }
}

if (! function_exists('number_format_price')) {
    function number_format_price(float $number, $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        if ($locale != 'my') {
            $numberFormatted = number_format_short($number);

            return "{$numberFormatted} Ks";
        }

        $formatted = '';
        $symbol = 'သိန်း';
        $symbolBefore = false;

        switch ($number) {
            case $number < 999:
                $symbol = 'ရာ';
            case $number < 9999:
                $symbol = 'ထောင်';
            case $number < 99999:
                $symbol = 'သောင်း';
            case $number < 2000000:
                $symbol = 'သိန်း';
            default:
                $symbol = 'သိန်း';
                $number = substr($number, 0, -5);
                $symbolBefore = substr($number, -1) == '0' ? true : false;
        }

        $formatted = number_format_tran($number, $locale);

        $formatted = $symbolBefore ? $symbol.' '.$formatted : $formatted.' '.$symbol;

        return $formatted;
    }
}

if (! function_exists('number_format_short')) {
    function number_format_short(float $number): string
    {
        $units = ['', 'K', 'M', 'B', 'T'];
        for ($i = 0; $number >= 1000; $i++) {
            $number /= 1000;
        }

        return round($number, 1).$units[$i];
    }
}

if (! function_exists('is_valid_url')) {
    function is_valid_url(string $string): bool
    {
        return (bool) filter_var($string, FILTER_VALIDATE_URL);
    }
}
