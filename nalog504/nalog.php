<?php
####################################################################################
//					헤더
####################################################################################
header('P3P: CP="NOI CURa ADMa DEVa TAIa OUR DELa BUS IND PHY ONL UNI COM NAV INT DEM PRE"');

####################################################################################
//					준비
####################################################################################
// 변수 초기화
if(!isset($path)){$path="";}
if(!isset($id)){$id="";}
if(!isset($counter)){$counter="";}

if(preg_match("/:\\/\\//", $path)){echo "n@log error : Unknown path name";exit;}

if(!$path){$path=".";}
else{$path=@preg_replace("/^\\/|\\/$/", "", $path);}
if($id && !$counter){$counter=$id;}

@include "$path/nalog_connect.php";
@include "$path/lib.php";
@include "$path/nalog_info.php";
$version=isset($nalog_info['version']) ? $nalog_info['version'] : '';

####################################################################################
//					설정
####################################################################################
$set=@nalog_config("$counter");
if(!$set){echo "n@log error : Unknown counter name";exit;}
$total=$set['total'];

####################################################################################
//					시간설정
####################################################################################
if($set['time_zone2']){
	if($set['time_zone1']){$time_zone=$set['time_zone2']*3600;}
	else{$time_zone=$set['time_zone2']*3600*(-1);}
}else{
$time_zone=0;
}

$time=time()+$time_zone;
$yy=date('Y',$time);
$mm=date('m',$time);$mm=preg_replace("/^0/", "", $mm);
$dd=date('d',$time);$dd=preg_replace("/^0/", "", $dd);
$hh=date('H',$time);$hh=preg_replace("/^0/", "", $hh);
$week=date('w',$time);

####################################################################################
//					접속자설정
####################################################################################
$member_id=$set['member_id'];
$member_id=isset($_COOKIE[$member_id]) ? $_COOKIE[$member_id] : '';
$ip=$_SERVER['REMOTE_ADDR'];

####################################################################################
//					쿠키
####################################################################################
$cookie_result=@setcookie("nalog_check",0,0,"/");

// counting 변수 초기화
$counting=0;

if(!$set['cookie']){
$counting=1;
@setcookie("nalog$counter",0,0,"/");
}

if($set['cookie']==1){

if(!isset($_COOKIE["nalog".$counter])){
$counting=1;

@setcookie("nalog$counter",time(),0,"/");
}
}

if($set['cookie']==2){
$temp=time()-(isset($_COOKIE["nalog".$counter]) ? $_COOKIE["nalog".$counter] : 0);
if($temp>$set['cookie_time']){
$counting=1;

@setcookie("nalog$counter",time(),time()+$set['cookie_time'],"/");
}
}

if($set['cookie']==3){
$temp1=date('Y-m-d', isset($_COOKIE["nalog".$counter]) ? $_COOKIE["nalog".$counter] : time());
$temp2=date('Y-m-d');
if($temp1!=$temp2){
$counting=1;

@setcookie("nalog$counter",time(),time()+30*24*3600,"/");
}
}

if($set['check_admin'] && isset($_COOKIE['nalog_admin']) && $_COOKIE['nalog_admin']){$counting=0;}

if(!$cookie_result){echo "n@log error : Cannot add header(Cookie) information. Insert n@log code in top of this page '".$_SERVER['PHP_SELF']."'";exit;}

#######################################################################################################
//					현재접속자
#######################################################################################################
// now 변수 초기화
$now=0;

if($set['now_check']){
####################################################################################
//					정리
####################################################################################
$temp=$time-$set['connecting'];
$input="delete from nalog3_now_$counter where time<$temp";
@mysqli_query($connect,$input);

####################################################################################
//					접속자넣기
####################################################################################
$query="insert into nalog3_now_$counter (ip,id,time) values ('$ip','$member_id','$time')";
@mysqli_query($connect,$query);

####################################################################################
//					접속자구하기
####################################################################################
$query="select count(*) from nalog3_now_$counter";
$connector=@mysqli_fetch_array(@mysqli_query($connect,$query));
$now=$connector["count(*)"];

####################################################################################
//					최고접속자
####################################################################################
if($set['peak']<$now){
$set['peak']=$now;
}
}
#######################################################################################################
//					현재접속자끝
#######################################################################################################

