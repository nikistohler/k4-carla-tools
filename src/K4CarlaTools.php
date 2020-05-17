<?php


namespace k4\k4carlatools;


use Craft;
use craft\base\Plugin;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterUserPermissionsEvent;
use craft\services\UserPermissions;
use craft\web\twig\variables\CraftVariable;
use craft\web\UrlManager;
use k4\k4carlatools\models\SettingsModel;
use k4\k4carlatools\services\CarlaService;
use k4\k4carlatools\services\DateService;
use k4\k4carlatools\services\MessageService;
use Kint\Kint;
use yii\base\Event;

/**
 * Class K4CarlaTools
 *
 * @package k4\k4carlatools
 *
 * @property SettingsModel $settings
 * @property MessageService $message
 * @property CarlaService $carla
 * @property DateService $date
 */
class K4CarlaTools extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var K4CarlaTools
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    public $hasCpSection = true;

    public $pluginHandle = 'k4-carla-tools';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->registerServices();
        $this->registerRoutes();
        $this->registerPermissions();
        $this->registerVariable();


    }


    // Settings
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new SettingsModel();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        return $this->module->view->renderTemplate(
            $this->pluginHandle.'/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }

    // CP Navigation
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getCpNavItem()
    {
        $cp_nav = include __DIR__ . '/nav_cp.php';

        if (count($cp_nav) > 0) {
            $currentUser = Craft::$app->getUser()->getIdentity();
            $allowAdminChanges = Craft::$app->getConfig()->getGeneral()->allowAdminChanges;

            $cpNavItem =  parent::getCpNavItem();
            $cpNavItem['subnav'] = [];


            foreach($cp_nav as $key => $item)
            {
                $cpNavItem['subnav'][$key] = $item;
            }
            return $cpNavItem;
        }
    }


    // Own Helper Functions
    // =========================================================================


    protected function registerServices()
    {
        $this->set('date',[
            'class' => DateService::class,
        ]);

        $this->set('message',[
            'class' => MessageService::class,
            'db' => $this->module->db,
            'view' => $this->module->view,
            'mailer' => $this->module->mailer,
            'basePath' => $this->basePath
        ]);

        $this->set('carla',[
            'class' => CarlaService::class,
            'db' => $this->module->db,
            'view' => $this->module->view,
            'mailer' => $this->module->mailer,
            'date' => $this->date,
            'settings' => $this->settings
        ]);

    }

    protected function registerRoutes()
    {
        // Register our site routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $routes       = include __DIR__ . '/routes_site.php';
                $event->rules = array_merge($event->rules, $routes);
            }
        );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $routes       = include __DIR__ . '/routes_cp.php';
                $event->rules = array_merge($event->rules, $routes);

                $routes  = include __DIR__ . '/routes_test.php';
                $event->rules = array_merge($event->rules, $routes);
            }
        );
    }

    protected function registerPermissions(){

        // Register user permissions
        Event::on(
            UserPermissions::class,
            UserPermissions::EVENT_REGISTER_PERMISSIONS,
            function(RegisterUserPermissionsEvent $event) {
                $event->permissions['k4-carla-tools'] = include __DIR__ . '/permissions.php';
            }
        );

    }

    protected function registerVariable(){


//        Event::on(
//            CraftVariable::class,
//            CraftVariable::EVENT_INIT,
//            function (Event $event) {
//                /** @var CraftVariable $variable */
//                $variable = $event->sender;
//                $variable->set('k4carlatools', K4CarlaToolsVariable::class);
//            }
//        );

    }


}
