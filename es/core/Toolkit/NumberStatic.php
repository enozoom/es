<?php

namespace es\core\Toolkit;

final class NumberStatic
{
    /**
     * 一个只增不减的数
     * @param int $base
     * @return int 整数
     */
    public static function increment($base=0){
        return ceil(($base+date('W')+date('m'))*1.2);
    }
}

?>