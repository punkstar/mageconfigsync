<?php
namespace MageConfigSync\Util;

class ArrayUtil
{
    /**
     * http://www.php.net/manual/en/function.array-diff-assoc.php#111675
     *
     * @param $array1
     * @param $array2
     * @return array
     */
    public static function diffAssocRecursive($array1, $array2)
    {
        $difference = array();

        foreach ($array1 as $key => $value) {
            if (is_array($value)) {
                if (!isset($array2[$key]) || !is_array($array2[$key])) {
                    $difference[$key] = $value;
                } else {
                    $new_diff = self::diffAssocRecursive($value, $array2[$key]);
                    if (!empty($new_diff)) {
                        $difference[$key] = $new_diff;
                    }
                }
            } elseif (!array_key_exists($key, $array2) || $array2[$key] != $value) {
                $difference[$key] = $value;
            }
        }

        return $difference;
    }
}
