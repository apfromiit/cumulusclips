<?php

class UserService extends ServiceAbstract
{
    /**
     * Delete a user
     * @param User $user Instance of user to be deleted
     * @return void User is deleted from system
     */
    public function delete(User $user)
    {
        Plugin::triggerEvent('user.delete');

        // Delete Avatar
        if (!empty ($user->avatar)) Avatar::Delete ($user->avatar);

        // Delete Comments
        $commentService = new CommentService();
        $commentMapper = new CommentMapper();
        $comments = $commentMapper->getMultipleCommentsByCustom(array('user_id' => $user->userId));
        foreach ($comments as $comment) $commentService->delete($comment);

        // Delete Ratings
        $ratingService = new RatingService();
        $ratingMapper = new RatingMapper();
        $ratings = $ratingMapper->getMultipleRatingsByCustom(array('user_id' => $user->userId));
        foreach ($ratings as $rating) $ratingService->delete($rating);

        // Delete Favorites
//        $query = "SELECT fav_id FROM " . DB_PREFIX . "favorites WHERE user_id = $id";
//        $result = $db->Query ($query);
//        while ($row = $db->FetchObj ($result)) Favorite::Delete ($row->fav_id);

        // Delete Flags
        $flagService = new FlagService();
        $flagMapper = new FlagMapper();
        $flags = $flagMapper->getMultipleFlagsByCustom(array('user_id' => $user->userId));
        foreach ($flags as $flag) $flagService->delete($flag);

        // Delete Subscriptions
        $subscriptionService = new SubscriptionService();
        $subscriptionMapper = new SubscriptionMapper();
        $subscriptions = $subscriptionMapper->getMultipleSubscriptionsByCustom(array('user_id' => $user->userId));
        foreach ($subscriptions as $subscription) $subscriptionService->delete($subscription);

        // Delete Messages
        $messageService = new MessageService();
        $messageMapper = new MessageMapper();
        $messages = $messageMapper->getMultipleMessagesByCustom(array('user_id' => $user->userId));
        foreach ($messages as $message) $messageService->delete($message);

        // Delete Videos
        $videoService = new VideoService();
        $videoMapper = new VideoMapper();
        $videos = $videoMapper->getMultipleVideosByCustom(array('user_id' => $user->userId));
        foreach ($videos as $video) $videoService->delete($video);

        // Delete Privacy
        $privacyService = new PrivacyService();
        $privacyMapper = new PrivacyMapper();
        $privacy = $privacyMapper->getPrivacyByUser($user->userId);
        $privacyService->delete($privacy);

        // Delete User
        $userMapper = $this->_getMapper();
        $userMapper->delete($user->userId);
    }

    /**
     * Get video count Method
     * @return integer Returns the number of approved videos uploaded by the user
     */
    private function getVideoCount()
    {
        $query = "SELECT COUNT(video_id) FROM " . DB_PREFIX . "videos WHERE user_id = $this->user_id AND status = 'approved'";
        $result = $this->db->Query ($query);
        $row = $this->db->FetchRow ($result);
        return $row[0];
    }
    
    /**
     * Generate and save a new password for user
     * @param User $user Instance of user to have password reset
     * @return string Returns user's new password
     */
    public function resetPassword($user)
    {
        $userMapper = new UserMapper();
        $password = Functions::random(10,true);
        $user->password = md5($password);
        Plugin::triggerEvent('user.reset_password');
        $userMapper->save($user);
        return $password;
    }
	
    /**
     * Generate a unique random string for a user account activation token
     * @return string Random user account activation token
     */
    public function createToken()
    {
        $userMapper = new UserMapper();
        do {
            $token = Functions::Random(40);
            if ($userMapper->getUserByCustom(array('confirm_code' => $token))) {
                $token_available = true;
            }
        } while (empty($token_available));
        return $token;
    }
    
    /**
     * Login a user
     * @param string $username Username of user to login
     * @param string $password Password of user to login
     * @return boolean User is logged in, returns true if login succeeded, false otherwise
     */
    public function login($username, $password)
    {
        $userMapper = new UserMapper();
        $user = $userMapper->getUserByCustom(array(
            'username' => $username,
            'password' => md5($password),
            'status' => 'active'
        ));
        if ($user) {
            $_SESSION['loggedInUserId'] = $user->userId;
            $user->lastLogin = date(DATE_FORMAT);
            $userMapper->save($user);
            Plugin::triggerEvent('user.login');
            return true;
        } else {
            return false;
        }
    }

    /**
     *  Log a user out of website
     * @return void
     */
    public function logout()
    {
        unset ($_SESSION['loggedInUserId']);
        Plugin::triggerEvent('user.logout');
    }

    /**
     * Check if user is logged in, with optional redirect
     * @param string $redirect_location optional Location to redirect user if login check fails
     * @return boolean|User Returns instance user if logged in, boolean false otherwise
     */
    public function loginCheck()
    {
        $userMapper = new UserMapper();
        if (!empty($_SESSION['loggedInUserId'])) {
            return $userMapper->getUserById($_SESSION['loggedInUserId']);
        } else {
            return false;
        }
    }

    /**
     * Check if a user has a given permission
     * @param string $permission Name of permission to check
     * @param mixed $userToCheck (optional) User object or ID of user to check permissions for. If null, logged in user is used
     * @return boolean Returns true if user's role has permission, false otherwise
     */
    public function checkPermissions($permission, $userToCheck = null)
    {
        $config = Registry::get('config');
        
        // Retrieve user information
        if (!empty($userToCheck) && $userToCheck instanceof User) {
            $user = $userToCheck;
        } else if (!empty($userToCheck) && is_numeric($userToCheck)) {
            $userMapper = new UserMapper();
            $user = $userMapper->getUserById($userToCheck);
        } else if ($logged_in = $this->loginCheck()) {
            $user = $logged_in;
        } else {
            return false;
        }

        // Check for given permission in user's role
        if (array_key_exists ($user->role, $config->roles)) {
            $permissions_list = $config->roles[$user->role]['permissions'];
            return in_array ($permission, $permissions_list);
        } else {
            return false;
        }
    }

