<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$page_title?></title>
<link rel="shortcut icon" href="<?=HOST?>/favicon.ico" type="image/x-icon" />
<link href="<?=THEME?>/css/reset.css" rel="stylesheet" type="text/css" />
<link href="<?=THEME?>/css/admin.css" rel="stylesheet" type="text/css" />
<link href="<?=THEME?>/css/jquery-ui.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?=THEME?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=THEME?>/js/jquery-ui.min.js"></script>
<script type="text/javascript">
$('document').ready(function(){
    $("#sidebar h3").click(function(){
        $(this).next().slideToggle('fast');
    });
    $('.list tr').hover(function(){$(this).find('.record-actions').toggleClass('invisible');});
});
</script>
</head>
<body>
<div id="wrapper">

    <?php include (THEME_PATH . '/header.tpl'); ?>

    <div id="sidebar" class="block">

        <div class="panel dashboard">
            <h3>Dashboard</h3>
            <div>
                <p><a href="<?=ADMIN?>/">Dashboard</a></p>
                <p><a href="<?=ADMIN?>">Updates</a></p>
            </div>
        </div>

        <div class="panel videos">
            <h3>Videos</h3>
            <div>
                <p><a href="<?=ADMIN?>/videos.php?status=6">Approved Videos</a></p>
                <p><a href="<?=ADMIN?>/videos.php?status=9">Pending Videos</a></p>
                <p><a href="<?=ADMIN?>/videos.php?status=5">Processing Videos</a></p>
                <p><a href="<?=ADMIN?>/videos.php?status=7">Banned Videos</a></p>
            </div>
        </div>

        <div class="panel members">
            <h3>Members</h3>
            <div>
                <p><a href="<?=ADMIN?>/members.php?status=active">Active Members</a></p>
                <p><a href="<?=ADMIN?>/members.php?status=pending">Pending Members</a></p>
                <p><a href="<?=ADMIN?>/members.php?status=banned">Banned Members</a></p>
            </div>
        </div>

        <div class="panel comments">
            <h3>Comments</h3>
            <div>
                <p><a href="<?=ADMIN?>">Approved Comments</a></p>
                <p><a href="<?=ADMIN?>">Pending Comments</a></p>
                <p><a href="<?=ADMIN?>">Banned Comments</a></p>
                <p><a href="<?=ADMIN?>">SPAM Comments</a></p>
            </div>
        </div>

        <div class="panel flags">
            <h3>Flags</h3>
            <div>
                <p><a href="<?=ADMIN?>">Flagged Videos</a></p>
                <p><a href="<?=ADMIN?>">Flagged Members</a></p>
                <p><a href="<?=ADMIN?>">Flagged Comments</a></p>
            </div>
        </div>

        <div class="panel pages">
            <h3>Pages</h3>
            <div>
                <p><a href="<?=ADMIN?>">Add Page</a></p>
                <p><a href="<?=ADMIN?>">Browse Pages</a></p>
            </div>
        </div>

        <div class="panel appearance">
            <h3>Appearance</h3>
            <div>
                <p><a href="<?=ADMIN?>">Themes</a></p>
                <p><a href="<?=ADMIN?>">Languages</a></p>
                <p><a href="<?=ADMIN?>">Banners</a></p>
            </div>
        </div>

        <div class="panel plugins">
            <h3>Plugins</h3>
            <div>
                <p><a href="<?=ADMIN?>">Installed Plugins</a></p>
                <p><a href="<?=ADMIN?>">Add Plugins</a></p>
            </div>
        </div>

        <div class="panel settings">
            <h3>Settings</h3>
            <div>
                <p><a href="<?=ADMIN?>">General</a></p>
                <p><a href="<?=ADMIN?>">Alerts</a></p>
            </div>
        </div>

    </div>

    <div id="container">
        <div id="main">
            <?php include (THEME_PATH . "/$content"); ?>
        </div>
    </div>

    <div id="footer-spacer"></div>

</div>

<?php include (THEME_PATH . '/footer.tpl'); ?>