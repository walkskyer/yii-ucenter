<?php
/**
 * Created by JetBrains PhpStorm.
 * User: weijie
 * Date: 13-2-17
 * Time: 下午3:36
 * File: UCenter.php
 * To change this template use File | Settings | File Templates.
 */
/**
 * @property UC_IUser $user
 * @property UC_User $ucUser
 */
class UCenter extends CComponent
{
    public $user;
    public $ucUser;
    public $loginMsg;

    public function __construct()
    {
        $this->ucUser=new UC_User();
    }
    public function test(){
        echo 'Hello! The UCenter works!';
    }
    public function init(){
        define('UC_PATH',dirname(__FILE__));
        require_once(UC_PATH.'/api/config.inc.php');
        require_once(UC_PATH.'/uc_client/client.php');

    }
    /**
     * 同步验证 ，如果uid和username同时不为空返回true；如果cookie中有用户数据，返回true；
     */
    public function synchronize_validate() {
        if($this->ucUser->uid >0 && $this->ucUser->username != '') {
            return true;
        }
        $request=Yii::app()->getRequest();
        $cookie=$request->getCookies()->itemAt(UC_COOKIE_NAME);
        if ($cookie != null) {
            list($this->ucUser->uid, $this->ucUser->username) = explode("\t", uc_authcode($cookie, 'DECODE'));
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * ucenter登陆
     * @param $username
     * @param $pwd
     */
    public function login($username,$pwd){
        if(!$username){
            $username=$_POST['username'];
        }
        if(!$pwd){
            $pwd=$_POST['password'];
        }
        list($this->ucUser->uid, $this->ucUser->username, $this->ucUser->password, $this->ucUser->email) =
            uc_user_login($username, $pwd);
        if($this->ucUser->uid > 0) {
            $this->loginMsg= '登录成功！';
        } elseif($this->ucUser->uid == -1) {
            $this->loginMsg= '用户不存在,或者被删除！';
        } elseif($this->ucUser->uid == -2) {
            $this->loginMsg= '密码错误，请重新输入！';
        } else {
            $this->loginMsg= '未定义错误！';
        }
    }

    /**
     * 向ucenter中添加用户
     * @param $useranme
     * @param $password
     * @param $email
     * @return mixed
     */
    public function add($useranme,$password,$email){
        return uc_user_register($useranme,$password,$email);
    }

    /**
     * 删除ucenter中的用户
     */
    public function deleteUser(){
        $uids = explode(',', str_replace("'", '', $_GET['ids']));
        !API_DELETEUSER && exit(API_RETURN_FORBIDDEN);

        $users = $this->user->findAllByUid($uids);
        /* @var UC_IUser $user*/
        foreach($users as $user)
        {
            $user->delete();
        }

        echo API_RETURN_SUCCEED;
    }

    public function setUser(UC_IUser $user)
    {
        $this->user = $user;
        $user=$this->user->getUser();
        $this->ucUser->uid=$user['uid'];
        $this->ucUser->username=$user['username'];
        $this->ucUser->password=$user['pwd'];
        $this->ucUser->email=$user['email'];
    }

    /**
     * 获取同步登陆代码
     * @return string
     */
    public function synLogin(){

        if(!API_SYNLOGIN) {
            echo API_RETURN_FORBIDDEN;
        }
        return uc_user_synlogin($this->ucUser->uid);
    }

    /**
     * 获取同步登出代码
     * @return string
     */
    public function synLogout(){

        if(!API_SYNLOGOUT) {
            echo API_RETURN_FORBIDDEN;
        }
        return uc_user_synlogout($this->ucUser->uid);
    }
}
