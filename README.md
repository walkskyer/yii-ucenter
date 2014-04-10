yii-UCenter
=======
基于UCenter的Yii扩展

<h1>yii-ucenter用法：</h1>
<p>重中之重：修改uc的配置文件ucenter/api/config.inc.bak.php名字为config.inc.php，并修改为正确的配置信息。</p>
<p>1、在配置文件中引用yii-ucenter：</p>
<ul>
    <li><code>
        'import'=>array('ext.ucenter.interface.UC_IUser',<br>
        'ext.ucenter.class.*',<br>
        'ext.ucenter.UCenter',<br>),</code></li>
</ul>
<p>2、用你的用户模型实现UC_IUser接口:</p>
<ul>
    <li><code>
        class User extends Model implements UC_IUser<br>
        {<br>
        ……<br>
        public function getUserName(){<br>
        return $this->username;<br>
        }<br>
        /**<br>
        * 取得uid<br>
        * @return int<br>
        */<br>
        public function getUid(){<br>
        return $this->uid;<br>
        }<br>
        }<br>
        ……<br>
    </code></li>
</ul>
<p>3、继承UC_WebUser,并创建用户模型实例给ucUser:</p>
<ul>
    <li><code>
        class WebUser extends UC_WebUser<br>
        {<br>
        public function init(){<br>
        $this->ucUser=new User();<br>
        parent::init();<br>
        }<br>
        }<br>
    </code></li>
    <li>然后将创建的WebUser用例实现Yii::app()->user。</li>
</ul>
<p>4、继承UC_UserIdentity,并创建用户模型实例给_user:</p>
<ul>
    <li><code>
        class UserIdentity extends UC_UserIdentity<br>
        {<br>
        public function __construct($username,$password){<br>
        parent::__construct($username,$password);<br>
        $this->_user=new User();<br>
        }<br>
        }<br>
    </code></li>
    <li>在登录验证时使用UserIdentity的实例进行验证。</li>
</ul>