
<div id="div_moving_banner" style="position:absolute; left:50%; margin-left:520px; top:520; width: 70;">
<!-- 여기에서 배너의 위치와 크기를 설정 하세요-->
<table width="70" border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td><img src="/2013/quick_link/quick_txt.jpg"	/></td>
	</tr>
	<tr>
	<td><a href="/kbstars/2011/d14/2013/01.php"><img src="/images/2014/1030/2014ambsdrboard/quick_1.jpg" border="0" align="absmiddle" /></a></td>
	</tr>
	<tr>
	<td height="7"></td>
	</tr>
	<tr>
	<td><a href="/kbstars/2011/d14/2013/02.php"><img src="/images/2014/1030/2014ambsdrboard/quick_2.jpg" border="0" align="absmiddle" /></a></td>
	</tr>
	<tr>
	<td height="7"></td>
	</tr>
	<tr>
	<td><a href="/kbstars/2011/d14/2013/03.php"><img src="/images/2014/1030/2014ambsdrboard/quick_3.jpg" border="0" align="absmiddle" /></a></td>
	</tr>
	<tr>
	<td height="7"></td>
	</tr>
	<tr>
	<td><a href="/kbstars/2011/d14/2013/04.php"><img src="/images/2014/1030/2014ambsdrboard/quick_4.jpg" border="0" align="absmiddle" /></a></td>
	</tr>
	<tr>
	<td height="7"></td>
	</tr>
	<tr>
	<td><a href="/kbstars/2011/d14/2013/05.php"><img src="/images/2014/1030/2014ambsdrboard/quick_5.jpg" border="0" align="absmiddle" /></a></td>
	</tr>	
	<tr>
	<td height="7"></td>
	</tr>
	<tr>
	<td><a href="/kbstars/2011/d14/2013/06.php"><img src="/images/2014/1030/2014ambsdrboard/quick_6.jpg" border="0" align="absmiddle" /></a></td>
	</tr>	
	<tr>
	<td><img src="/2013/quick_link/quick_txt2.jpg" /></td>
	</tr>
</table>
</div>

<script language=javascript>
<!--
var bNetscape4plus = (navigator.appName == "Netscape" && navigator.appVersion.substring(0,1) >= "4");
var bExplorer4plus = (navigator.appName == "Microsoft Internet Explorer" && navigator.appVersion.substring(0,1) >= "4");
function CheckUIElements_mv(){
		var yMenuFrom, yMenuTo, yButtonFrom, yButtonTo, yOffset, timeoutNextCheck;

		if ( bNetscape4plus ) { 
				yMenuFrom	= document["div_moving_banner"].top;
				yMenuTo	= top.pageYOffset + 335; //이 숫자를 수정하면 위쪽여백을 조절 할 수 있습니다(네츠케이프)
		}
		else if ( bExplorer4plus ) {
				yMenuFrom	= parseInt (div_moving_banner.style.top, 10);
				yMenuTo	= document.body.scrollTop + 335;//이 숫자를 수정하면 위쪽여백을 조절 할 수 있습니다(익스플로러)
		}

		timeoutNextCheck = 500;

		if ( Math.abs (yButtonFrom - (yMenuTo + 152)) < 6 && yButtonTo < yButtonFrom ) {
				setTimeout ("CheckUIElements_mv()", timeoutNextCheck);
				return;
		}

		if ( yButtonFrom != yButtonTo ) {
				yOffset = Math.ceil( Math.abs( yButtonTo - yButtonFrom ) / 10 );
				if ( yButtonTo < yButtonFrom )
						yOffset = -yOffset;

				if ( bNetscape4plus )
						document["divLinkButton"].top += yOffset;
				else if ( bExplorer4plus )
						divLinkButton.style.top = parseInt (divLinkButton.style.top, 10) + yOffset;

				timeoutNextCheck = 10;
		}
		if ( yMenuFrom != yMenuTo ) {
				yOffset = Math.ceil( Math.abs( yMenuTo - yMenuFrom ) / 20 );
				if ( yMenuTo < yMenuFrom )
						yOffset = -yOffset;

				if ( bNetscape4plus )
						document["div_moving_banner"].top += yOffset;
				else if ( bExplorer4plus )
						div_moving_banner.style.top = parseInt (div_moving_banner.style.top, 10) + yOffset;

				timeoutNextCheck = 10;
		}

		setTimeout ("CheckUIElements_mv()", timeoutNextCheck);
}

function OnLoad_mv()
{
		var y;
		if ( top.frames.length )
		if ( bNetscape4plus ) {
				document["div_moving_banner"].top = top.pageYOffset + 135;
				document["div_moving_banner"].visibility = "visible";
		}
		else if ( bExplorer4plus ) {
				div_moving_banner.style.top = document.body.scrollTop + 135;
				div_moving_banner.style.visibility = "visible";
		}
		CheckUIElements_mv();
		return true;
}
OnLoad_mv();
//-->
</script>

