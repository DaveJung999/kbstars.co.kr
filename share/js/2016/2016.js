/********************************************************************
Note : 서브선택 메뉴 색상 orange 변경 JS 
*********************************************************************/

function changeSubmenuColor(id) {
	var el = document.getElementById(id);
	if (el != null)
		el.className = "orange";
}


/********************************************************************
Note : FAN > 응원가
*********************************************************************/

var audiotypes={
	"mp3": "audio/mpeg",
	"mp4": "audio/mp4",
	"ogg": "audio/ogg",
	"wav": "audio/wav"
}

function ss_soundbits(sound){
	var audio_element = document.createElement('audio');
	if (audio_element.canPlayType){
		for (var i=0; i<arguments.length; i++){
			var source_element = document.createElement('source');
			source_element.setAttribute('src', arguments[i]);
			if (arguments[i].match(/\.(\w+)$/i))
				source_element.setAttribute('type', audiotypes[RegExp.$1]);
			audio_element.appendChild(source_element);
		}
		audio_element.load();
		audio_element.playclip=function(){
			
			if (audio_element.currentTime != 0)
				audio_element.currentTime=0;
				
			if (audio_element.paused == true) {
				audio_element.play();
			} else {
				audio_element.pause();
			}
		   
			
		}
		return audio_element;
	}
}

/*

var vsound_vs = ss_soundbits('/images/2016/new/music/sound/victory_song.mp3');
var vsound_kb = ss_soundbits('/images/2016/new/music/sound/kb_victory.mp3');

var vsound_01 = ss_soundbits('/images/2016/new/music/sound/01.mp3');
var vsound_02 = ss_soundbits('/images/2016/new/music/sound/02.mp3');
var vsound_03 = ss_soundbits('/images/2016/new/music/sound/03.mp3');
var vsound_05 = ss_soundbits('/images/2016/new/music/sound/05.mp3');
var vsound_06 = ss_soundbits('/images/2016/new/music/sound/06.mp3');
var vsound_07 = ss_soundbits('/images/2016/new/music/sound/07.mp3');
var vsound_08 = ss_soundbits('/images/2016/new/music/sound/08.mp3');
var vsound_09 = ss_soundbits('/images/2016/new/music/sound/09.mp3');
var vsound_11 = ss_soundbits('/images/2016/new/music/sound/11.mp3');
var vsound_12 = ss_soundbits('/images/2016/new/music/sound/12.mp3');
var vsound_13 = ss_soundbits('/images/2016/new/music/sound/13.mp3');
var vsound_14 = ss_soundbits('/images/2016/new/music/sound/14.mp3');
var vsound_17 = ss_soundbits('/images/2016/new/music/sound/17.mp3');
var vsound_21 = ss_soundbits('/images/2016/new/music/sound/21.mp3');
var vsound_22 = ss_soundbits('/images/2016/new/music/sound/22.mp3');
var vsound_24 = ss_soundbits('/images/2016/new/music/sound/24.mp3');

*/