document.write('<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromediacom/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="145" height="29" id="kb_calender_date" align="middle">');
document.write('<param name="allowScriptAccess" value="sameDomain" />');
document.write('<param name="movie" value="/img/kb_calender_date.swf?date_y=<?=$_GET['date_y']?>&date_m=<?=$_GET['date_m']?>" />');
document.write('<param name="quality" value="high" />');
document.write('<param name="bgcolor" value="#ffffff" />');
document.write('<embed src="/img/kb_calender_date.swf" quality="high" bgcolor="#ffffff" width="145" height="29" name="kb_calender_date" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromediacom/go/getflashplayer" />');
document.write('</object>');