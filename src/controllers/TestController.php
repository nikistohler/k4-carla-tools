<?php


namespace k4\k4carlatools\controllers;


use Craft;
use craft\helpers\DateTimeHelper;
use craft\helpers\Db;
use craft\web\Controller;
use craft\web\Response;
use DateTime;
use k4\k4carlatools\K4CarlaTools;
use Kint\Kint;
use yii\web\NotFoundHttpException;

class TestController extends Controller
{
    // Protected Properties
    // =========================================================================

    /**
     * @var K4CarlaTools $module
     */
    public $module;

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = [];

    public $enableCsrfValidation = true;

    public function actionIndex(){

        return $this->renderTemplate('k4-carla-tools/email',array(
            'meeps' => $this->module->carla->getMeepsFromWantedWeek(),
            'week' =>  $this->module->date->getWantedWeekStartAndEndDate()

        ));

    }





}