#######################################################################################################
//					카운팅
#######################################################################################################
if($counting){
$total++;

###############################################################################################
//					카운팅사용
###############################################################################################
if($set['counter_check']){
####################################################################################
//					경로설정
####################################################################################
$dlog=$log=isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
$log=@str_replace("http://www.","http://",$log);
$log=@str_replace("http://","",$log);
$log=@explode("/",$log);
$log="http://".$log[0];

####################################################################################
//					os+browser
####################################################################################
// check_agent() 함수 호출 전 변수 초기화
$os_name='';
$os_version='';
$br_name='';
$br_version='';

if(function_exists('check_agent')){
	check_agent();
}
$os=@trim("$os_name $os_version");
$browser=@trim("$br_name $br_version");

####################################################################################
//					카운터저장
####################################################################################
$http_referer=isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
$value="'','$ip','$member_id','$time','$yy','$mm','$dd','$hh','$week','$os','$browser','$http_referer'";
$query="insert into nalog3_counter_$counter values ($value)";
@mysqli_query($connect,$query);

####################################################################################
//					통계저장
####################################################################################
$query="select * from nalog3_data where counter='$counter' and yy='$yy' and mm='$mm' and dd='$dd'";
$result=@mysqli_fetch_array(@mysqli_query($connect,$query)); 
if($result){
$query="update nalog3_data set h$hh=h$hh+1, hit=h0+h1+h2+h3+h4+h5+h6+h7+h8+h9+h10+h11+h12+h13+h14+h15+h16+h17+h18+h19+h20+h21+h22+h23 where counter='$counter' and yy='$yy' and mm='$mm' and dd='$dd'";
@mysqli_query($connect,$query);
}
else{
$query="insert into nalog3_data (yy,mm,dd,h$hh,week,counter,hit) values ('$yy','$mm','$dd','1','$week','$counter','1')";
@mysqli_query($connect,$query);
}

####################################################################################
//					os저장
####################################################################################
$query="select * from nalog3_os where counter='$counter' and os='1' and name='$os'";
$result=@mysqli_fetch_array(@mysqli_query($connect,$query)); 
if($result){
$query="update nalog3_os set hit=hit+1 where counter='$counter' and os='1' and name='$os'";
@mysqli_query($connect,$query);
}
else{
$query="insert into nalog3_os (name,os,hit,counter) values ('$os','1','1','$counter')";
@mysqli_query($connect,$query);
}

####################################################################################
//					browser저장
####################################################################################
$query="select * from nalog3_os where counter='$counter' and os='0' and name='$browser'";
$result=@mysqli_fetch_array(@mysqli_query($connect,$query)); 
if($result){
$query="update nalog3_os set hit=hit+1 where counter='$counter' and os='0' and name='$browser'";
@mysqli_query($connect,$query);
}
else{
$query="insert into nalog3_os (name,os,hit,counter) values ('$browser','0','1','$counter')";
@mysqli_query($connect,$query);
}

}
###############################################################################################
//					카운팅사용끝
###############################################################################################

###############################################################################################
//					로그사용
###############################################################################################
if($set['log_check']){
####################################################################################
//					로그저장
####################################################################################
$temp=nalog_total("nalog3_log_".$counter,"and log='$log'");
if(!$temp){
$value="'','$log','1','$time',''";
$query="insert into nalog3_log_$counter values ($value)";
@mysqli_query($connect,$query);
}
else{
$input="update nalog3_log_$counter set hit=hit+1, time='$time' where log='$log'";
@mysqli_query($connect,$input);
}

####################################################################################
//					상세로그저장
####################################################################################
$temp=nalog_total("nalog3_dlog_".$counter,"and log='$dlog'");
if(!$temp){
$value="'','$dlog','1','$time',''";
$query="insert into nalog3_dlog_$counter values ($value)";
@mysqli_query($connect,$query);
}
else{
$input="update nalog3_dlog_$counter set hit=hit+1, time='$time' where log='$dlog'";
@mysqli_query($connect,$input);
}
}
###############################################################################################
//					로그사용끝
###############################################################################################

}
#######################################################################################################
//					카운팅끝
#######################################################################################################


