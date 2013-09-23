<?php

// Establish page variables, objects, arrays, etc
View::InitView ('profile');
Plugin::Trigger ('profile.start');

View::$vars->logged_in = UserService::LoginCheck();
if (View::$vars->logged_in) {
    $userMapper = new UserMapper();
    $userMapper->getUserById(View::$vars->logged_in);
}

$success = NULL;
$errors = NULL;
$subscriptionId = NULL;

// Verify Member was supplied
$userMapper = new UserMapper();
if (!empty($_GET['username'])) {
    $user = $userMapper->getUserByCustom(array('username' => $_GET['username'], 'status' => 'Active'));
} else {
    App::Throw404();
}

// Verify Member exists
if ($user) {
    View::$vars->member = $user;
    View::$vars->meta->title = Functions::Replace(View::$vars->meta->title, array('member' => View::$vars->member->username));
    Plugin::Trigger('profile.load_member');
} else {
    App::Throw404();
}

// Check if user is subscribed
if (View::$vars->logged_in) {
    $subscriptionMapper = new SubscriptionMapper();
    View::$vars->subscribe_text = $subscriptionMapper->isSubscribed(View::$vars->user->userId, View::$vars->member->userId) ? 'unsubscribe' : 'subscribe';
} else {
    View::$vars->subscribe_text = 'subscribe';
}

// Count subscription
$query = "SELECT COUNT(subscription_id) as count FROM " . DB_PREFIX . "subscriptions WHERE member = " . View::$vars->member->userId;
$countResult = $db->fetchRow($query);
View::$vars->sub_count = $countResult['count'];

// Retrieve member's video list
$videoMapper = new VideoMapper();
$videoMapper->getMultipleVideosById($videoIds);
$query = "SELECT video_id FROM " . DB_PREFIX . "videos WHERE user_id = " . View::$vars->member->userId . " AND status = 'approved' AND private = '0' LIMIT 6";
Plugin::Trigger ('profile.load_recent_videos');
$memberVideosResults = $db->fetchAll($query);
View::$vars->result_videos = $videoMapper->getMultipleVideosById(
    Functions::flattenArray($memberVideosResults, 'video_id')
);

// Update Member's profile view count
View::$vars->member->views++;
$videoMapper->save(View::$vars->member);

// Retrieve latest comments by user
$query = "SELECT comment_id FROM " . DB_PREFIX . "comments WHERE user_id = " . View::$vars->member->userId . "  ORDER BY comment_id DESC LIMIT 10";
Plugin::Trigger ('profile.load_comments');
$result_comments = $db->Query ($query);
View::$vars->comment_list = array();
while ($row = $db->FetchObj ($result_comments)) {
    View::$vars->comment_list[] = $row->comment_id;
}


// Output Page
Plugin::Trigger ('profile.before_render');
View::Render ('profile.tpl');