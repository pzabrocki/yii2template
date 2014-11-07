<?php

/* 
 * Helpers are special classes we create for formatting and returning certain values, that help us move development along quickly with reusable code.
 * In order to control access, we need to be able to extract the values we want.
 * To help make things easier on myself, we create a number of methods that will return the values that we will need for more complex operations.
 */

namespace common\models;

use yii;

Class RecordHelpers

{
    
    /**
     * return the value of role name as string
     * example: "Admin"
     * @param mixed $role_name
     * 
     * What I have planned for our application is a user profile and I want a Profile link that when you click on it, figures out whether or not the user has a profile or if they need to create one.
       I wanted to keep the syntax in my controller very intuitive and have the result formatted to either false or the record id. That way if it comes back false, I can have the user create the record, and if it returns the id of the record because the user already has one, I can render that view. Something like:
        If ($already_exists = RecordHelpers::userHas('profile') { // show profile with id with value of $already_exists
        } else { // go to form to create profile
        }
       This kind of syntax makes it incredibly easy to understand what is happening here. If the if statement returns a record id, show the profile with that record id, which is now referenced by the variable $already_exists. If it comes back false, go to the create form.
       I also wrote it so I could use it with other models, I just need to hand in the model name as string.
     */
    public static function userHas($model_name)
    {
        $connection = \Yii::$app->db;
        $userid = Yii::$app->user->identity->id;
        $sql = "SELECT id FROM $model_name WHERE user_id=:user_id";
        $command = $connection->createCommand($sql);
        $command -> bindValue(":userid", $userid);
        $result = $command->queryOne();
        
        if ($result == null) {
            return false;
        }
        else {
            return $result['id'];
        }
    }

}