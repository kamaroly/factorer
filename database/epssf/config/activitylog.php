<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Also log to Laravel's default log handler
    |--------------------------------------------------------------------------
    |
    | If "alsoLogInDefaultLog" the activity will also be logged in the default
    | Laravel logger handler
    |
    */
    'alsoLogInDefaultLog' => true,

    /*
    |--------------------------------------------------------------------------
    | Max age in months for log records
    |--------------------------------------------------------------------------
    |
    | When running the cleanLog-command all recorder older than the number of months
    | specified here will be deleted
    |
    */
    'deleteRecordsOlderThanMonths' => 2,

    /*
    |--------------------------------------------------------------------------
    | Fallback user id if no user is logged in
    |--------------------------------------------------------------------------
    |
    | If you don't specify a user id when logging some activity and no
    | user is logged in, this id will be used.
    |
    */
    'defaultUserId' => '',

    /*
    |--------------------------------------------------------------------------
    | Model for the users
    |--------------------------------------------------------------------------
    |
    | While saving or retrieving activity per user, we need to know which model
    | we are relating to, this model will be used
    |
    */
    'activity_user_model' => '\Ceb\Models\User',

    /*
    |--------------------------------------------------------------------------
    | Verify if the user is authenticated by this model
    |--------------------------------------------------------------------------
    |
    | Before we do any activity log, we need to know if the user who is doing it
    | is authenticated or not, if he is authenticate then we get his id
    |
    */
    'activity_auth_model' => '\Sentry::check', // For example this is same as Sentry::check()

    /*
    |--------------------------------------------------------------------------
    | Authenticated user id object
    |--------------------------------------------------------------------------
    |
    | For us to know if we need to use the logged in user for logs, we need to 
    | first now if the user is authenticated or not.
    |
    */
    'activity_authenticated_user_model' => '\Sentry::getUser',

    /*
    |--------------------------------------------------------------------------
    | User id field to be called 
    |--------------------------------------------------------------------------
    |
    | In other to avoid undefiened property errors, we will call activity model
    | with this defined field if the user is authenticated only
    |
    */
    'user_id_field_name' => 'id',

    /*
    |--------------------------------------------------------------------------
    | User id field to be called 
    |--------------------------------------------------------------------------
    |
    | Eloquent models fire several events, allowing you to hook into various 
    | points in the model's lifecycle using the following methods  Events
    | allow you to easily execute code each time a specific model class 
    | is saved or updated in the database.with this defined field
    | if the user is authenticated only
    |
    */
    'record_activity_events' => [
                                    // 'creating', // Whenever a new model is saved for the first time
                                    'created',     // Whenever a new model is saved for the first time
                                    // 'updating', // If a model already existed in the database
                                    'updated',     // If a model already existed in the database
                                    // 'saving',   // If a model already existed in the database and the save method is called
                                    //'saved',      // If a model already existed in the database and the save method is called
                                    'deleting',    
                                    'deleted', 
                                    // 'restoring', 
                                    // 'restored'
                                ],

];
