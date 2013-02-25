<?php
/**
 * Created by JetBrains PhpStorm.
 * User: weijie
 * Date: 13-2-21
 * Time: 上午11:36
 * File: WebUser.php
 * To change this template use File | Settings | File Templates.
 */
/**
 * @property User $ucUser
 * @property UCenter $uc
 */
class WebUser extends UC_WebUser
{
    public function init(){
        $this->ucUser=new User();
        parent::init();
    }
}
