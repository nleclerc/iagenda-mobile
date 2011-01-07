<?php
include_once '../inc/io.php';
include_once '../inc/data.php';
session_start();

header('Content-type: application/json; charset=utf-8');
//header('Content-type: text/plain; charset=utf-8'); // used for debugging.

$errorMessage = '';
$result = array();
	
if (!isset($_GET['memberId']))
	$errorMessage = "Identifiant de membre manquant (memberId).";
else {
	try {
		$memberId = $_GET['memberId'];
		$content = getMemberDetailPage($memberId);
		
		$isLoggedIn = isLoggedIn($content);
		
		if (!$isLoggedIn)
			$errorMessage = "Vous n'êtes pas identifié.";
		else {
			$username = getUserNameFromContent($content);
			
			$member = createMemberDetails($content);
			
			if ($member) {
				$result = $member;
				$result["username"] = $username;
			} else
				$errorMessage = "Membre non trouvé : $memberId";
		}
		
		$result["loggedIn"] = $isLoggedIn;
	} catch (Exception $e) {
		$errorMessage = $e->getMessage();
	}
}

$result["errorMessage"] = $errorMessage;
echo json_encode($result);
?>
