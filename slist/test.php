<?php
$string = "This is a tst";
echo preg_replace("/ is/", " was", $string),'<br>';
echo preg_replace("/( )is/", "\\1was", $string),'<br>';
echo preg_match("/((This )is (a )test)/", $string, $rt),'<br>';
print_r($rt);
?>