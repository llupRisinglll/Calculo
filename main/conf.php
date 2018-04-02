<?php
/**
 * =========================== Every Configuration Files Start Here ===========================
 * Notice: Comment out all the
 */

// Define the Constance DNS
define("DNS", "//".Route::getDomainName());


// Define All of the Global Variables
Config::set("SiteName", "LeafDev Digital Solution");
Config::set("CDN", DNS);
Config::set("DNS", DNS);

// Define Directories
Config::set("VENDOR_DIR", DNS. "/vendor");
Config::set("CSS_DIR", DNS. "/main/res/css");
Config::set("JS_DIR", DNS. "/main/res/js");
Config::set("IMG_DIR", DNS. "/main/res/img");
Config::set("JSON_DIR", DNS. "/main/res/json");

// MySQL Database Credentials Configuration
define("MySQL_host", "127.0.0.1");
define("MySQL_user", "root");
define("MySQL_pass", "");
define("MySQL_dbn", "calculo");


// MongoDB Database Credentials Configuration
define("mongoHost", "127.0.0.1");
define("mongoPort", "27017");
define('mongoDBName', "leafTabulation");

// Admin Authentication
define('mongoUser', "leaf");
define('mongoPass', "mldev126");

// Client Authentication
define('mongoUserClient', "client");
define('mongoPassClient', "123456");

/**
 *  ========================Any valid URI paths will be declared here.. =======================
 */

function verifyUserStatus(){
	// Verify User Status
	if(isset($_SESSION["username"]) && isset($_SESSION["type"])) {
		// Verify the user
		Config::set("logUsername", $_SESSION['username']);
		Config::set("logAccountType", $_SESSION['type']);
		Config::set("userLoggedStatus", true);

	} else if(isset($_COOKIE["username"])&& isset($_COOKIE["type"])){
		$_SESSION['username'] = $_COOKIE["username"];
		$_SESSION['type'] = $_COOKIE["type"];

		Config::set("logUsername", $_SESSION['username']);
		Config::set("logAccountType", $_SESSION['type']);
		Config::set("userLoggedStatus", true);

	}else{
		Config::set("logUsername", "");
		Config::set("logAccountType", "");
		Config::set("userLoggedStatus", false);
	}


}

function verifyAttempt(){
	session_start();

	// Record the number of Administrative login attempts
	if(isset($_SESSION['attemptCount'])){
		Config::set("attemptCount", $_SESSION['attemptCount']);
	}else if(isset($_COOKIE["attemptCount"])){
		$_SESSION['attemptCount'] = $_COOKIE["attemptCount"];
		Config::set("attemptCount", $_SESSION['attemptCount']);
	}

	if(!isset($_SESSION['resetRequest'])){
		$_SESSION['resetRequest'] = false;
		Config::set("resetRequest", $_SESSION['resetRequest']);

	}elseif (isset($_COOKIE['resetRequest'])){
		$_SESSION['resetRequest'] = $_COOKIE['resetRequest'];
		Config::set("resetRequest", $_SESSION['resetRequest']);
	}
}

Route::method("GET", function (){

	verifyUserStatus();


	Route::path("/", false, function (){
		if (Config::get("userLoggedStatus") == false){
			header("location: /login");
			exit;
		}else {
			Route::view('test.leaf', array());
		}
	});


	Route::path("/login", false, function (){
		if (Config::get("userLoggedStatus") == true){
			header("location: /");
			exit;
		}else {
			verifyAttempt();
			Route::view('login.leaf');
		}
	});

	Route::path("/printInterface", true, function ($year){
		/** printInterface */
		Config::set("APP_YEAR", $year[0]);
		Route::view('print.leaf', array());
	});

	Route::path("/logout", false, function (){
		verifyUserStatus();
		session_start();
		$_SESSION = array();

		# Expire their cookie files
		if(isset($_COOKIE["username"])) {
			setcookie("username", null, -1, '/');
			setcookie("type", null, -1, '/');
		}

		# Destroy the sessions
		session_destroy();

		header("location: /login");
		exit;
	});


	Route::path("/mainInterface", false, function (){
		Route::view('main.leaf', array());
	});

	Route::path("/generate", false, function (){
		Route::view('generate.leaf', array());
	});

	Route::path("/password", false, function (){
		Route::view('password.leaf', array());
	});

	Route::path("/resetRequest", false, function (){

		// Set Default Value of the attempt count
		$_SESSION["resetRequest"] = true;
		setcookie("resetRequest", true, strtotime( '+30 days' ), "/", "", "", TRUE);

		verifyAttempt();

		header("location: /");
		exit;
	});

	Route::path("/removeRequest", false, function (){
		session_start();

		// Set Default Value of the attempt count
		$_SESSION["resetRequest"] = false;
		setcookie("resetRequest", false, strtotime( '+30 days' ), "/", "", "", TRUE);

		verifyAttempt();

		header("location: /");
		exit;
	});


	Route::path("/employees", false, function (){
		$sql = "SELECT `username`, `password` FROM `account` WHERE  `type`='employee'";
		$query = mysqli_query(MySqlLeaf::getCon(), $sql);

		Route::view('employees.leaf', array(
			"ArrDATA" => mysqli_fetch_all($query)
		));

	});

});



