<?php


namespace k4\k4carlatools\models;


use craft\base\Model;

class SettingsModel extends Model {

    /**
     * @var Integer $groupId
     */
    public $groupId;

    /**
     * @var String $groupName
     */
    public $groupName;

    /**
     * @var String $username
     */
    public $username;

    /**
     * @var String $password
     */
    public $password;

    /**
     * @var String $email
     */
    public $email;

    /**
     * @var Boolean $lastWeek
     */
    public $lastWeek;

}
