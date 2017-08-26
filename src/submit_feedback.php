 <?php
function arr_get($array, $key, $default = null){
    return isset($array[$key]) ? $array[$key] : $default;
}


$info_hash = arr_get($_GET, 'hash', '');
$info_data_base64 = arr_get($_GET, 'data', 'e30='); //'e30=' is base64 for '{}'
$info_data = base64_decode($info_data_base64);

$servername = "localhost";
$username = "tafeedback";
$password = "password";

try {
	$conn = new PDO("mysql:host=$servername;dbname=tafeedback", $username, $password);
	// set the PDO error mode to exception
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// find if a TA with the specified has exists. Bail if not.
	$query = $conn->prepare("SELECT id FROM info WHERE hash=:info_hash LIMIT 1");
	$query->execute(array(':info_hash' => $info_hash));
	$result = $query->fetchAll(PDO::FETCH_ASSOC);

	if (count($result) != 1) {
		print(json_encode(array('status' => 'ERROR', 'error' => 'Cannot find hash'), JSON_PRETTY_PRINT));
		exit;
	}

	$info_id = $result[0]['id'];

	// insert a new rating into the database
	$query = $conn->prepare("INSERT INTO ratings VALUES(NULL, :info_data, :info_id)");
	$query->execute(array(':info_data' => $info_data, ':info_id' => $info_id));

	// if we made it without an error, prepare a success message
	$result = array('hash' => $info_hash, 'data' => $info_data, 'status' => 'OK');
	print(json_encode($result, JSON_PRETTY_PRINT));

} catch(PDOException $e) {
	$result = array('status' => 'ERROR', 'error' => "Connection failed: " . $e->getMessage());
	print(json_encode($result, JSON_PRETTY_PRINT));
	//echo "Connection failed: " . $e->getMessage();
}
?> 
