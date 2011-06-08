<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$page_title?></title>
<link rel="shortcut icon" href="<?=HOST?>/favicon.ico" type="image/x-icon" />
<link href="<?=ADMIN?>/css/reset.css" rel="stylesheet" type="text/css" />
<link href="<?=ADMIN?>/css/admin.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="wrapper">

    <div id="header">
        <a id="logo" href="<?=ADMIN?>/" title="CumulusClips Admin Panel">CumulusClips Admin Panel</a>
        <div id="header-menu">
            <a href="<?=HOST?>/" title="View Site">View Site</a> &nbsp;&nbsp;|&nbsp;&nbsp;
            <a href="<?=HOST?>/logout/" title="Logout">Logout</a>
        </div>
    </div>

    <div id="sidebar" class="block">

        <div class="panel<?=(Functions::IsPanelOpen('dashboard'))?' open-panel down-icon':''?>">
            <h3 class="dashboard"><span>Dashboard</span></h3>
            <div>
                <p><a href="<?=ADMIN?>/">Dashboard</a></p>
                <p><a href="<?=ADMIN?>">Updates</a></p>
            </div>
        </div>

        <div class="panel<?=(Functions::IsPanelOpen('videos'))?' open-panel down-icon':''?>">
            <h3 class="videos"><span>Videos</span></h3>
            <div>
                <p><a href="<?=ADMIN?>/videos.php?status=6">Approved Videos</a></p>
                <p><a href="<?=ADMIN?>/videos.php?status=9">Pending Videos</a></p>
                <p><a href="<?=ADMIN?>/videos.php?status=7">Banned Videos</a></p>
            </div>
        </div>

        <div class="panel<?=(Functions::IsPanelOpen('members'))?' open-panel down-icon':''?>">
            <h3 class="members"><span>Members</span></h3>
            <div>
                <p><a href="<?=ADMIN?>/members.php?status=active">Active Members</a></p>
                <p><a href="<?=ADMIN?>/members.php?status=new">New Members</a></p>
                <p><a href="<?=ADMIN?>/members.php?status=pending">Pending Members</a></p>
                <p><a href="<?=ADMIN?>/members.php?status=banned">Banned Members</a></p>
            </div>
        </div>

        <div class="panel<?=(Functions::IsPanelOpen('comments'))?' open-panel down-icon':''?>">
            <h3 class="comments"><span>Comments</span></h3>
            <div>
                <p><a href="<?=ADMIN?>/comments.php?status=approved">Approved Comments</a></p>
                <p><a href="<?=ADMIN?>/comments.php?status=pending">Pending Comments</a></p>
                <p><a href="<?=ADMIN?>/comments.php?status=banned">Banned Comments</a></p>
            </div>
        </div>

        <div class="panel<?=(Functions::IsPanelOpen('flags'))?' open-panel down-icon':''?>">
            <h3 class="flags"><span>Flags</span></h3>
            <div>
                <p><a href="<?=ADMIN?>">Flagged Videos</a></p>
                <p><a href="<?=ADMIN?>">Flagged Members</a></p>
                <p><a href="<?=ADMIN?>">Flagged Comments</a></p>
            </div>
        </div>

        <div class="panel<?=(Functions::IsPanelOpen('pages'))?' open-panel down-icon':''?>">
            <h3 class="pages"><span>Pages</span></h3>
            <div>
                <p><a href="<?=ADMIN?>">Add Page</a></p>
                <p><a href="<?=ADMIN?>">Browse Pages</a></p>
            </div>
        </div>

        <div class="panel<?=(Functions::IsPanelOpen('appearance'))?' open-panel down-icon':''?>">
            <h3 class="appearance"><span>Appearance</span></h3>
            <div>
                <p><a href="<?=ADMIN?>">Themes</a></p>
                <p><a href="<?=ADMIN?>">Languages</a></p>
                <p><a href="<?=ADMIN?>">Banners</a></p>
            </div>
        </div>

        <div class="panel<?=(Functions::IsPanelOpen('plugins'))?' open-panel down-icon':''?>">
            <h3 class="plugins"><span>Plugins</span></h3>
            <div>
                <p><a href="<?=ADMIN?>">Installed Plugins</a></p>
                <p><a href="<?=ADMIN?>">Add Plugins</a></p>
            </div>
        </div>

        <div class="panel<?=(Functions::IsPanelOpen('settings'))?' open-panel down-icon':''?>">
            <h3 class="settings"><span>Settings</span></h3>
            <div>
                <p><a href="<?=ADMIN?>">General</a></p>
                <p><a href="<?=ADMIN?>">Alerts</a></p>
            </div>
        </div>

    </div>

    <div id="container">
        <div id="main">