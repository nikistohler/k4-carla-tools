<?php

namespace k4\k4carlatools\console\controllers;

use craft\helpers\Console;
use k4\k4carlatools\K4CarlaTools;
use Throwable;
use yii\console\Controller;
use yii\console\ExitCode;

class SendController extends Controller
{
    /**
     * @var K4CarlaTools $module
     */
    public $module;

    /**
     * Send mail
     *
     * @return int
     * @throws Throwable
     */
    public function actionMail(): int
    {
        $result = $this->module->message->sendMeeps(
            $this->module->settings->email,
            $this->module->carla->getMeepsFromLastWeek(),
            $this->module->date->getLastWeekStartAndEndDate()
        );

        $text = $result ? '' : 'not ';

        $this->stdout('Emails '.$text.'sent.'.PHP_EOL, Console::FG_GREEN);

        return ExitCode::OK;
    }
}
