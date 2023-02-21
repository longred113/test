<?php
namespace App\Helpers;

class Helper 
{
    public static function IDGenerator($model, $trow, $length = 4, $prefix) {
        $data = $model::orderBy('studentId', 'desc')->first();
        if(!$data) {
            $og_length = $length;
            $last_number = '';
        }else{
            $code = substr($data->$trow, strlen($prefix)+1);
            $acctial_last_number = ($code/1)*1;
            $increment_last_number = $acctial_last_number + 1;
            $last_number_length = strlen($increment_last_number);
            $og_length = $length - $last_number_length;
            $last_number = $increment_last_number;
        }
        $zeros = "";
        for($i=0;$i<$og_length;$i++){
            $zeros.="0";
        }
        return $prefix.'-'.$zeros.$last_number;
    }
    public static function IDRoleGenerator($model, $trow, $length = 4, $prefix) {
        $data = $model::orderBy('roleId', 'desc')->first();
        if(!$data) {
            $og_length = $length;
            $last_number = '';
        }else{
            $code = substr($data->$trow, strlen($prefix)+1);
            $acctial_last_number = ($code/1)*1;
            $increment_last_number = $acctial_last_number + 1;
            $last_number_length = strlen($increment_last_number);
            $og_length = $length - $last_number_length;
            $last_number = $increment_last_number;
        }
        $zeros = "";
        for($i=0;$i<$og_length;$i++){
            $zeros.="0";
        }
        return $prefix.'-'.$zeros.$last_number;
    }
}
?>