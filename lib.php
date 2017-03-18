<?php
function getManageBacSession($username, $password, $domain = "raha") {
	$authenticated = false;
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://".$domain.".managebac.com/login");
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$response = curl_exec($ch);
	if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == "404") {
		echo json_encode(array(
			"message" => "ManageBac doesn't exist on this domain!"
		));
		http_response_code(404);
		die();
	}
	curl_close($ch);
	$doc = new DOMDocument();
	@$doc->loadHTML($response);
	$nodes = $doc->getElementsByTagName("meta");

	for($i = 0; $i < $nodes->length; $i++) {
		$meta = $nodes->item($i);
		if($meta->getAttribute("name") == "csrf-token") {
			$csrf_token = $meta->getAttribute("content");
		}
	}

	list($header, $body) = explode("\r\n\r\n", $response, 2);
	preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $response, $matches);
	$cookies = array();
	$nextCurlcookies = "";
	foreach($matches[1] as $item) {
		parse_str($item, $cookie);
		$cookies = array_merge($cookies, $cookie);
		if(array_key_exists("__cfduid", $cookie)) {
			$nextCurlcookies .= "__cfduid=".$cookie["__cfduid"].";";
		} else if(array_key_exists("_managebac_session", $cookie)) {
			$nextCurlcookies .= "_managebac_session=".$cookie["_managebac_session"]."; request_method=POST;";
		}
	}

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://" . $domain . ".managebac.com/sessions");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_COOKIE, $nextCurlcookies);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
		'login' => $username,
		'password' => $password,
		'remember_me' => '0',
		'commit' => 'Sign-in',
		'utf' => '%E2%9C%93',
		'authenticity_token' => $csrf_token
	)));
	curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	$response = curl_exec($ch);
	if(curl_getinfo($ch, CURLINFO_HTTP_CODE) == '200') {
		echo json_encode(array(
			"message" => "Wrong Credentials."
		));
		http_response_code(401);
		die();
	}
	list($header, $body) = explode("\r\n\r\n", $response, 2);
	preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $response, $matches);
	preg_match_all('/^Location:(.*)$/mi', $response, $matches2);
	$cookies = array();
	$nextCurlcookies = "";
	foreach($matches[1] as $item) {
		parse_str($item, $cookie);
		$cookies = array_merge($cookies, $cookie);
		if(array_key_exists("__cfduid", $cookie)) {
			$nextCurlcookies .= "__cfduid=" . $cookie["__cfduid"] . ";";
		} else if(array_key_exists("_managebac_session", $cookie)) {
			$nextCurlcookies .= "_managebac_session=" . $cookie["_managebac_session"] . "; request_method=POST;";
		}
	}
	curl_close($ch);	
	$payloadURL = trim($matches2[1][0]);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $payloadURL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_COOKIE, $nextCurlcookies);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$response = curl_exec($ch);
	list($header, $body) = explode("\r\n\r\n", $response, 2);
	preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $response, $matches);
	preg_match_all('/^Location:(.*)$/mi', $response, $matches2);
	$cookies = array();
	$requestCurlcookies = "";
	foreach($matches[1] as $item) {
		parse_str($item, $cookie);
		$cookies = array_merge($cookies, $cookie);
		if(array_key_exists("__cfduid", $cookie)) {
			$nextCurlcookies .= "__cfduid=" . $cookie["__cfduid"] . ";";
		} else if(array_key_exists("_managebac_session", $cookie)) {
			$nextCurlcookies .= "_managebac_session=" . $cookie["_managebac_session"] . "; request_method=POST;";
		}
	}
	curl_close($ch);
	$handleURL = trim($matches2[1][0]);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $handleURL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_COOKIE, $nextCurlcookies);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$response = curl_exec($ch);
	list($header, $body) = explode("\r\n\r\n", $response, 2);
	preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $response, $matches);
	preg_match_all('/^Location:(.*)$/mi', $response, $matches2);
	$cookies = array();
	$requestCurlcookies = "";
	foreach($matches[1] as $item) {
		parse_str($item, $cookie);
		$cookies = array_merge($cookies, $cookie);
		if (array_key_exists("__cfduid", $cookie)) {
			$requestCurlcookies .= "__cfduid=" . $cookie["__cfduid"] . ";";
		} else if (array_key_exists("user_id", $cookie)) {
			$requestCurlcookies .= "user_id=" . $cookie["user_id"] . ";";
		}
		if(array_key_exists("_managebac_session", $cookie)) {
			$requestCurlcookies .= "_managebac_session=" . $cookie["_managebac_session"] . "; request_method=POST;";
		}
	}

	if (strpos(trim($matches2[1][0]), "/student") !== false) {
		$authenticated = true;
	}

	if ($authenticated) {
		return array(
			"cookie_string" => $requestCurlcookies,
			"csrf_token" => $csrf_token
		);
	} else {
		echo json_encode(array(
			"message" => "Wrong Credentials."
		));
		http_response_code(401);
		die();
	}
}

