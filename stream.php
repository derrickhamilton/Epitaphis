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

    $loader = new Twig_Loader_Filesystem('resources/views');
    $twig = new Twig_Environment($loader);
    
    $admin = check_if_user_is_admin($_SESSION['user_id']);
    
    $stmt = $conn->prepare("SELECT `users`.`firstName`, `goals`.`goal`, profile_pictures.path, `goal_assoc`.`timestamp` from users inner join goal_assoc on users.id=goal_assoc.user_id inner join goals on goal_assoc.goal_id=goals.id inner join profile_pictures on users.profile_picture_id=profile_pictures.id ORDER BY `goal_assoc`.`timestamp` ASC");
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $i = 0;
    
    while ($row = $res->fetch_assoc())
    {
        $rows[$i] = $row;
        $i++;
    }
    
    echo $twig->render('stream.html', array(
                                             'nav' => array('page' => $_SERVER['PHP_SELF'], 'admin' => $admin),
                                             'rows' => $rows
                                             ));
    ?>

