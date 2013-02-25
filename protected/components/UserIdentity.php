<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
/**
 * @property User $userInfo
 */
class UserIdentity extends UC_UserIdentity
{
    public function __construct($username,$password){
        parent::__construct($username,$password);
        $this->_user=new User();
    }
}