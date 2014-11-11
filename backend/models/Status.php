<?php

namespace backend\models;

use Yii;
use common\models\User;

/**
 * This is the model class for table "status".
 *
 * @property string $status_name
 * @property integer $status_value
 *
 * @property User[] $users
 */
class Status extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'status';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status_name', 'status_value'], 'required'],
            ['status_name', 'string', 'max' => 45],
            ['status_value', 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'status_name' => Yii::t('app', 'Status Name'),
            'status_value' => Yii::t('app', 'Status Value'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['status' => 'status_value']);
        // status on the user table is mapped to status_value in the status table
    }
}
