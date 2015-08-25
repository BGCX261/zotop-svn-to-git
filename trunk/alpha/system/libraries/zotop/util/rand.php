<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class BaseRand extends Base
{
    static public function string($len,$type=1,$prefix='')
    {
        //$AllStr = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";

        if ($len<=0) return false;
        $allstr  = '';
        $code  = '';
        switch($type){
           case 1:
           default:
	     $allstr = "123456789ABCDEFGHIJKLMNPQRSTUVWXYZ";
             break;
       }

       if ($allstr){
            while (strlen($code) < $length) {
                $code .= $allstr[rand(0,strlen($allstr))];
            }
       }
       
       return $prefix.$code;
       
    }

    static public function number()
    {
        
    }
}
?>
