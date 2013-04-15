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

    /**
     * 设置UC user
     * @param UC_IUser $user
     */
    public function setUser(UC_IUser $user)
    {
        if($user==null){
            return;
        }
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

    /**
     * 上传头像，返回上传头像的客户端代码，可以使用默认的样式，
     * 也可通过$returnhtml设置为false获得一个配置客户端配置的数组进行自定义。
     * @param  number  $uid
     * @param  string $type  [virtual/real]
     * @param  boolean  $returnhtml
     * @return mixed
     */
    public  function  avatar($uid, $type = 'virtual', $returnhtml = true){
        return uc_avatar($uid, $type, $returnhtml);
    }
    /**
     * 检查email,格式，是否被占用
     * @param $email string
     * @return string 验证消息
     */
    public function   checkEmail($email){
        switch(uc_user_checkemail($email)){
            case  1:$msg='';break;
            case -4:
                $msg='请输入正确的Email格式';break;
            case -5:
                $msg='Email不允许注册';break;
            case  -6:
                $msg='Email已被注册，请更换Email';break;
            default:
                $msg='未知错误';
        }
        return $msg;
    }

    /**
     * 检查用户名
     * @param $username 需要验证的用户名
     * @return string 返回验证消息
     */
    public  function  checkUsername($username){
        switch(uc_user_checkname($username)){
            case  1: $msg='';break;
            case -1:
                $msg='用户名不合法，为 3 - 15个字符';break;
            case -2:
                $msg='包含不允许注册的词语';break;
            case -3:
                $msg='用户名已经存在,请更换';break;
            default:
                $msg='未知错误';
        }
        return $msg;
    }

    /**
     * 检查头像是否存在
     * @param $uid 用户uid
     * @param string $size 头像尺寸
     * @param string $type 头像类型
     * @return int 头像存在返回1否则返回0
     */
    public  function  checkAvatar($uid,$size='middle',$type='virtual'){
        return uc_check_avatar($uid,$size,$type);
    }

    /**
     * 获取用户头像
     * @param $uid 用户uid
     * @param string $size 头像尺寸
     * @param string $type 是否调用真实头像
     * @return string 返回头像url
     */
    public  function  getAvatar($uid,$size='middle',$type='virtual'){
        if($type=='real'){
            return UC_API."/avatar.php?uid=$uid&size=$size&type=$type";
        }
        return UC_API."/avatar.php?uid=$uid&size=$size";
    }
}
