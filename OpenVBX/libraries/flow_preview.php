<?php
define('PREVIEW_APP_NAME', 'OpenVBX Flow Preview');

function getAppSid($twilio, $twilio_sid) {
	$sid = '';

	$apps = $twilio->request("Accounts/{$twilio_sid}/Applications", "GET");
	$apps_xml = $apps->ResponseXml;

	foreach($apps_xml->Applications->Application as $app) {
		if ($app->FriendlyName == PREVIEW_APP_NAME) {
			$sid = $app->Sid;
		}
	}

	if (!$sid) {
		$app_xml = $twilio->request("Accounts/{$twilio_sid}/Applications", "POST", array(
			'FriendlyName' => PREVIEW_APP_NAME,
			'ApiVersion' => '2010-04-01'));
			$sid = $app_xml->Application->Sid;
	}

	return "$sid";
}

function updateApp($twilio, $twilio_sid, $flowId) {
	$sid = getAppSid($twilio, $twilio_sid);

	$twilio->request("Accounts/{$twilio_sid}/Applications/$sid", "POST", array(
		'VoiceUrl' => site_url('twiml/start/voice/' . $flowId)));
}

function getTwilioRestClient() {
	$CI =& get_instance();

	if (empty($CI->twilio)) {

		$CI->twilio = new TwilioRestClient($CI->twilio_sid,
					$CI->twilio_token,
					$CI->twilio_endpoint);
	}

	return $CI->twilio;
}

