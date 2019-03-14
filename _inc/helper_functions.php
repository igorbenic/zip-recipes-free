<?php
/**
 * Created by PhpStorm.
 * User: gezimhome
 * Date: 2017-06-08
 * Time: 17:08
 */

namespace ZRDN;

/**
 * Remove blank values from JSON-LD. It looks through nested arrays and considers them to be blank if
 *  the all of their keys start with @. E.g.: @type, @context.
 * @param $arr array in JSON LD format.
 * @return array
 */
function clean_jsonld($arr) {
    $cleaned_crap = array_reduce(array_keys($arr), function ($acc, $key) use ($arr) {
        $value = $arr[$key];
        if (is_array($value)) {
            $cleaned_array = clean_jsonld($value);
            // add array if it has keys that don't start with @
            $array_has_data = count(array_filter(array_keys($cleaned_array), function ($elem) {
                    return substr($elem, 0, 1) !== '@';
                })) > 0;

            if ($array_has_data) {
                $acc[$key] = $cleaned_array;
            }
        }
        else {
            if ($value !== "") {
                $acc[$key] = $value;
            }
        }

        return $acc;
    }, array());

    return $cleaned_crap;
}