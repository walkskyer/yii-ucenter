<?php
/**
 * Created by JetBrains PhpStorm.
 * User: weijie
 * Date: 13-2-25
 * Time: 上午11:03
 * File: UC_UserIdentity.php
 * To change this template use File | Settings | File Templates.
 */
class UC_UserIdentity extends CUserIdentity
{
    /**
     * @var UC_IUser
     */
    protected $_user;
    public function __construct($username,$password){
        parent::__construct($username,$password);
    }
    public function setUser($username,$password){
        $this->username=$username;
        $this->password=$password;
    }
    /**
     * Authenticates a user.
     * The example implementation makes sure if the username and password
     * are both 'demo'.
     * In practical applications, this should be changed to authenticate
     * against some persistent user identity storage (e.g. database).
     * @return boolean whether authentication succeeds.
     */
    public function authenticate()
    {
        /* @var UC_IUser $user*/
        $user=$this->_user->findByName($this->username);
        /* @var UCenter $uc*/
        $uc=Yii::app()->user->uc;
        $uc->login($this->username,$this->password);
        if($user!==null){
            if($user->getUserName()!==$this->username)
                $this->errorCode=self::ERROR_USERNAME_INVALID;
            elseif($user->getPassword()!==$this->password || $uc->ucUser->uid===-1)//密码不一致有两种情况，暂时不区分处理。
                $this->errorCode=self::ERROR_PASSWORD_INVALID;
            else{
                $this->errorCode=self::ERROR_NONE;
                if($uc->ucUser->uid===-2){
                    $uc->add($user->getUserName(),$user->getPassword(),$user->getEmail());//此处暂不处理注册的异常
                }
            }
        }elseif($user===null && $uc->ucUser->uid>0){
            $class=get_class($this->_user);
            $user=new $class();
            $ucUser=$uc->ucUser;
            $data=array(
                'uid'=>$ucUser->uid,
                'username'=>$ucUser->username,
                'password'=>$ucUser->password,
                'email'=>$ucUser->email,
            );
            $this->username=$ucUser->username;
            $this->password=$ucUser->password;
            $user->add($data);
            $this->errorCode=self::ERROR_NONE;
        }elseif($user===null && $uc->ucUser->uid<=0){
            $this->errorCode=$uc->ucUser->uid;
            $this->errorMessage=$uc->loginMsg;
            return false;
        }
        return !$this->errorCode;
    }

    /**
     * 这是一个与数据源无关的验证方法的示例。
     */
    public function authenticate2()
    {
        /* @var UCenter $uc*/
        $uc = Yii::app()->user->uc;
        $uc->login($this->username, $this->password);
        if ($uc->ucUser->uid > 0) {
            $ucUser = $uc->ucUser;
            $this->username = $ucUser->username;
            $this->password = $ucUser->password;
            $this->errorCode = self::ERROR_NONE;
        } elseif ($uc->ucUser->uid == -1) {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        } elseif ($uc->ucUser->uid == -2) { //密码不一致有两种情况，暂时不区分处理。
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        } else { //未定义错误
            $this->errorCode = self::ERROR_UNKNOWN_IDENTITY;
        }
        return !$this->errorCode;
    }
}
