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
        
        echo "1";
        foreach ($_POST as $key => $value)
        {
            echo "2";
            // for each user this user is following
            if (substr($key, 0, 10) == "followUser")
            {
                echo "3";
                // if user wants to cancel following
                if ($value == "follow")
                {
                    echo "4";
                    $following_id = filter_var(substr($key, 10), FILTER_SANITIZE_NUMBER_INT);
                    $stmt = $conn->prepare("SELECT * FROM `follower_assoc` WHERE `user_id`=? AND `following_id`=?");
                    $stmt->bind_param("ii", $user_id, $following_id);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    if ($res->num_rows == 0)
                    {
                        echo "5";
                        $stmt = $conn->prepare("INSERT INTO `follower_assoc` (user_id, following_id) VALUES (?, ?)");
                        $stmt->bind_param("ii", $user_id, $following_id);
                        $stmt->execute();
                    }
                }
            }
        }
        
    } //ends post request
    
    //header("Location: profile.php");
    
    ?>

