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
        $profile_user_id = $_POST['profile_user_id'];
        if ($user_id != $profile_user_id)
        {
            header("Location: profile.php?id=".$profile_user_id);
        }
        $bio = $_POST['bio'];
        $profile_picture_id = $_POST['profile_picture_id'];
        
        // goal id of new goal added
        $new_goal_id = -1;
        
        foreach ($_POST as $key => $value)
        {
            if (substr($key, 0, 11) == "goal_remove")
            {
                $goal_id = filter_var(substr($key, 11), FILTER_SANITIZE_NUMBER_INT);
                // deletes role from role_assoc
                $stmt = $conn->prepare("DELETE FROM `goal_assoc` WHERE `goal_id`=?");
                $stmt->bind_param("i", $goal_id);
                $stmt->execute();
                // deletes role from row
                $stmt = $conn->prepare("DELETE FROM `goals` WHERE `id`=?");
                $stmt->bind_param("i", $goal_id);
                $stmt->execute();
            }
            // if new goal added
            else if (substr($key, 0, 8) == "goal_new")
            {
                if ($value != "")
                {
                    // inserts new goal into goals table with name and empty text
                    $stmt = $conn->prepare("INSERT INTO `goals` (goal) VALUES (?)");
                    $stmt->bind_param("s", $value);
                    $stmt->execute();
                    $new_goal_id = $conn->insert_id;
                    // inserts new goal into role_assoc for user who posted request
                    $stmt = $conn->prepare("INSERT INTO `goal_assoc` (user_id, goal_id) VALUES (?, ?)");
                    $stmt->bind_param("ii", $user_id, $new_goal_id);
                    $stmt->execute();
                }
            }
            // updates user assigned to role
            else if (substr($key, 0, 9) == "goal_edit")
            {
                $goal_id = filter_var(substr($key, 9), FILTER_SANITIZE_NUMBER_INT);
                $stmt = $conn->prepare("UPDATE `goals` SET `goal`=? WHERE `id`=?");
                $stmt->bind_param("si", $value , $goal_id);
                $stmt->execute();
            }
        }
        if (count($error) == 0)
        {
            $stmt = $conn->prepare("UPDATE `users` SET `bio`=?, `profile_picture_id`=? WHERE `id`=?");
            $stmt->bind_param("sii", $bio, $profile_picture_id, $user_id);
            $stmt->execute();
        }
    } //ends post request
    
    $profile_user_id = (($_GET['id'])? $_GET['id'] : $user_id);
    $owner = (($profile_user_id == $user_id)? true : false);
    
    $stmt = $conn->prepare("SELECT `goal_assoc`.`goal_id`, `goals`.`goal` FROM `goal_assoc` LEFT JOIN `goals` ON `goal_assoc`.`goal_id`=`goals`.`id` WHERE `goal_assoc`.`user_id`=?");
    $stmt->bind_param("i", $profile_user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $i = 0;
    
    while ($row = $res->fetch_assoc())
    {
        $rows[$i] = $row;
        $i++;
    }
    
    $stmt2 = $conn->prepare("SELECT `profile_picture_id`, `path`, `firstName`, `lastName`, `bio` FROM `users` INNER JOIN `profile_pictures` ON `users`.`profile_picture_id`=`profile_pictures`.`id` WHERE `users`.`id`=?");
    $stmt2->bind_param("i", $profile_user_id);
    $stmt2->execute();
    $res2 = $stmt2->get_result();
    $row2 = $res2->fetch_assoc();
    
    $stmt3 = $conn->prepare("SELECT `id`, `path` FROM `profile_pictures`");
    $stmt3->execute();
    $res3 = $stmt3->get_result();
    $i = 0;
    
    while ($row = $res3->fetch_assoc())
    {
        $image_rows[$i] = $row;
        $i++;
    }
    
    $stmt4 = $conn->prepare("SELECT `accepted` from follower_assoc where user_id=? and following_id=?");
    $stmt4->bind_param("ii", $user_id, $profile_user_id);
    $res4 = $stmt4->get_result();
    $followed = true;
    
    if ($res4->num_rows == 0) {
        $followed = false;
    }
    
    
    $bio = $row2['bio'];
    $firstName = $row2['firstName'];
    $lastName = $row2['lastName'];
    $profile_picture_path = $row2['path'];
    $profile_picture_id = $row2['profile_picture_id'];
    
    $loader = new Twig_Loader_Filesystem('resources/views');
    $twig = new Twig_Environment($loader);
    
    $admin = check_if_user_is_admin($_SESSION['user_id']);
    
    echo $twig->render('profile.html', array(
                                             'nav' => array('page' => $_SERVER['PHP_SELF'], 'admin' => $admin),
                                             'rows' => $rows,
                                             'image_rows' => $image_rows,
                                             'profile_user_id' => $profile_user_id,
                                             'bio' => $bio,
                                             'firstName' => $firstName,
                                             'lastName' => $lastName,
                                             'owner' => $owner,
                                             'profile_picture_path' => $profile_picture_path,
                                             'profile_picture_id' => $profile_picture_id,
                                             'followed' => $followed
                                             ));
    ?>

