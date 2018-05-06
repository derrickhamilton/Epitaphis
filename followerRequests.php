<?php
    session_start();
    ob_start();
    header('Content-Type: text/html; charset=utf-8');
    
    require_once("config/config.php");
    require_once("functions.php");
    require_once("vendor/autoload.php");
    
    // if user is not signed in
    if (!isset($_SESSION['user_id']))
    {
        header("Location: index.php");
    }
    
    $user_id = $_SESSION['user_id'];
    
    // profile form submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        $error = array();
        // get data
        
        foreach ($_POST as $key => $value)
        {
            // for each user this user is following
            if (substr($key, 0, 26) == "usersRequestingToFollowYou")
            {
                $following_id = filter_var(substr($key, 26), FILTER_SANITIZE_NUMBER_INT);
                if ($value == "accept")
                {
                    $stmt = $conn->prepare("UPDATE `follower_assoc` SET `accepted`=? WHERE `user_id`=? AND `following_id`=?");
                    $accepted = 1;
                    $stmt->bind_param("iii", $accepted, $following_id, $user_id);
                    $stmt->execute();
                }
                elseif ($value == "reject")
                {
                    $stmt = $conn->prepare("DELETE FROM `follower_assoc` WHERE `user_id`=? AND `following_id`=?");
                    $stmt->bind_param("ii", $following_id, $user_id);
                    $stmt->execute();
                }
            }
        }
        
    } //ends post request
    
    header("Location: followers.php");
    
?>
