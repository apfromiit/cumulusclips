<?php

// Init application
include_once(dirname(dirname(__FILE__)) . '/cc-core/config/admin.bootstrap.php');

// Verify if user is logged in
$userService = new UserService();
$adminUser = $userService->loginCheck();
Functions::RedirectIf($adminUser, HOST . '/login/');
Functions::RedirectIf($userService->checkPermissions('admin_panel', $adminUser), HOST . '/account/');

// Establish page variables, objects, arrays, etc
$pageMapper = new PageMapper();
$pageService = new PageService();
$records_per_page = 9;
$url = ADMIN . '/pages.php';
$query_string = array();
$message = null;
$sub_header = null;



### Handle "Delete" record if requested
if (!empty ($_GET['delete']) && is_numeric ($_GET['delete'])) {

    // Validate id
    $page = $pageMapper->getPageById($_GET['delete']);
    if ($page) {
        $pageService->delete($page);
        $message = 'Page has been deleted';
        $message_type = 'success';
    }

}





### Determine which type (status) of pages to display
$status = (!empty ($_GET['status'])) ? $_GET['status'] : 'published';
switch ($status) {

    case 'draft':
        $query_string['status'] = 'draft';
        $header = 'Draft Pages';
        $page_title = 'Draft Pages';
        break;
    default:
        $status = 'published';
        $header = 'Published Pages';
        $page_title = 'Published Pages';
        break;

}
$query = "SELECT page_id FROM " . DB_PREFIX . "pages WHERE status = '$status'";
$queryParams = array();



// Handle Search Member Form
if (isset ($_POST['search_submitted'])&& !empty ($_POST['search'])) {

    $like = trim ($_POST['search']);
    $query_string['search'] = $like;
    $query .= " AND title LIKE :like OR content LIKE :like";
    $sub_header = "Search Results for: <em>$like</em>";
    $queryParams[':like'] = "%$like%";

} else if (!empty ($_GET['search'])) {

    $like = trim ($_GET['search']);
    $query_string['search'] = $like;
    $query .= " AND title LIKE :like OR content LIKE :like";
    $sub_header = "Search Results for: <em>$like</em>";
    $queryParams[':like'] = "%$like%";

}



// Retrieve total count
$query .= " ORDER BY page_id DESC";
$db->fetchAll ($query, $queryParams);
$total = $db->rowCount();

// Initialize pagination
$url .= (!empty ($query_string)) ? '?' . http_build_query($query_string) : '';
$pagination = new Pagination ($url, $total, $records_per_page, false);
$start_record = $pagination->GetStartRecord();
$_SESSION['list_page'] = $pagination->GetURL();

// Retrieve limited results
$query .= " LIMIT $start_record, $records_per_page";
$resultPages = $db->fetchAll ($query, $queryParams);
$pageList = $pageMapper->getPagesFromList(
    Functions::arrayColumn($resultPages, 'page_id')
);


// Output Header
include ('header.php');

?>

<div id="pages">

    <h1><?=$header?></h1>
    <?php if ($sub_header): ?>
    <h3><?=$sub_header?></h3>
    <?php endif; ?>


    <?php if ($message): ?>
    <div class="message <?=$message_type?>"><?=$message?></div>
    <?php endif; ?>


    <div id="browse-header">

        <div class="jump">
            Jump To:
            <select name="status" data-jump="<?=ADMIN?>/pages.php">
                <option <?=(isset($status) && $status == 'published') ? 'selected="selected"' : ''?>value="published">Published</option>
                <option <?=(isset($status) && $status == 'draft') ? 'selected="selected"' : ''?>value="draft">Draft</option>
            </select>
        </div>

        <a class="button add" href="<?=ADMIN?>/pages_add.php">Add New</a>

        <div class="search">
            <form method="POST" action="<?=ADMIN?>/pages.php?status=<?=$status?>">
                <input type="hidden" name="search_submitted" value="true" />
                <input type="text" name="search" value="" />&nbsp;
                <input type="submit" name="submit" class="button" value="Search" />
            </form>
        </div>

    </div>

    <?php if ($total > 0): ?>

        <div class="block list">
            <table>
                <thead>
                    <tr>
                        <td class="large">Title</td>
                        <td class="large">Status</td>
                        <td class="large">Date Created</td>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($pageList as $page): ?>

                    <?php $odd = empty ($odd) ? true : false; ?>
                    <tr class="<?=$odd ? 'odd' : ''?>">
                        <td>
                            <a href="<?=ADMIN?>/pages_edit.php?id=<?=$page->pageId?>" class="large"><?=$page->title?></a><br />
                            <div class="record-actions invisible">
                                <a href="<?=HOST?>/page/?preview=<?=$page->pageId?>" target="_ccsite">Preview</a>
                                <a href="<?=ADMIN?>/pages_edit.php?id=<?=$page->pageId?>">Edit</a>
                                <a class="delete confirm" href="<?=$pagination->GetURL('delete='.$page->pageId)?>" data-confirm="You are about to delete this page, this cannot be undone. Are you sure you want to do this?">Delete</a>
                            </div>
                        </td>
                        <td><?=($page->status == 'published') ? 'Published' : 'Draft'?></td>
                        <td><?=Functions::DateFormat('m/d/Y',$page->dateCreated)?></td>
                    </tr>

                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?=$pagination->paginate()?>

    <?php else: ?>
        <div class="block"><strong>No pages found</strong></div>
    <?php endif; ?>

</div>

<?php include ('footer.php'); ?>