<?php

### Created on February 28, 2009
### Created by Miguel A. Hurtado
### This script displays the site homepage


// Include required files
include ('../cc-core/config/admin.bootstrap.php');
App::LoadClass ('User');
App::LoadClass ('Filesystem');


// Establish page variables, objects, arrays, etc
Plugin::Trigger ('admin.videos.start');
//$logged_in = User::LoginCheck(HOST . '/login/');
//$admin = new User ($logged_in);
$message = null;
$page_title = 'Updates';


### Handle "Delete" theme if requested
if (!empty ($_GET['delete']) && !ctype_space ($_GET['delete'])) {

    // Validate theme
    if (file_exists (THEMES_DIR . '/' . $_GET['delete'] . '/theme.xml')) {

        $xml = simplexml_load_file (THEMES_DIR . '/' . $_GET['delete'] . '/theme.xml');
        if (Settings::Get('active_theme') != $_GET['delete']) {
            // DELETE THEME CODE
            $message = $xml->name . ' theme has been deleted';
            $message_type = 'success';
        } else {
            $message = 'Active theme cannot be deleted. Activate another theme and then try again';
            $message_type = 'error';
        }

    }

}


// Output Header
include ('header.php');

?>

<div id="updates">

    <h1>Update CumulusClips</h1>

    <?php if ($message): ?>
    <div class="<?=$message_type?>"><?=$message?></div>
    <?php endif; ?>

    <div class="block">
        <p>An updated version of CumulusClips (version 2.1) is available!</p>
        <p>Steps you can take:</p>
        <ol>
            <li>
                <strong>Update Automatically</strong> - CumulusClips will perform the update on
                it's own. You can just sit back and relax while it completes.
                <em>(Recommended)</em>
            </li>
            <li>
                <strong>Update Manually</strong> - Download version 2.1 from our
                website. Then manually extract and overwrite the files.
                This is usually done to recover from failed updates.
                <p>For detailed instructions on how to update manually you can reference our <a href="">documentation</a>.</p>
            </li>
        </ol>
        <p>
            <a class="button" href="<?=ADMIN?>/updates_begin.php">Update Automatically</a>
            <a class="button" href="http://cumulusclips.org/download/">Update Manually</a>
        </p>
    </div>

</div>

<?php include ('footer.php'); ?>