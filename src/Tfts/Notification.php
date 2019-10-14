<?php

namespace Tfts;

use Concrete\Core\View\View;
use Tfts\Match;

/**
 * To get Notifications to look and work like we want them to we need to extend the core UI Class
 * Class UserInterface
 */
class Notification
{
    /**
     * @param array $arguments
     *
     * @return string
     */
    public function challenge( Match $match )
    {
        $match_id = $match->getId();
        $ccm_token = Core::make('token')->generate('joinUserPool');
        $challenger_id = $match->getUser1()->getUserID();
        $challenger_name = $match->getUser1()->getUserName();
        $game_name = $match->getGame()->getName();

        $text = t('%s has challenged you to a Game of %s',$challenger_name,$game_name);
        $text .= '<div class=\"ccm-notification-inner-buttons form-inline\">';
        $text .= '<button class=\"btn btn-success\" onClick=\"Tfts.acceptUserChallenge('.$match_id.','.$challenger_id.',\"'.$ccm_token.'\");\">'.t('Accept').'</button>&nbsp;';
        $text .= '<button class=\"btn btn-danger\" onClick=\"Tfts.declineUsereChallenge('.$match_id.','.$challenger_id.',\"'.$ccm_token.'\")\">'.t('Decline').'</button>';
        $text .= '</div>';

        $content = '<script type="text/javascript">$(function() {
            Tfts.challenge("'.$text.'");
        });</script>';

        return $content;
    }

    /**
     * @param array $arguments
     *
     * @return string
     */
    public function confirm( Match $match )
    {
        $view = new View();
        $view->requireAsset('core/app');
        $view->requireAsset('javascript','pnotify-confirm');

        $game = 'Heartstone';
        $text = t('%s has entered the following Result for your %s match: %s','Some Dude',$game, '1:0, you lost.');
        $text .= '<div class=\"ccm-notification-inner-buttons form-inline\">';
        $text .= '<button class=\"btn btn-success\" onClick=\"Tfts.confirmMatch()\">'.t('Confirm').'</button>&nbsp;';
        $text .= '<button class=\"btn btn-danger\" onClick=\"Tfts.disputeMatch()\">'.t('Dispute').'</button>';
        $text .= '</div>';

        $content = '<script type="text/javascript">$(function() {
            Tfts.challenge("'.$text.'");
        });</script>';

        return $content;
    }
}