<?php
	$ci =& get_instance();

$service = new Services_Twilio($ci->twilio_sid, $ci->twilio_token);
$recordings = $service->account->recordings;
?>
<div class="vbx-content-main">

	<div class="vbx-content-menu vbx-content-menu-top">
		<h2 class="vbx-content-heading">Recorded Calls</h2>
	</div><!-- .vbx-content-menu -->

	<div class="voicemail-blank <?php echo (count($recordings) > 0) ? 'hide' : '' ?>">
		<h2>There are no recorded calls.</h2>
	</div>	

	<div class="vbx-content-container">
		<div class="vbx-content-section">
<table width="80%">
<tr><th>Date</th><th>Duration</th><th>Caller</th><th>Direction</th><th>Agent(s)</th><th>URL</th></tr>
<?php
foreach($recordings as $recording) {

	$call = $service->account->calls->get($recording->call_sid);

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
?>
	<tr>
		<td><?php echo date("F j, Y, g:i a",strtotime($recording->date_created)) ?></td>
		<td><?php echo gmdate("H:i:s",$recording->duration%86400) ?></td>
		<td><?php echo $call->from_formatted ?></td>
		<td><?php echo $call->direction ?></td>
		<td><?php foreach($agents as $agent) { echo $agent->to_formatted.'<br/>'; } ?></td>
		<td><a href="http://api.twilio.com<?php echo $recording->uri ?>.mp3">MP3</a></td>
	</tr>
<?php
	}
}
?>
</table>
<?php /*			<div class="vbx-form">
				<h3>Voicemail</h3>
				<div class="voicemail-container">
					<div class="voicemail-icon standard-icon"><span class="replace">Voicemail</span></div>
					<div class="voicemail-label">Greeting</div>
					<div class="voicemail-picker">
						<?php
							 $widget = new AudioSpeechPickerWidget(
											'voicemail', 
											$voicemail_mode, 
											$voicemail_say, 
											$voicemail_play, 
											'user_id:'.$this->session->userdata('user_id')
										);
							echo $widget->render();
						?>
					</div>
				</div><!-- .voicemail-container -->
			</div> */?>
		</div><!-- .vbx-content-section -->
	</div><!-- .vbx-content-container -->
	
</div><!-- .vbx-content-main -->
