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
     * @var string $username
     */
    public $username;

    /**
     * @var string $password
     */
    public $password;

    /**
     * @var string $email
     */
    public $email;

}
