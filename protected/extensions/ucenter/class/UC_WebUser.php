<?php
/**
 * Created by JetBrains PhpStorm.
 * User: weijie
 * Date: 13-2-25
 * Time: 上午9:26
 * File: UC_WebUser.php
 * To change this template use File | Settings | File Templates.
 */
/**
 * @property UC_IUser $ucUser
 */
class UC_WebUser extends CWebUser
{
    public $ucUser;
    public $uc;

    public function init()
    {
        parent::init();
        $this->uc = new UCenter();
        $this->uc->init();
        if ($this->getIsGuest() && $this->uc->synchronize_validate()) {
            /* @var UC_IUser $user*/
            $user=$this->ucUser->findByName($this->uc->ucUser->username);
            $identity = new UserIdentity($user->getUserName(), $user->getPassword());
            $duration = false ? 3600 * 24 * 30 : 0; // 30 days
            $this->setUCUser($user);
            $this->login($identity, $duration);
        }elseif(!$this->uc->synchronize_validate() && !$this->getIsGuest()){
            $this->logout();
        } else {
            $this->ucUser = $this->getUCUser();
            $this->uc->setUser($this->ucUser);
        }
    }
    protected function restoreFromCookie()
    {
        $app = Yii::app();
        $request = $app->getRequest();
        $cookie = $request->getCookies()->itemAt($this->getStateKeyPrefix());
        if ($cookie && !empty($cookie->value) && is_string($cookie->value) && ($data = $app->getSecurityManager()->validateData($cookie->value)) !== false) {
            $data = @unserialize($data);
            if (is_array($data) && isset($data[0], $data[1], $data[2], $data[3], $data[4])) {
                list($id, $name, $duration, $states, $userInfo) = $data;
                if ($this->beforeLogin($id, $states, true)) {
                    $this->changeIdentity($id, $name, $states);
                    if ($this->autoRenewCookie) {
                        $cookie->expire = time() + $duration;
                        $request->getCookies()->add($cookie->name, $cookie);
                    }
                    $this->afterLogin(true);
                }
            }
        }
    }

    protected function saveToCookie($duration)
    {
        $app = Yii::app();
        $cookie = $this->createIdentityCookie($this->getStateKeyPrefix());
        $cookie->expire = time() + $duration;
        $data = array(
            $this->getId(),
            $this->getName(),
            $duration,
            $this->saveIdentityStates(),
            $this->ucUser,
        );
        $cookie->value = $app->getSecurityManager()->hashData(serialize($data));
        $app->getRequest()->getCookies()->add($cookie->name, $cookie);
    }

    protected function changeIdentity($id, $name, $states)
    {
        Yii::app()->getSession()->regenerateID(true);
        $this->setId($this->ucUser->id);
        $this->setName($name);
        $this->setUCUser();
        $this->loadIdentityStates($states);
    }

    public function setUCUser(UC_User $ucuser=null)
    {
        if($ucuser!==null){
            $this->ucUser=$ucuser;
        }
        $this->setState('__UCUser', $this->ucUser);
    }

    public function getUCUser()
    {
        if (($name = $this->getState('__UCUser')) !== null)
            return $name;
        else
            return $this->ucUser;
    }
}
