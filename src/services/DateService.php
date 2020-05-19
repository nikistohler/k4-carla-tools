<?php


namespace k4\k4carlatools\services;


use craft\base\Component;
use craft\db\Connection;
use craft\web\View;
use DateTime;
use k4\k4carlatools\models\SettingsModel;
use Kint\Kint;
use yii\mail\MailerInterface;

/**
 * Class DateService
 * @package k4\k4carlatools\services
 * @property SettingsModel $settings
 */
class DateService extends Component
{
    public $settings;

    public function getWantedWeekStartAndEndDate(){
        return $this->getStartAndEndDateOfWeek(
            $this->getWantedWeekNumber(),
            $this->getCurrentYear()
        );
    }


    public function getStartAndEndDateOfWeek($week, $year) {
        $dto = new DateTime();
        $ret['weekNumber'] = $week;
        $ret['weekYear'] = $year;
        $ret['weekStart'] = clone $dto->setISODate($year, $week)->setTime(0,0,0);
        $ret['weekEnd'] = clone $dto->modify('+6 days')->setTime(23,59,59);
        return $ret;
    }

    public function getCurrentWeekNumber(){

        return (new DateTime)->format("W");
    }

    public function getWantedWeekNumber()
    {
        if ($this->settings->lastWeek == true)
            return $this->getLastWeekNumber();

        return $this->getCurrentWeekNumber();

    }

    public function getLastWeekNumber(){

        return (new DateTime)->modify('last sunday')->format("W");
    }



    public function getCurrentYear(){

        return (new DateTime)->format("Y");
    }

}