Route::method("POST", function (){
	Route::action("/tryLogin", function($data){

		$username = $data["username"];
		$password = md5($data["password"]);


		$sql = "SELECT `type` FROM `account` WHERE `username`='$username' AND `password`='$password'";
		$query = mysqli_query(MySqlLeaf::getCon(), $sql);

		if (mysqli_num_rows($query) > 0) {
			$type = mysqli_fetch_array($query)["type"];

			# Create Sessions and Cookies
			$_SESSION['username'] = $username;
			$_SESSION['type'] = $type;

			setcookie("username", $username, strtotime( '+30 days' ), "/", "", "", TRUE);
			setcookie("type", $type, strtotime( '+30 days' ), "/", "", "", TRUE);

			verifyUserStatus();

			# Return a success Message into the client's request
			echo "STATUS|1";
		} else{

			if ($username == "admin"){
				verifyAttempt();

				if (isset($_SESSION["attemptCount"])){
					$_SESSION["attemptCount"]++;
					setcookie("attemptCount", $_SESSION["attemptCount"], strtotime( '+30 days' ), "/", "", "", TRUE);

				}  else{
					// Set Default Value of the attempt count
					$_SESSION["attemptCount"] = 1;
					setcookie("attemptCount", 1, strtotime( '+30 days' ), "/", "", "", TRUE);
				}


				if ($_SESSION["attemptCount"] == 5){
					echo "ATTEMPT|5";
				}elseif ($_SESSION["attemptCount"] > 5) {
					echo "BLOCKED";
				}else{

					echo "STATUS|0";
				}
			}else{
				echo "STATUS|0";
			}

		}
		exit;

	});

	Route::action("/changePassword", function ($data){
		$username = $data["username"];
		$password = md5($data["password"]);
		$newPassword = md5($data["newPassword"]);

		$sql = "SELECT * FROM `account` WHERE `username`='$username' AND `password`='$password'";
		$query = mysqli_query(MySqlLeaf::getCon(), $sql);

		if (mysqli_num_rows($query) > 0){
			$sql =  "UPDATE `account` SET `password`='$newPassword' WHERE `username`='$username' AND `password`='$password'";
			mysqli_query(MySqlLeaf::getCon(), $sql);
			echo "success";
		}else{
			echo "failed";
		}
		exit;
	});

	Route::action("/changePassAdmin", function ($data){
		$newPass = md5($data["newPass"]);
		$sql =  "UPDATE `account` SET `password`='$newPass' WHERE `username`='admin'";
		mysqli_query(MySqlLeaf::getCon(), $sql);

		// Set Default Value of the attempt count
		$_SESSION["resetRequest"] = false;
		setcookie("resetRequest", false, strtotime( '+30 days' ), "/", "", "", TRUE);

		verifyAttempt();
		echo $sql;

		exit;
	});

	Route::action("/loginVerify", function ($data){
		verifyUserStatus();
		session_start();
	});

	Route::action("/resetForm", function ($data){
		$loc = file_get_contents("res/json/data.json");
		$json = json_decode($loc, true);

		if ( $json["secretCode"] == $data["secretCode"]){
			verifyAttempt();

			$_SESSION["attemptCount"] = 0;
			setcookie("attemptCount", 0, strtotime( '+30 days' ), "/", "", "", TRUE);

			echo "right";
		}else{
			echo "wrong";
		}
		exit;

	});

	Route::action("/resetPass", function ($data){
		$loc = file_get_contents("res/json/data.json");
		$json = json_decode($loc, true);

		if ( $json["emailAdd"] == $data["secretEmail"]) {
			verifyAttempt();

			$_SESSION["attemptCount"] = 0;
			setcookie("attemptCount", 0, strtotime( '+30 days' ), "/", "", "", TRUE);

			echo "right";
		}else{
			echo "wrong";
		}

		exit;

	});


	Route::action("/addEmployee", function ($data){
		$username = $data["username"];
		$password = md5($data["password"]);

		$sql = "INSERT INTO `account`(`username`, `password`, `type`) VALUES ('$username','$password','employee')";
		$query = mysqli_query(MySqlLeaf::getCon(), $sql);

		echo $query === TRUE ? "success" : "failed";
		exit;

	});

	Route::action("/editEmployee", function ($data){
		$oldUsername = $data["oldUsername"];
		$username = $data["username"];
		$password = md5($data["password"]);

		$sql = "UPDATE `account` SET `username`='$username',`password`='$password' WHERE `username`='$oldUsername'";
		$query = mysqli_query(MySqlLeaf::getCon(), $sql);

		echo $query === TRUE ? "success" : "failed";
		exit;

	});

	Route::action("/deleteEmployee", function ($data){
		$username = $data["username"];

		$sql = "DELETE FROM `account` WHERE `username`='$username'";
		$query = mysqli_query(MySqlLeaf::getCon(), $sql);

		echo $query === TRUE ? "success" : "failed";
		exit;

	});

	// Logout Post method
	Route::action("/getAllYears", function($data){
		$sql = "SELECT year(`datetime`) FROM `date_interval` GROUP BY year(`datetime`) ORDER BY `year(``datetime``)` DESC";
		$query = mysqli_query(MySqlLeaf::getCon(), $sql);

		$arr = array();

		if (mysqli_num_rows($query) > 0){
			while ($row = mysqli_fetch_assoc($query)){
				$arr[] = $row["year(`datetime`)"];
			}
		}
		echo json_encode($arr);
		exit;

	});

	Route::action("/getYearVisitor", function ($data){
		$sql = "SELECT DATE_FORMAT(`datetime`, \"%Y-%m\") AS `date`, SUM(`amount`) AS `amount` FROM `date_interval` WHERE YEAR(`datetime`)=" .$data['year']. " GROUP BY MONTH(`datetime`)";
		$query = mysqli_query(MySqlLeaf::getCon(), $sql);

		$arr = array();

		if (mysqli_num_rows($query) > 0){
			while ($row = mysqli_fetch_assoc($query)){
				$arr[] = $row;
			}
		}

		echo json_encode($arr);
		exit;
	});

	Route::action("/getIntervalVisitor", function ($data){
		$start = $data["start"];
		$end = $data["end"];

		$sql = "SELECT SUM(`amount`) AS `amount`, DATE(`datetime`) AS `date` FROM `date_interval` WHERE DATE(`datetime`) BETWEEN '$start' AND '$end' GROUP BY DATE(`datetime`)";
		$query = mysqli_query(MySqlLeaf::getCon(), $sql);

		$arr = array();

		if (mysqli_num_rows($query) > 0){
			while ($row = mysqli_fetch_assoc($query)){
				$arr[] = $row;
			}
		}

		echo json_encode($arr);
		exit;
	});

	Route::action("/getWeeklyVisitor", function ($data){
		$dataArray = $data["interval"];
		$month = $data["month"];
		$year = $data["year"];

		$dataArray[0];

		$lastWeekEnd = 0;

		$weeklyData = array();

		for($i=0; $i < count($dataArray); $i++){
			if ($i == 0){
				$sql = "SELECT SUM(`amount`) as total, DATE_FORMAT(`datetime`, \"%Y-%m-%d\") AS `date` FROM `date_interval` WHERE DATE(`datetime`) BETWEEN '$year-$month-01' AND ' $year-$month-" .$dataArray[$i]. "' GROUP BY MONTH(`datetime`)";
			}else{
				$sql = "SELECT SUM(`amount`) as total, DATE_FORMAT(`datetime`, \"%Y-%m-%d\") AS `date` FROM `date_interval` WHERE DATE(`datetime`) BETWEEN '$year-$month-" .($lastWeekEnd+1). "' AND ' $year-$month-" .$dataArray[$i]. "' GROUP BY MONTH(`datetime`)";
			}

			$lastWeekEnd = $dataArray[$i];
			$query = mysqli_query(MySqlLeaf::getCon(), $sql);

			if (mysqli_num_rows($query) > 0){
				$arr = mysqli_fetch_assoc($query);
				$weeklyData[] = $arr;
			}
		}

		echo json_encode($weeklyData);
		exit;

	});

	Route::action("/getTodayVisitor", function ($data){

		$sql = "SELECT `datetime`,`amount` FROM `date_interval` WHERE DATE(`datetime`)=DATE_FORMAT(NOW(),'%Y-%m-%d')";
		$query = mysqli_query(MySqlLeaf::getCon(), $sql);

		$todayData = array();
		while ($row = mysqli_fetch_assoc($query)){
			$todayData[] = $row;
		}
//		echo $sql;
		echo json_encode($todayData);
		exit;
	});

	Route::action("/updateVisitor", function ($data){
		$datetime = $data["datetime"];
		$amount = $data["amount"];

		$sql = "INSERT INTO `date_interval` (`amount`, `datetime`) VALUES ('$amount','$datetime')
  ON DUPLICATE KEY UPDATE `amount`=`amount`+$amount";

		$query = mysqli_query(MySqlLeaf::getCon(), $sql);
		echo $query === TRUE ? $sql : "ERROR";
		exit;

	});

});


/**
 * ====================== This is the action when a WebPage is not found... ===================
 * When the URL exist in the System, reset it or else, Display Not found
// */
if (Route::$pathExist){
	Route::setPathExist(false); // This makes the system will not found anything in default
}else{
	Route::view("error.leaf", array(
		"error_number" => "404",
		"error_title" => "File Not Found",
		"error_description" => "The requested URL was not found on the server"
	));
}