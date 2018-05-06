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
    
    // table of users this user is following
    $stmt = $conn->prepare("SELECT `users`.`id`, `users`.`firstName`, `users`.`lastName` FROM `follower_assoc` INNER JOIN `users` ON `follower_assoc`.`following_id`=`users`.`id` WHERE `user_id`=? && `accepted`");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $i = 0;
    
    while ($row = $res->fetch_assoc())
    {
        $rows[$i] = $row;
        $i++;
    }
    
    // table of users following this user
    $stmt2 = $conn->prepare("SELECT `users`.`id`, `users`.`firstName`, `users`.`lastName` FROM `follower_assoc` INNER JOIN `users` ON `follower_assoc`.`user_id`=`users`.`id` WHERE `following_id`=? && `accepted`");
    $stmt2->bind_param("i", $user_id);
    $stmt2->execute();
    $res2 = $stmt2->get_result();
    $i = 0;
    
    while ($row2 = $res2->fetch_assoc())
    {
        $rows2[$i] = $row2;
        $i++;
    }
    
    // table of users requesting to follow this user
    $stmt3 = $conn->prepare("SELECT `users`.`id`, `users`.`firstName`, `users`.`lastName` FROM `follower_assoc` INNER JOIN `users` ON `follower_assoc`.`user_id`=`users`.`id` WHERE `following_id`=? && not(`accepted`)");
    $stmt3->bind_param("i", $user_id);
    $stmt3->execute();
    $res3 = $stmt3->get_result();
    $i = 0;
    
    while ($row3 = $res3->fetch_assoc())
    {
        $rows3[$i] = $row3;
        $i++;
    }
    
    // list of users you can request fo follow
    $stmt4 = $conn->prepare("SELECT `users`.`id`, `users`.`firstName`, `users`.`lastName` FROM `users` WHERE `users`.`id`!=?");
    $stmt4->bind_param("i", $user_id);
    $stmt4->execute();
    $res4 = $stmt4->get_result();
    $i = 0;
    
    while ($row4 = $res4->fetch_assoc())
    {
        $rows4[$i] = $row4;
        $i++;
    }
    
    $stmt5 = $conn->prepare("SELECT `users`.`firstName`, `users`.`lastName`, `profile_pictures`.`path` FROM `users` INNER JOIN `profile_pictures` ON `users`.`profile_picture_id`=`profile_pictures`.`id` WHERE `users`.`id`=?");
    $stmt5->bind_param("i", $user_id);
    $stmt5->execute();
    $res5 = $stmt5->get_result();
    $row5 = $res5->fetch_assoc();
    
    $firstName = $row5['firstName'];
    $lastName = $row5['lastName'];
    $profile_picture_path = $row5['path'];
    
    $loader = new Twig_Loader_Filesystem('resources/views');
    $twig = new Twig_Environment($loader);
    
    $admin = check_if_user_is_admin($_SESSION['user_id']);
    
    echo $twig->render('followers.html', array(
                                               'nav' => array('page' => $_SERVER['PHP_SELF'], 'admin' => $admin),
                                                'usersYouAreFollowing' => $rows,
                                                'usersFollowingYou' => $rows2,
                                                'usersRequestingToFollowYou' => $rows3,
                                                'usersSearch' => $rows4,
                                                'firstName' => $firstName,
                                                'lastName' => $lastName,
                                                'profile_picture_path' => $profile_picture_path
                                               )
                       );
?>