####################################################################################
//					전체방문자저장
####################################################################################
$input="update nalog3_config_$counter set peak='".$set['peak']."',total='$total' where no=1";
@mysqli_query($connect,$input);

####################################################################################
//					오늘방문자저장
####################################################################################
$query="select * from nalog3_data where yy='$yy' and mm='$mm' and dd='$dd' and counter='$counter'";
$today=@mysqli_fetch_array(@mysqli_query($connect,$query));
$today=isset($today['hit']) ? $today['hit'] : 0;

####################################################################################
//					최대방문자저장
####################################################################################
$query="select max(hit) from nalog3_data where counter='$counter'";
$day_peak=@mysqli_fetch_array(@mysqli_query($connect,$query));
$day_peak=$day_peak[0];

####################################################################################
//					어제방문자저장
####################################################################################
$time=$time-(24*3600);
$yy=date('Y',$time);
$mm=date('m',$time);
$dd=date('d',$time);
$query="select * from nalog3_data where yy='$yy' and mm='$mm' and dd='$dd' and counter='$counter'";
$yester=@mysqli_fetch_array(@mysqli_query($connect,$query));
$yester=isset($yester['hit']) ? $yester['hit'] : 0;
if(!$yester){$yester=0;}

####################################################################################
//					스킨적용
####################################################################################
if(!$today){$today=0;}
if(!$yester){$yester=0;}
if(!$total){$total=0;}
if(!$now){$now=0;}
if(!isset($peak)){$peak=0;}
if(!$day_peak){$day_peak=0;}

$peak=$set['peak'];
$today_image=$today_text=$today;
$yester_image=$yester_text=$yester;
$total_image=$total_text=$total;
$now_image=$now_text=$now;
$peak_image=$peak_text=$peak;
$day_peak_image=$day_peak_text=$day_peak;

for($i=0;$i<=9;$i++){$today_image=str_replace("$i","<img src=★★★/${i}.gif border=0 align=absmiddle ☆☆☆>",$today_image);}
for($i=0;$i<=9;$i++){$yester_image=str_replace("$i","<img src=★★★/${i}.gif border=0 align=absmiddle ☆☆☆>",$yester_image);}
for($i=0;$i<=9;$i++){$total_image=str_replace("$i","<img src=★★★/${i}.gif border=0 align=absmiddle ☆☆☆>",$total_image);}
for($i=0;$i<=9;$i++){$now_image=str_replace("$i","<img src=★★★/${i}.gif border=0 align=absmiddle ☆☆☆>",$now_image);}
for($i=0;$i<=9;$i++){$peak_image=str_replace("$i","<img src=★★★/${i}.gif border=0 align=absmiddle ☆☆☆>",$peak_image);}
for($i=0;$i<=9;$i++){$day_peak_image=str_replace("$i","<img src=★★★/${i}.gif border=0 align=absmiddle ☆☆☆>",$day_peak_image);}

$today_image=@str_replace("★★★",$path."/skin/".$set['skin'],$today_image);
$yester_image=@str_replace("★★★",$path."/skin/".$set['skin'],$yester_image);
$total_image=@str_replace("★★★",$path."/skin/".$set['skin'],$total_image);
$now_image=@str_replace("★★★",$path."/skin/".$set['skin'],$now_image);
$peak_image=@str_replace("★★★",$path."/skin/".$set['skin'],$peak_image);
$day_peak_image=@str_replace("★★★",$path."/skin/".$set['skin'],$day_peak_image);

$alt="alt='n@log analyzer $version' onclick=window.open(\"$path/admin_counter.php?counter=$counter\") style=cursor:hand";
$today_image=@str_replace("☆☆☆",$alt,$today_image);
$yester_image=@str_replace("☆☆☆",$alt,$yester_image);
$total_image=@str_replace("☆☆☆",$alt,$total_image);
$now_image=@str_replace("☆☆☆",$alt,$now_image);
$peak_image=@str_replace("☆☆☆",$alt,$peak_image);
$day_peak_image=@str_replace("☆☆☆",$alt,$day_peak_image);

####################################################################################
//					스킨패턴적용
####################################################################################
if($set['skin_check']){
$skin="$path/skin/".$set['skin'];
include"$skin/skin.php";
}

@mysqli_close($connect);
unset($connect);
?>