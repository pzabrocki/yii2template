<?php

namespace backend\models;

use Yii;
use common\models\User;

/**
 * This is the model class for table "user_type".
 *
 * @property integer $id
 * @property string $user_type__name
 * @property integer $user_type_value
 *
 * @property User[] $users
 */
class UserType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_type__name', 'user_type_value'], 'required'],
            ['user_type__name', 'string', 'max' => 45],
            ['user_type_value', 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_type__name' => Yii::t('app', 'User Type  Name'),
            'user_type_value' => Yii::t('app', 'User Type Value'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['user_type' => 'user_type_value']);
        // user_type on the user table is mapped to user_type_value in the user_type table
    }
}
