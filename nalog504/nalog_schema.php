<?php
####################################################################################
//					카운터테이블
####################################################################################
$table="
CREATE TABLE nalog3_counter_$new_board (
  no int(11) NOT NULL auto_increment,
  ip varchar(30) default NULL,
  id varchar(30) default NULL,
  time int(11) default NULL,
  yy int(4) default NULL,
  mm int(2) default NULL,
  dd int(2) default NULL,
  hh int(2) default NULL,
  week int(1) default NULL,
  os varchar(50) default NULL,
  browser varchar(50) default NULL,
  referer varchar(200) default NULL,
  PRIMARY KEY  (no)
)";
@mysqli_query($connect,$table);
####################################################################################
//					로그테이블
####################################################################################
$table="
CREATE TABLE nalog3_log_$new_board (
  no int(11) NOT NULL auto_increment,
  log varchar(200) default NULL,
  hit int(11) default NULL,
  time int(11) default NULL,
  bookmark tinyint(4) default '0',
  PRIMARY KEY  (no)
)";
@mysqli_query($connect,$table);
####################################################################################
//					상세로그테이블
####################################################################################
$table="
CREATE TABLE nalog3_dlog_$new_board (
  no int(11) NOT NULL auto_increment,
  log varchar(200) default NULL,
  hit int(11) default NULL,
  time int(11) default NULL,
  bookmark tinyint(4) default '0',
  PRIMARY KEY  (no)
)";
@mysqli_query($connect,$table);
####################################################################################
//					현재접속자
####################################################################################
$table="
CREATE TABLE nalog3_now_$new_board (
  ip char(30) NOT NULL default '',
  id char(30) default NULL,
  time int(11) default NULL,
  PRIMARY KEY  (ip)
)";
@mysqli_query($connect,$table);
####################################################################################
//					설정테이블
####################################################################################
$table="
CREATE TABLE nalog3_config_$new_board (
  no int(11) NOT NULL auto_increment,
  skin varchar(50) default 'navyism',
  cookie tinyint(1) default '1',
  cookie_time int(11) default '0',
  counter_check tinyint(1) default '1',
  now_check tinyint(1) default '1',
  log_check tinyint(1) default '1',
  skin_check tinyint(1) default '1',
  connecting int(11) default '20',
  counter_limit int(11) default '10000',
  log_limit int(11) default '10000',
  member_id varchar(100) default 'na3_member',
  auth_time tinyint(1) default '1',
  auth_day tinyint(1) default '1',
  auth_week tinyint(1) default '1',
  auth_month tinyint(1) default '1',
  auth_year tinyint(1) default '1',
  auth_log tinyint(1) default '1',
  auth_dlog tinyint(1) default '1',
  auth_os tinyint(1) default '1',
  auth_member tinyint(1) default '1',
  auth_ip tinyint(1) default '1',
  peak int(11) default '0',
  total int(11) default '0',
  check_admin tinyint(4) default '0',
  time_zone1 char(1) default '1',
  time_zone2 int(11) default '0',
  PRIMARY KEY  (no)
)";
@mysqli_query($connect,$table);
####################################################################################
//					기본설정넣기
####################################################################################
$query="insert into nalog3_config_$new_board (no) values (1)";
@mysqli_query($connect,$query);
?>