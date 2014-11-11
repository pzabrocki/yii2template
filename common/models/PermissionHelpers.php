<?php

/* 
 * Helpers are special classes we create for formatting and returning certain values, that help us move development along quickly with reusable code.
 * In order to control access, we need to be able to extract the values we want.
 * To help make things easier on myself, we create a number of methods that will return the values that we will need for more complex operations.
 */

namespace common\models;

use yii;
use yii\web\Controller;
use yii\helpers\Url;

Class PermissionHelpers

{
    
    /**
     * check if the user is the owner of the record
     * use Yii::$app->user->identity->id for $userid, 'string' for model name
     * for example 'profile' will check the profile model to see if the user owns the record.
     * Provide the model instance, typically as $model->id as the last parameter.
     * Returns true or false, so you can wrap in if statement.
     * @param mixed $userid
     * @param mixed $model_name
     * @param mixed $model_id
     */
    public static function userMustBeOwner($model_name, $model_id)
    {
        $connection = \Yii::$app->db;
        $userid = Yii::$app->user->identity->id;
        $sql = "SELECT id FROM $model_name WHERE user_id=:user_id AND id=:model_id"; // raw sql return results faster
        $command = $connection->createCommand($sql);
        $command->bindValue(":user_id", $userid);
        $command->bindValue(":model_id", $model_id);
        if($result = $command->queryOne()) {
            return true;
        }
        else {
            return false;
        }
        
    }
    
    /**
     * method for requiring paid type user, if test fails, redirect to upgrade page
     * $user_type_name handed in as 'string', 'Paid' for example.
     * @param mixed $user_type_name
     * @return \yii\web\Response
     */
    public static function requireUpgradeTo($user_type_name)
    {
        if (Yii::$app->user->identity->user_type != ValueHelpers::getUserTypeValue($user_type_name)) {
            return Yii::$app->getResponse()->redirect(Url::to(['upgrade/index']));
        }
    }
    
    /**
     * This method will come in handy insife of controllers to check if the user has sufficient status to access the page.
     * @requireStatus
     * @param mixed $status_name
     */
    public static function requireStatus($status_name)
    {
        if (Yii::$app->user->identity->status == ValueHelpers::getStatusValue($status_name)) {
            return true;
        }
        else {
            return false;
        }
    }
    
    /**
     * The same like above, but allows us to set a minimum required status.
     * @requireMinimumStatus
     * @param mixed $status_name
     */
    public static function requireMinimumStatus($status_name)
    {
        if (Yii::$app->user->identity->status >= ValueHelpers::getStatusValue($status_name)) {
            return true;
        }
        else {
            return false;
        }
    }
    
    /**
     * Checks if a user has a required role.
     * @requireRole
     * @param mixed $role_name
     */
    public static function requireRole($role_name)
    {
        if (Yii::$app->user->identity->role == ValueHelpers::getRoleValue($role_name)) {
            return true;
        }
        else {
            return false;
        }
    }
    
    /**
     * This can be used if you want for example Admin AND SuperAdmin to be able to access the backend. 
     * @requireMinimumRole
     * @param mixed $role_name
     */
    public static function requireMinimumRole($role_name)
    {
        if (Yii::$app->user->identity->role >= ValueHelpers::getRoleValue($role_name)) {
            return true;
        }
        else {
            return false;
        }
    }

    
    
}