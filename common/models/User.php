<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use backend\models\Role; // gives access to our Role model
use backend\models\Status; // gives access to our Role model
use backend\models\UserType; // gives access to our UserType model
use frontend\models\Profile;
use yii\helpers\Arrayhelper;
use yii\helpers\Url;
use Yii\helpers\Html;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $role_id
 * @property integer $status_id
 * @property integer $user_type_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{   
    // default values
    const STATUS_ACTIVE = 10;
    const ROLE_USER = 10;
    const USER_TYPE = 10;
  
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii/behaviors/TimestampBehavior',
                'attributes' => [
                ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status_id', 'default', 'value' => self::STATUS_ACTIVE],
            ['status_id', 'in', 'range'=>array_keys($this->getStatusList())], // accept status_id values that are in range of the values in the status_value field in the status table. The array keys are the status_value records.
            
            ['role_id', 'default', 'value' => self::ROLE_USER],
            ['role_id', 'in', 'range'=>array_keys($this->getRoleList())], // accept role_id values that are in range of the values in the role_value field in the role table.
            
            ['user_type_id', 'default', 'value' => self::USER_TYPE],
            ['user_type_id', 'in', 'range'=>array_keys($this->getUserTypeList())], 
            
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique'],
            ['username', 'string', 'min' => '2', 'max' => '255'],
            
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'unique'],
            ['email','email'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'roleName' => Yii::t('app', 'Role'), // in some cases the attribute is the name of a method. roleName is alias for getRoleName method
            'statusName' => Yii::t('app', 'Status'),
            'profileId' => Yii::t('app', 'Profile'),
            'profileLink' => Yii::t('app', 'Profile'),
            'userLink' => Yii::t('app', 'User'),
            'username' => Yii::t('app', 'User'),
            'userTypeName' => Yii::t('app', 'User Type'),
            'userTypeId' => Yii::t('app', 'User Type'),
            'userIdLink' => Yii::t('app', 'ID'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status_id' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status_id' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status_id' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
    
    /**
     * get role relationship
     */
    public function getRole() 
    {
        return $this->hasOne(Role::className(), ['role_value' => 'role_id']);
        // Users only have one role, so it's hasOne relationship. 
        // role_value on the role table is mapped to role_id in the user table (opposite to getUsers in Role model)
    }
    
    /**
     * get role name
     */
    public function getRoleName()
    {
        return $this->role ? $this->role->role_name : '- no role -';
    }
    
    /**
     * return a list of role values and names to use in dropdown lists in the UI
     */
    public static function getRoleList()
    {
        $droptions = Role::find()->asArray->all();
        return Arrayhelper::map($droptions, 'role_value', 'role_name');
    }
    
    /**
     * get status relationship
     */
    public function getStatus() 
    {
        return $this->hasOne(Status::className(), ['status_value' => 'status_id']);
        // Users only have one status, so it's hasOne relationship. 
        // status_value on the status table is mapped to status_id in the user table (opposite to getUsers in Status model)
    }
    
    /**
     * get status name
     */
    public function getStatusName()
    {
        return $this->status ? $this->status->status_name : '- no status -';
    }
    
    /**
     * return a list of statuses to use in dropdown lists in the UI
     */
    public static function getStatusList()
    {
        $droptions = Status::find()->asArray->all();
        return Arrayhelper::map($droptions, 'status_value', 'status_name');
    }
    
    
    /**
     * get userType
     */
    public function getUserType() 
    {
        return $this->hasOne(UserType::className(), ['user_type_value' => 'user_type_id']);
    }
    
    /**
     * get user type name
     */
    public function getUserTypeName()
    {
        return $this->userType ? $this->userType->user_type_name : '- no user type -';
    }
    
    /**
     * return a list of user types to use in dropdown lists in the UI
     */
    public static function getUserTypeList()
    {
        $droptions = UserType::find()->asArray->all();
        return Arrayhelper::map($droptions, 'user_type_value', 'user_type_name');
    }
    
    /**
     * get user type name
     */
    public function getUserTypeId()
    {
        return $this->userType ? $this->userType->id : 'none';
    }
    
    /**
     * @getProfile
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'id']);
    }
    
    /**
     * $getProfileId
     */
    public function getProfileId()
    {
        return $this->profile ? $this->profile->id : 'none';
    }
    
    /**
     * @getProfileLink
     */
    public function getProfileLink()
    {
        $url = Url::to(['profile/view', 'id'=>$this->profileId]);
        $options = [];
        return Html::a($this->profile ? 'profile' : 'none', $url, $options);
    }
    
    /**
     * get user id link
     */
    public function getUserIdLink()
    {
        $url = Url::to(['user/update', 'id'=>$this->id]);
        $options = [];
        return Html::a($this->id, $url, $options);
    }
    
    /**
     * @getUserLink
     */
    public function getUserLink()
    {
        $url = Url::to(['user/view', 'id'=>$this->id]);
        $options = [];
        return Html::a($this->username, $url, $options);
    }
    
}
