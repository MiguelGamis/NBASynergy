<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="style/style.css">
    <script src="jquery-3.1.1.min.js"></script>
    <script src="bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
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
        <!--<table width='100%'><tr><td align='center'>Contact <a href='mailto:cwthornt@cs.ubc.ca'>Chris</a> if you are having any Mechanical TA issues, not the course instructor</td></tr></table>-->
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