    /**
     * Change the status of a user's content
     * @param User $user Instance of user who's content is getting updated
     * @param string $status The new status being assigned to the user's content
     * @return void User's related records are updated to the new status
     */
    public function updateContentStatus(User $user, $status)
    {
        $db = Registry::get('db');
        switch ($status) {
            case 'banned':
            case 'pending':

                // Set user's videos to 'User Not Available'
                $query = "UPDATE " . DB_PREFIX . "videos SET status = CONCAT('user not available - ',status) WHERE user_id = :userId AND status NOT LIKE 'user not available - %'";
                $db->Query ($query, array(':userId' => $user->userId));

                // Set user's comments to 'User Not Available'
                $query = "UPDATE " . DB_PREFIX . "comments SET status = CONCAT('user not available - ',status) WHERE user_id = :userId AND status NOT LIKE 'user not available - %'";
                $db->Query ($query, array(':userId' => $user->userId));
                break;

            case 'active':

                // Restore user's videos IF/APP
                $query = "UPDATE " . DB_PREFIX . "videos SET status = REPLACE(status,'user not available - ','') WHERE user_id = :userId";
                $db->Query ($query, array(':userId' => $user->userId));

                // Restore user's comments IF/APP
                $query = "UPDATE " . DB_PREFIX . "comments SET status = REPLACE(status,'user not available - ','') WHERE user_id = :userId";
                $db->Query ($query, array(':userId' => $user->userId));
                break;
        }
    }

    /**
     * Make a user visible to the public and notify admin of registration
     * @param User $user User to get updated
     * @param string $action Step in the approval proccess to perform. Allowed values: create|activate|approve
     * @return void User is activated, and admin alerted. If approval is
     * required user is marked pending and placed in queue
     */
    public function approve(User $user, $action)
    {
        $send_alert = false;
        $userMapper = new UserMapper();
        Plugin::triggerEvent('user.before_approve');

        // 1) Admin created user in Admin Panel
        // 2) User signed up & activated
        // 3) User is being approved by admin for first time
        if ((in_array ($action, array ('create','activate'))) || $action == 'approve' && $user->released == 0) {

            // User is activating account, but approval is required
            if ($action == 'activate' && Settings::Get ('auto_approve_users') == '0') {

                // Send Admin Approval Alert
                $send_alert = true;
                $subject = 'New Member Awaiting Approval';
                $body = 'A new member has registered and is awaiting admin approval.';

                // Set Pending
                $user->status = 'pending';
                $userMapper->save($user);
                Plugin::triggerEvent('user.approve_required');
            } else {

                // Send Admin Alert
                if (in_array ($action, array ('create','activate')) && Settings::Get ('alerts_users') == '1') {
                    $send_alert = true;
                    $subject = 'New Member Registered';
                    $body = 'A new member has registered.';
                }

                // Activate & Release
                $user->status = 'active';
                $user->released = true;
                $userMapper->save($user);

                // Update user's anonymous comments IF/APP
                $query = "UPDATE " . DB_PREFIX . "comments SET user_id = ? WHERE email = ?";
                Registry::get('db')->query($query, array($user->userId, $user->email));

                // Send Welcome email
                if ($action == 'approve') {
                    App::LoadClass ('Mail');
                    $mail = new Mail();
                    $mail->LoadTemplate ('account_approved', array('sitename' => Registry::get('config')->sitename));
                    $mail->Send ($user->email);
                }

                Plugin::triggerEvent('user.release');
            }

        // User is being re-approved
        } else if ($action == 'approve' && $user->released) {
            // Activate User
            $user->status = 'active';
            $userMapper->save($user);
            Plugin::triggerEvent('user.reapprove');
        }

        // Send admin alert
        if ($send_alert) {
            $body .= "\n\n=======================================================\n";
            $body .= "Username: $user->username\n";
            $body .= "Profile URL: " . HOST . "/members/$user->username/\n";
            $body .= "=======================================================";
            App::Alert ($subject, $body);
        }

        Plugin::triggerEvent('user.approve');
    }
    
    /**
     * Retrieve URL to current user's avatar
     * @param User Instance of user who's avatar is wanted
     * @return string URL to user's uploaded avatar or default theme avatar if none is set
     */
    public function getAvatarUrl(User $user)
    {
        return (!empty($user->avatar)) ? HOST . "/cc-content/uploads/avatars/$user->avatar" : null;
    }
    
    /**
     * Retrieve a list of users who are subscribed to given member
     * @param User $member Instance of user whose subscribers will be retrieved
     * @return array Returns list of User objects 
     */
    public function getSubscribedUsers(User $member)
    {
        $userMapper = $this->_getMapper();
        $subscriptionMapper = new SubscriptionMapper();
        $subscriptionList = $subscriptionMapper->getMultipleSubscriptionsByCustom(array('member' => $member->userId));
        $userIdList = array();
        foreach ($subscriptionList as $subscription) {
            $userIdList[] = $subscription->userId;
        }
        return $userMapper->getUsersFromList($userIdList);
    }
    
    /**
     * Retrieve instance of User mapper
     * @return UserMapper Mapper is returned
     */
    protected function _getMapper()
    {
        return new UserMapper();
    }
}