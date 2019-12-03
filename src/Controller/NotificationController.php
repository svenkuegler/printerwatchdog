<?php

namespace App\Controller;

use App\Service\ContainerParametersHelper;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;

class NotificationController extends AbstractController
{
    /**
     * @Route("/notification", name="notification")
     *
     * @param ContainerParametersHelper $helper
     * @param LoggerInterface $logger
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(ContainerParametersHelper $helper, LoggerInterface $logger, Request $request)
    {
        $vars = $request->request->all();
        $config = Yaml::parseFile( $helper->getApplicationRootDir()  . "/config/notification.yaml");

        if(count($vars) == 8) {
            // Web
            $config['web']['enabled'] = (intval($vars['webWarning'])==0 && intval($vars['webDanger']) == 0) ? false : true;
            $config['web']['tonerlevel']['warning'] = intval($vars['webWarning']);
            $config['web']['tonerlevel']['danger'] = intval($vars['webDanger']);

            // Email
            $config['email']['enabled'] = (intval($vars['emailWarning'])==0 && intval($vars['emailDanger']) == 0) ? false : true;
            $config['email']['tonerlevel']['warning'] = intval($vars['emailWarning']);
            $config['email']['tonerlevel']['danger'] = intval($vars['emailDanger']);

            // Slack
            $config['slack']['enabled'] = (intval($vars['slackWarning'])==0 && intval($vars['slackDanger']) == 0) ? false : true;
            $config['slack']['tonerlevel']['warning'] = intval($vars['slackWarning']);
            $config['slack']['tonerlevel']['danger'] = intval($vars['slackDanger']);

            // Monitoring
            $config['monitoring']['enabled'] = (intval($vars['monitoringWarning'])==0 && intval($vars['monitoringDanger']) == 0) ? false : true;
            $config['monitoring']['tonerlevel']['warning'] = intval($vars['monitoringWarning']);
            $config['monitoring']['tonerlevel']['danger'] = intval($vars['monitoringDanger']);

            $newConfig = Yaml::dump($config);
            if(file_put_contents($helper->getApplicationRootDir() . "/config/notification.yaml", $newConfig)) {
                $this->addFlash("success", "notification settings saved!");
            } else {
                $this->addFlash("danger", "could not write notification settings");
            }
        }

        return $this->render('notification/index.html.twig', [
            'notificationConfig' => $config,
        ]);
    }
}
