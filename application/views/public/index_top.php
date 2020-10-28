<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
        if (file_exists('organisation.xml')) {
            $organisation = simplexml_load_file('organisation.xml');
        } else {
            exit('Failed to open test.xml.');
        }
    ?>
    <link rel="icon" href="img/gaziantepspor_favicon_32x32.png">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title><?php echo $organisation->title; ?></title>

    <!-- Bootstrap -->
    <link href="bootstrap-4.0.0/dist/css/bootstrap.css" rel="stylesheet">
    <script src="jquery-3.3.1/jquery-3.3.1.min.js"></script>
    <script src="fontawesome-free-5.0.13/svg-with-js/js/fontawesome-all.min.js" type="text/javascript"></script>
<!--    Hui Color Style-->
    <link href="css/hui-color-style.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">


</head>

<body ng-app="myApp" class="blue-gradient h-100">




