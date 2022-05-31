<?php 
function convert_number_to_words($number) {
    
    $hyphen      = trans('numbers.hyphen');  // In english it is  - 
    $conjunction = ' '.trans('numbers.and').' ';
    $separator   = trans('numbers.separator').' '; // in english it's , 
    $negative    = ' '.trans('numbers.genative').' ';
    $decimal     = ' '.trans('numbers.point').' ';;
    $dictionary  = array(
        0                   => trans('numbers.zero'),
        1                   => trans('numbers.one'),
        2                   => trans('numbers.two'),
        3                   => trans('numbers.three'),
        4                   => trans('numbers.four'),
        5                   => trans('numbers.five'),
        6                   => trans('numbers.six'),
        7                   => trans('numbers.seven'),
        8                   => trans('numbers.eight'),
        9                   => trans('numbers.nine'),
        10                  => trans('numbers.ten'),
        11                  => trans('numbers.eleven'),
        12                  => trans('numbers.twelve'),
        13                  => trans('numbers.thirteen'),
        14                  => trans('numbers.fourteen'),
        15                  => trans('numbers.fifteen'),
        16                  => trans('numbers.sixteen'),
        17                  => trans('numbers.seventeen'),
        18                  => trans('numbers.eighteen'),
        19                  => trans('numbers.nineteen'),
        20                  => trans('numbers.twenty'),
        30                  => trans('numbers.thirty'),
        40                  => trans('numbers.fourty'),
        50                  => trans('numbers.fifty'),
        60                  => trans('numbers.sixty'),
        70                  => trans('numbers.seventy'),
        80                  => trans('numbers.eighty'),
        90                  => trans('numbers.ninety'),
        100                 => trans('numbers.hundred'),
        1000                => trans('numbers.thousand'),
        1000000             => trans('numbers.million'),
        1000000000          => trans('numbers.billion'),
        1000000000000       => trans('numbers.trillion'),
        1000000000000000    => trans('numbers.quadrillion'),
        1000000000000000000 => trans('numbers.quintillion')
    );
    
    if (!is_numeric($number)) {
        return false;
    }
    
    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }
    
    $string = $fraction = null;
    
    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }
    
    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }
    
    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }
    
    return $string;
} ?>