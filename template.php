<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="style/style.css">
<!--    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
    <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" />-->
    <script src="coreui/jquery.min.js"></script>
    <script src="coreui/jquery-ui.js"></script>
    <link rel="stylesheet" type="text/css" href="style/jquery-ui.css" />
    <script src="coreui/bootstrap.min.js"></script>
    <link rel="stylesheet" href="style/bootstrap.min.css">
    <title><?php /*get_page_title();*/ ?> </title>
</head>
<body>
<div id="wrapper">
    <div id="header">
        <div id="logo">
            <h1>NBA Synergy</h1>
        </div>
        <div id="menu">
            <?php get_page_menu(); ?>
        </div>
    </div>
    <div id="page">
        <div id="content">
            <div class="box">
                <?php get_page_content(); ?>
            </div>
        </div>
    </div>
</div>
<div id="footer">
</div>
</body>
</html>
