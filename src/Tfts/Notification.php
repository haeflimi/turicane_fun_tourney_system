<?php

namespace Tfts;

use Concrete\Core\View\View;
use Concrete\Core\User\User;
use Core;
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
        $ccm_token_accept = Core::make('token')->generate('acceptUserChallenge');
        $ccm_token_decline = Core::make('token')->generate('declineUserChallenge');
        $challenger_id = $match->getUser1()->getUserID();
        $challenger_name = $match->getUser1()->getUserName();
        $game_name = $match->getGame()->getName();
        $me = new User();
        $text = t('%s has challenged you to a Game of %s',$challenger_name,$game_name);
        $text .= "<input id='acceptUserChallengeToken' type='hidden' value='".$ccm_token_accept."'>";
        $text .= "<input id='declineUserChallengeToken' type='hidden' value='".$ccm_token_decline."'>";
        $text .= '<div class="ccm-notification-inner-buttons form-inline">';
        $text .= '<button class="btn btn-success" onClick="Tfts.acceptUserChallenge('.$match_id.','.$me->getUserID().')">'.t('Accept').'</button>&nbsp;';
        $text .= '<button class="btn btn-danger" onClick="Tfts.declineUserChallenge('.$match_id.','.$me->getUserID().')">'.t('Decline').'</button>';
        $text .= '</div>';
        $text = json_encode($text);
        $content = '<script type="text/javascript">$(function() {
            Tfts.challenge('.$text.');
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
        $u = new User();
        $game_name = $match->getGame()->getName();
        $user_id = $u->getUserID();
        $match_id = $match->getId();
        $ccm_token_confirm = Core::make('token')->generate('confirmResultUserMatch');
        $ccm_token_decline = Core::make('token')->generate('declineResultUserMatch');
        if($match->getWinner()->getUserID() == $u->getUserId()){
            $winOrLoss = t('You won!');
        } else {
            $winOrLoss = t('You lost!');
        }

        $score = '<strong>'.$match->getScore1().' : '.$match->getScore2();
        $text = t('Your Opponent has entered the following Result for your %s match: %s <br/>%s',$game_name, $score, $winOrLoss);
        $text .= "<input id='confirmResultUserMatchToken' type='hidden' value='".$ccm_token_confirm."'>";
        $text .= "<input id='declineResultUserMatchToken' type='hidden' value='".$ccm_token_decline."'>";
        $text .= '<div class="ccm-notification-inner-buttons form-inline">';
        $text .= '<button class="btn btn-success" onClick="Tfts.confirmResultUserMatch('.$match_id.','.$user_id.')">'.t('Confirm').'</button>&nbsp;';
        $text .= '<button class="btn btn-danger" onClick="Tfts.declineResultUserMatch('.$match_id.','.$user_id.')">'.t('Decline').'</button>';
        $text .= '</div>';

        $text = json_encode($text);
        $content = '<script type="text/javascript">$(function() {
            Tfts.challenge('.$text.');
        });</script>';

        return $content;
    }
}