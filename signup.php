<?php

session_start();

require_once("config/config.php");
require_once("vendor/autoload.php");

if (isset($_SESSION['user_id']))
{
    header("Location: index.php");
}

// possible signup roles
$roles = array("Student", "Faculty");

// signup form submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$error = array();
	$warning = array();
	// validate data -----------------------------------
	// check empty fields
	$required = array("email", "firstName", "lastName", "pass1", "pass2");
	foreach ($required as $key => $value)
	{
		if(!isset($_POST[$value]) || empty($_POST[$value]) && $_POST[$value] != '0')
		{
			$error[$value] = "This field is required.";
		}
	}
	// escape data
	$email = $_POST['email'];
	$firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $pass1 = $_POST['pass1'];
    $pass2 = $_POST['pass2'];
	
	if (!empty($email))
	{
		// TODO: validate email with regex
		// validates email is not already in use
		$stmt = $conn->prepare("SELECT `email` FROM `users` WHERE `email`=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if ($res->num_rows != 0) {
            $error['email'] = "Email is already in use.";
        }
	}
	
	if (!empty($firstName))
	{
		// TODO: validate first name with regex
	}
	
	if (!empty($lastName))
	{
		// TODO: validate last name with regex
	}
	
	if (!empty($pass1))
	{
		// TODO: validate password
	}
	
	if (!empty($pass2))
	{
		if ($pass1 != $pass2)
        {
            $error['pass2'] = "Passwords must match.";
        }
	}
    
	if (count($error) == 0)
	{
		$hash = password_hash($pass1, PASSWORD_DEFAULT);
		// insert row into database
        
        $stmt = $conn->prepare("INSERT INTO `users` (`email`, `firstName`, `lastName`, `passHash`) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $email, $firstName, $lastName, $hash);
        if ($stmt->execute())
        {
            header("Location: index.php");
            exit();
        }
        else
        {
            echo "<strong>Registration error: ".$stmt->error."</strong>";
        }
    }
}

    $loader = new Twig_Loader_Filesystem('resources/views');
    $twig = new Twig_Environment($loader);
    
    echo $twig->render('index.html', array(
                                            'signup_email' => $email,
                                            'signup_firstName' => $firstName,
                                            'signup_lastName' => $lastName,
                                            'signup_error' => $error
                       ));
    
?>
