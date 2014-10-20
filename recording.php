<?php
//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

include_once('helpers.php');

$ci =& get_instance();

$service = new Services_Twilio($ci->twilio_sid, $ci->twilio_token);
$recordings = $service->account->recordings;
$recording_host = $ci->vbx_settings->get('recording_host', $ci->tenant->id);
$recording_host = (strlen($recording_host) <= 0) ? 'https://api.twilio.com' : 'http://'.$recording_host;

/*$plugin = OpenVBX::$currentPlugin;
$info = $plugin->getInfo();
$path_jquery = base_url() . implode('/', array('plugins', $info['dir_name'], 'js/jquery-1.7.1.min.js'));
$path_js = base_url() . implode('/', array('plugins', $info['dir_name'], 'player/jquery.jplayer.min.js'));

//echo '<script type="text/javascript" src="'.version_url($path_jquery).'"></script>';
//echo '<script type="text/javascript" src="'.version_url($path_js).'"></script>';

//echo '<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>'."\n";
//echo '<script type="text/javascript" src="'.version_url($path_js).'"></script>'."\n";
*/
OpenVBX::addCSS('player/skin/jplayer-black-and-blue.css');

//It would be nice to use a newer version of jquery but I cannot seem to overried the version that OpenVBX adds automatically
//OpenVBX::addJS('js/jquery-1.7.1.min.js');

//jplayer ABSOLUTELY has to go after the jquery script
OpenVBX::addJS('player/2.7.0/jquery.jplayer.min.js');

?>
<div class="vbx-content-main">
	<div class="vbx-content-menu vbx-content-menu-top">
		<h2 class="vbx-content-heading">Recorded Calls</h2>
	</div><!-- .vbx-content-menu -->
<?php
$have_recordings = false;
foreach($recordings as $recording) {

	//Need to find any details on child calls that the parent of the recording made.
	//This tells us who the parent call ended up talking to
	//If there are no child calls, then the recording was probably a voicemail...unless the parent call was an outbound call
	//TODO: Check how recording works on outbound calls
	$child_calls = $service->account->calls->getIterator(0, 50, array(    
	'ParentCallSid' => $recording->call_sid,
	'Status' => 'completed'));  

	$has_calls = false;
	$agents = array();
	//if $child_calls has no calls, $has_calls will remain false. This is the only way to effectively
	//eliminate voicemail recordings also.
	foreach($child_calls as $to_agent) { 
		if ($to_agent->direction === 'outbound-dial') {
			$has_calls = true;
			$agents[] = $to_agent;
		} else if ($to_agent->direction === 'outbound-api') {
			$has_calls = true;
			$agents[] = $to_agent;
		}
	}
	if ($has_calls) {
		//We only know down here if the recording is not a voicemail
		//Prints the table opening an header the first time it sees an actual call recording
		if (!$have_recordings) {?>
	<div class="vbx-content-container">
		<div class="vbx-content-section">
			<table class="vbx-items-grid" border="0">
				<tr class="items-head recording-head"><th>Date</th><th>Duration</th><th>Caller</th><th>Direction</th><th>Spoke To</th><th>Recording</th></tr>
<?
			$have_recordings = true;
		}
		//Details on the recording's parent call
		//This is put here to help reduce the api calls. It is only needed if there are child calls.
		$call = $service->account->calls->get($recording->call_sid);

		$users = array();
		foreach($agents as $agent) {
			if (strpos($agent->to,'client:') !== false) {
				$user = OpenVBX::getUsers(array('id' => intval($agent->to_formatted)),1);
				$users[$agent->to_formatted] = $user->full_name().' <small>(client call)</small>';
			}
		}
?>
				<tr class="message-row recording-type">
					<td class="recording-date"><?php echo date("F j, Y, g:i a",strtotime($recording->date_created)) ?></td>
					<td class="recording-duration"><?php echo gmdate("H:i:s",$recording->duration%86400) ?></td>
					<td class="message-caller recording-caller"><span class="phone-number"><?php echo $call->from_formatted ?></span></td>
					<td class="recording-direction"><?php echo $call->direction ?></td>
					<td class="message-caller recording-dialed"><span class="phone-number"><?php foreach($agents as $agent) { echo (array_key_exists($agent->to_formatted,$users))? $users[$agent->to_formatted] : $agent->to_formatted.'<br/>'; } ?></span></td>
					<td class="recording-playback"><?php echo generateFlashAudioPlayer($recording_host.$recording->uri, 'sm') ?></td>
				</tr>
<?php
	}
}
?>
<?php if ($have_recordings) {?>
			</table>
		</div><!-- .vbx-content-section -->
	</div><!-- .vbx-content-container -->
<?php } else {?>
	<div class="voicemail-blank recording-blank">
		<h2>There are no recorded calls.</h2>
		<p>When a call is recorded, they will show up here. You can listen to the message right in your web browser.
	</div>
<?php } ?>
</div><!-- .vbx-content-main -->
<?php
/* TODO: Add caching?
   TODO: Add styling. (almost done. Custom class names are in place. Using OpenVBX default names as fallbacks.)
   TODO: Add pagination?
   TODO: Add delete (option for admin only delete?).
   TODO: Show a user's name instead of or in addition to phone number (Currently done when "Spoke To" is a client instead of a phone number).
   TODO: Cleanup code.
   TODO: Add transcripts? (hard to test this without spending $$)
   TODO: Take a nap.
