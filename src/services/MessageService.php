<?php


namespace k4\k4carlatools\services;


use craft\base\Component;
use craft\db\Connection;
use craft\mail\Message;
use craft\web\View;
use yii\mail\MailerInterface;

/**
 * Class MessageService
 * @package k4\k4carlatools\services
 *
 * @property Connection $db
 * @property MailerInterface $mailer
 * @property View $view
 * @property String $basePath
 */
class MessageService extends Component
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
     * @var String $basePath
     */
    public $basePath;

    public function sendMeeps($email,$meeps,$week)
    {
        $oldTemplatesPath = $this->view->getTemplatesPath();
        $this->view->setTemplatesPath($this->basePath);
        $body = $this->view->renderTemplate('/templates/email.twig', array('meeps' => $meeps,'week' =>  $week));
        $this->view->setTemplatesPath($oldTemplatesPath);

        $message = new Message();
        $message->setFrom(array("noreply@kreisvier.ch" => 'K4 Intern'));
        $message->setTo($email);
        $message->setSubject("k4 Allgemein KW ".$week["weekNumber"]);
        $message->setHtmlBody($body);

        return $this->mailer->send($message);
    }


}
