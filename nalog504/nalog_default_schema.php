<?php
####################################################################################
//                  접속통계테이블
####################################################################################
$table = "
CREATE TABLE nalog3_data (
    no int(11) NOT NULL AUTO_INCREMENT,
    yy int(4) DEFAULT NULL,
    mm int(4) DEFAULT NULL,
    dd int(4) DEFAULT NULL,
    h0 int(11) DEFAULT '0',
    h1 int(11) DEFAULT '0',
    h2 int(11) DEFAULT '0',
    h3 int(11) DEFAULT '0',
    h4 int(11) DEFAULT '0',
    h5 int(11) DEFAULT '0',
    h6 int(11) DEFAULT '0',
    h7 int(11) DEFAULT '0',
    h8 int(11) DEFAULT '0',
    h9 int(11) DEFAULT '0',
    h10 int(11) DEFAULT '0',
    h11 int(11) DEFAULT '0',
    h12 int(11) DEFAULT '0',
    h13 int(11) DEFAULT '0',
    h14 int(11) DEFAULT '0',
    h15 int(11) DEFAULT '0',
    h16 int(11) DEFAULT '0',
    h17 int(11) DEFAULT '0',
    h18 int(11) DEFAULT '0',
    h19 int(11) DEFAULT '0',
    h20 int(11) DEFAULT '0',
    h21 int(11) DEFAULT '0',
    h22 int(11) DEFAULT '0',
    h23 int(11) DEFAULT '0',
    hit int(11) DEFAULT '0',
    week int(1) DEFAULT NULL,
    counter varchar(50) NOT NULL DEFAULT '',
    PRIMARY KEY (no)
)";
mysqli_query($connect, $table);

####################################################################################
//                  os/browser
####################################################################################
$table = "
CREATE TABLE nalog3_os (
    no int(11) NOT NULL AUTO_INCREMENT,
    name varchar(100) DEFAULT NULL,
    os tinyint(1) DEFAULT NULL,
    hit int(11) DEFAULT '0',
    counter varchar(50) DEFAULT NULL,
    PRIMARY KEY (no)
)";
mysqli_query($connect, $table);
?>
