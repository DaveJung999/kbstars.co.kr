<?php
require 'class.mysql.status.php';

# 아래에서 HOSTNAME_OF_DBSERVER,USERNAME,PASSWD,DB_NAME 은 자신에게 맞게 수정하세요
//mysql_connect('localhost','USERNAME','PASSWD');
//mysql_select_db('DB_NAME');

$status = new mysql_status;
$status->tohtml();

// 25/01/XX Auto mysql_close() 제거 (연결이 없으므로 불필요)
?>
