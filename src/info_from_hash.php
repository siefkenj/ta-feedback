 <?php
function arr_get($array, $key, $default = null){
    return isset($array[$key]) ? $array[$key] : $default;
}

$info_hash = arr_get($_GET, 'hash', '');

$servername = "localhost";
$username = "tafeedback";
$password = "password";

try {
	$conn = new PDO("mysql:host=$servername;dbname=tafeedback", $username, $password);
	// set the PDO error mode to exception
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$query = $conn->prepare("SELECT hash, year, term, course, tutorial, ta FROM info WHERE hash=:info_hash");
	$query->execute(array(':info_hash' => $info_hash));

	// query the database for all results. If there are
	// any, pick the first one, otherwise, return {status: 'EMPTY'}
	$result = $query->fetchAll(PDO::FETCH_ASSOC);
	if (count($result) >= 1) {
		$result = $result[0];
		$result['status'] = 'OK';
	} else {
		$result = array('status' => 'EMPTY');
	}
	$result['received_hash'] = $info_hash;

	print(json_encode($result, JSON_PRETTY_PRINT));

} catch(PDOException $e) {
	$result = array('status' => 'ERROR', 'error' => "Connection failed: " . $e->getMessage());
	print(json_encode($result, JSON_PRETTY_PRINT));
	//echo "Connection failed: " . $e->getMessage();
}
?> 
