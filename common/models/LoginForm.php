<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in an active user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate() && $this->getUser()->status_id == ValueHelpers::getStatusValue('Active')) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }
    
    /**
     * Control access to the admin login by requiring the user to have at least a role of admin and an active status.
     * @return type
     * @throws NotFoundHttpException
     */
    public function loginAdmin() 
    {
        if (($this->validate()) && $this->getUser()->role_id >= ValueHelpers::getRoleValue('Admin') && $this->getUser()->status_id == ValueHelpers::getStatusValue('Active')) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            throw new NotFoundHttpException('You Shall Not Pass.');
        }       

    }       

    /**
     * Finds user by [[username]]
     * Since we know the private attribute $_user defaults to false, 
     * the condition in the if statement is going to be met if this method has not already been run. 
     * So if there is no username in $_user, then it uses a static method of the User model 
     * to return a model instance of the user and set it to $_user.
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
