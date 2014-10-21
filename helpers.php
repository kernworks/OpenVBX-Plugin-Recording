<?php


function generateFlashAudioPlayer($url, $size='sm')
{
	$ci =& get_instance();
	$iphone = (strpos($_SERVER['HTTP_USER_AGENT'],"iPhone") === false) ? false : true;
	$ipod = (strpos($_SERVER['HTTP_USER_AGENT'],"iPod") === false) ? false : true;
	$ipad = (strpos($_SERVER['HTTP_USER_AGENT'],"iPad") === false) ? false : true;
	$android = (strpos($_SERVER['HTTP_USER_AGENT'],"Android") === false) ? false : true;
	$berry = (strpos($_SERVER['HTTP_USER_AGENT'],"BlackBerry") === false) ? false : true;
	$palmpre = (strpos($_SERVER['HTTP_USER_AGENT'],"webOS") === false) ? false : true;
	$palm = (strpos($_SERVER['HTTP_USER_AGENT'],"PalmOS") === false) ? false : true;
	if ($iphone || $ipod || $android || $berry || $ipad || $palmpre || $palm )
	{
		switch($size)
		{
			case "sm":
				$width=165;
				break;
			case "lg":
				$width=400;
				break;
		}
		?><audio src="<?php echo $url; ?>" controls preload="none" style="width:<?php echo $width; ?>px;"></audio><?php
	}
	else
	{
		$id = uniqid("",true);
		$id = str_replace(".","",$id);
		?>
	<div id="jquery_jplayer_<?php echo $id; ?>" class="jp-jplayer"></div>

	<div class="jp-container_<?php echo $id; ?>"<?php if($size=="lg") echo " style='display:inline-block; width:360px;'"; ?>>
		<div class="jp-audio"<?php if($size=="sm") echo " style='width:160px;'"; ?>>
			<div class="jp-type-single">
				<div id="jp_interface_1" class="jp-interface">
					<ul class="jp-controls">
						<li style="background:none;"><a href="#" class="jp-play" tabindex="1">play</a></li>
						<li style="background:none;"><a href="#" class="jp-pause" tabindex="1">pause</a></li>
						<li style="background:none;"><a href="#" class="jp-mute"<?php if($size=="sm") echo " style='left:133px;'"; ?> tabindex="1">mute</a></li>
						<li style="background:none;"><a href="#" class="jp-unmute"<?php if($size=="sm") echo " style='left:133px;'"; ?> tabindex="1">unmute</a></li>
					</ul>
					<div class="jp-progress-container"<?php if($size=="sm") echo " style='width:65px;'"; ?>>
						<div class="jp-progress"<?php if($size=="sm") echo " style='width:60px;'"; ?>>
							<div class="jp-seek-bar">
								<div class="jp-play-bar"></div>
							</div>
						</div>
					</div>
					<div class="jp-volume-bar-container"<?php if($size=="sm") echo " style='display:none;'"; ?>>
						<div class="jp-volume-bar">
							<div class="jp-volume-bar-value"></div>
						</div>
					</div>
				</div>
			</div>
		</div><?php

		//Stage the javascript for this jPlayer
		$jplayer = <<<JPLAYER
			\$("#jquery_jplayer_$id").jPlayer({
				ready: function () {
					\$(this).jPlayer("setMedia", {
						mp3: "$url.mp3",
						wav: "$url.wav"
					});
				},
				play: function() { // To avoid both jPlayers playing together.
					\$(this).jPlayer("pauseOthers");
				},
				repeat: function(event) { // Override the default jPlayer repeat event handler
					if(event.jPlayer.options.loop) {
						\$(this).unbind(".jPlayerRepeat").unbind(".jPlayerNext");
						\$(this).bind($.jPlayer.event.ended + ".jPlayer.jPlayerRepeat", function() {
							\$(this).jPlayer("play");
						});
					} else {
						\$(this).unbind(".jPlayerRepeat").unbind(".jPlayerNext");
						\$(this).bind($.jPlayer.event.ended + ".jPlayer.jPlayerNext", function() {
							\$("#jquery_jplayer_2").jPlayer("play", 0);
						});
					}
				},
				swfPath: "player",
				supplied: "mp3, wav",
				volume: 1,
				preload: "none",
				wmode: "window",
				smoothPlayBar: true,
				cssSelectorAncestor: ".jp-container_$id"
			});
JPLAYER;
		//Have to use ci to add the js since OpenVBX:addJS doesnt allow for direct scripts
		$ci->template->add_js($jplayer,'embed');
	}
} // end function generateFlashAudioPlayer
