<?php

namespace Daijulong\Generator;

class Helper
{

    /**
     * 蛇形命名
     *
     * @param string $value
     * @param string $delimiter
     * @return string
     */
    public static function snakeCase($value, $delimiter = '_')
    {
        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));
            $value = mb_strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value));
        }
        return $value;
    }

    /**
     * 驼峰命名
     *
     * @param string $value
     * @return string
     */
    public static function camelCase($value)
    {
        $value = ucwords(str_replace(['-', '_'], ' ', $value));
        return str_replace(' ', '', $value);
    }

    /**
     * 获取数组中所有元素长度的最大值
     *
     * @param array $array
     * @return int
     */
    public static function getMaxLength($array)
    {
        $length = 0;
        if (!empty($array)) {
            foreach ($array as $item) {
                if (is_string($item)) {
                    $length = max($length, strlen($item));
                }
            }
        }
        return $length;
    }

}