function getClasses($cookie_string) {
	$opts = array(
		'http'=>array(
			'method'=>"GET",
			'header'=>"Cookie: ".$cookie_string."\r\n"
		)
	);
	$context = stream_context_create($opts);
	$response = file_get_contents("https://raha.managebac.com/student", false, $context);
	$class_string = explode('"', explode("<li class='more'>", explode('nav with-indicators', $response)[1])[0]);
	$classes = [];
	for($i = 1; $i < count($class_string); $i += 2) {
		$classes[] = explode('/', $class_string[$i])[3];
	}
	return $classes;
}

function getClassName($cookie_string, $class_id) {
	// https://raha.managebac.com/student/classes/10611515
	// <h3> </h3>
	$opts = array(
		'http'=>array(
			'method'=>"GET",
			'header'=>"Cookie: ".$cookie_string."\r\n"
		)
	);
	$context = stream_context_create($opts);
	$response = file_get_contents("https://raha.managebac.com/student/classes/".$class_id, false, $context);
	$class_name = explode('</h3>', explode("<h3>", $response)[1])[0];
	return $class_name;
}

function getTasks($cookie_string, $class_id) {
	$opts = array(
		'http'=>array(
			'method'=>"GET",
			'header'=>"Cookie: ".$cookie_string."\r\n"
		)
	);
	$context = stream_context_create($opts);
	$response = file_get_contents("https://raha.managebac.com/student/classes/".$class_id."/tasks", false, $context);
	$tasks_arr = explode("<div class='details'>", $response);
	$tasks = [];
	for($i = 1; $i < count($tasks_arr); $i++) {
		$tasks[] = explode('/', explode('"', $tasks_arr[$i])[1])[5];
	}
	return $tasks;
}

function getTaskNameAndGrade($cookie_string, $class_id, $task_id) {
	$opts = array(
		'http'=>array(
			'method'=>"GET",
			'header'=>"Cookie: ".$cookie_string."\r\n"
		)
	);
	$context = stream_context_create($opts);
	$response = file_get_contents("https://raha.managebac.com/student/classes/".$class_id."/tasks/".$task_id, false, $context);
	if (strpos($response, "<div class='label label-score'>") !== false) {
		if (strpos($response, "<div class='label label-summative'>Summative</div>") !== false) {
			$title = explode('</h4>', explode("<h4 class='title'>", $response)[1])[0];
			$count = 1;
			$A = -1;
			$B = -1;
			$C = -1;
			$D = -1;
			if (strpos($response, "<li>\nA: ") !== false) {
				$A = explode("</div>", explode("<div class='label label-score'>", $response)[$count])[0];
				$count++;
			}
			if (strpos($response, "<li>\nB: ") !== false) {
				$B = explode("</div>", explode("<div class='label label-score'>", $response)[$count])[0];
				$count++;
			}
			if (strpos($response, "<li>\nC: ") !== false) {
				$C = explode("</div>", explode("<div class='label label-score'>", $response)[$count])[0];
				$count++;
			}
			if (strpos($response, "<li>\nD: ") !== false) {
				$D = explode("</div>", explode("<div class='label label-score'>", $response)[$count])[0];
				$count++;
			}
			return array($title, $A, $B, $C, $D);
		} else {
			return -2;
		}
	} else {
		return -1;
	}
}
?>