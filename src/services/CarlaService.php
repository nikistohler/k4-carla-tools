<?php


namespace k4\k4carlatools\services;


use craft\base\Component;
use craft\db\Connection;
use craft\web\View;
use DateTime;
use GuzzleHttp\Client;
use k4\k4carlatools\models\SettingsModel;
use Kint\Kint;
use yii\db\Exception;
use yii\mail\MailerInterface;

/**
 * Class CarlaService
 * @package k4\k4carlatools\services
 *
 * @property Connection $db
 * @property MailerInterface $mailer
 * @property View $view
 * @property DateService $date
 * @property SettingsModel $settings
 */
class CarlaService extends Component
{
    /**
     * @var Connection $db
     */
    public $db;

    /**
     * @var View $view
     */
    public $view;

    /**
     * @var MailerInterface $mailer
     */
    public $mailer;

    /**
     * @var DateService $date
     */
    public $date;

    /**
     * @var SettingsModel $date
     */
    public $settings;

    public function getMeeps($limit, $offset = false)
    {

        $link = "https://projects.kreisvier.ch/api/v1/projects/".$this->settings->groupId."/meeps";

        $data = array('limit' => $limit);
        if (!empty($offset)) {
            $data["offset"] = $offset;
        }

        try {
            $client = new Client();

            $res = $client->get(
                $link,
                array(
                    'query' => $data,
                    'auth' => array(
                        $this->settings->username,
                        $this->settings->password
                    ),
                    'timeout' => 20, // Response timeout
                    'connect_timeout' => 20 // Connection timeout
                ));

            if ($res->getStatusCode() != 200)
            {
                throw new Exception($res->getStatusCode());
            }

            return json_decode($res->getBody()->getContents(), true)["meeps"];


        } catch (\Exception $e)
        {
            throw new Exception($e->getMessage());
        }


    }

    public function isLastMeepWithinLastWeek($meeps){

        $last = DateTime::createFromFormat(DATE_ATOM, end($meeps)["created_at"]);

        $week = $this->date->getLastWeekStartAndEndDate();

        return ($last > $week["weekStart"]);
    }

    public function filterMeepsForLastWeekDaysOnly($meeps){

        $week = $this->date->getLastWeekStartAndEndDate();

        return array_filter(
            array_map(
                function($meep)
                {
                    $meep["created_at"] = DateTime::createFromFormat(DATE_ATOM, $meep["created_at"]);
                    $meep["message"] = $this->decorateMeep($meep["message"]);
                    return $meep;
                },
                $meeps
            ),
            function($meep) use ($week)
            {
                return $meep["created_at"] > $week["weekStart"] && $meep["created_at"] < $week["weekEnd"];
            }
        );
    }

    public function decorateMeep($text)
    {

        $mentions = "/\[(@[a-zA-Z\ \-]*)\]\(([a-zA-Z\/0-9]*)\)/";

        $text = preg_replace($mentions, "<a style='text-decoration: none;font-weight: bold;color: #57a4f9;' href='https://projects.kreisvier.ch/#/$2'>$1</a>", $text);

        return $text;
    }

    public function getMeepsFromLastWeek()
    {
        try
        {
            $offset = false;
            $meeps = [];
            do {
                $meeps = array_merge($meeps,$this->getMeeps(100,$offset));
                $offset = end($meeps)["id"];

            } while ($this->isLastMeepWithinLastWeek($meeps));

            $meeps = $this->filterMeepsForLastWeekDaysOnly($meeps);

            return array(
                "success" => true,
                "result" => $meeps
            );

        } catch (\Exception $e) {

            return array(
                "success" => false,
                "result" => $e->getMessage()
            );

        }



    }


}
