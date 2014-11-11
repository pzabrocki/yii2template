<?php

/* 
 * Helpers are special classes we create for formatting and returning certain values, that help us move development along quickly with reusable code.
 * In order to control access, we need to be able to extract the values we want.
 * Number of methods that will return the values that we will need for more complex operations.
 */

namespace common\models;

Class ValueHelpers

{
    
    /**
     * return the value of role name as string
     * example: "Admin"
     * @param mixed $role_name
     */
    public static function getRoleValue($role_name) // it can be called as: ValueHelpers::getRoleValue('Admin');
    {
        $connection = \Yii::$app->db;
        $sql = "SELECT role_value FROM role WHERE role_name=:role_name"; // raw sql return results faster
        $command = $connection->createCommand($sql);
        $command->bindValue(":role_name", $role_name);
        $result = $command->queryOne();
        
        return $result['role_value'];
    }
    
    /**
     * return the value of status name as string
     * example: "Active"
     * @param mixed $status_name
     */
    public static function getStatusValue($status_name)
    {
        $connection = \Yii::$app->db;
        $sql = "SELECT status_value FROM status WHERE status_name=:status_name"; // raw sql return results faster
        $command = $connection->createCommand($sql);
        $command->bindValue(":status_name", $status_name);
        $result = $command->queryOne();
        
        return $result['status_value'];
    }
    
    /**
     * return the value of user_type_name as string
     * so that it can be used in PermissionHelpers methods
     * example: "Paid"
     * @param mixed $user_type_name
     */
    public static function getUserTypeValue($user_type_name)
    {
        $connection = \Yii::$app->db;
        $sql = "SELECT user_type_value FROM user_type WHERE user_type_name=:user_type_name"; // raw sql return results faster
        $command = $connection->createCommand($sql);
        $command->bindValue(":user_type_name", $user_type_name);
        $result = $command->queryOne();

        return $result['user_type_value'];
    }

}