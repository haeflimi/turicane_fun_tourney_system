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
        $me = new User();
        $match_id = $match->getId();
        $ccm_token_accept = Core::make('token')->generate('acceptUserChallenge');
        $ccm_token_decline = Core::make('token')->generate('declineUserChallenge');
        if($match->getGroup1Id()){
            $is_team = true;
            $challengerId = $match->getGroup1Id();
            $challengedId = $match->getGroup2Id();
            $challenger_name = $match->getGroup1()->getGroupName();
        } else {
            $is_team = false;
            $challengerId = $match->getUser1()->getUserID();
            $challengedId = $me->getUserID();
            $challenger_name = $match->getUser1()->getUserName();
        }
        $game_name = $match->getGame()->getName();
        $text = t('%s has challenged you to a Game of %s',$challenger_name,$game_name);
        $text .= "<input id='acceptUserChallengeToken' type='hidden' value='".$ccm_token_accept."'>";
        $text .= "<input id='declineUserChallengeToken' type='hidden' value='".$ccm_token_decline."'>";
        $text .= '<div class="ccm-notification-inner-buttons form-inline">';
        $text .= '<button class="btn btn-success" onClick="Tfts.acceptUserChallenge('.$match_id.','.$challengedId.','.$is_team.')">'.t('Accept').'</button>&nbsp;';
        $text .= '<button class="btn btn-danger" onClick="Tfts.declineUserChallenge('.$match_id.','.$challengedId.','.$is_team.')">'.t('Decline').'</button>';
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
        if(get_class($winner = $match->getWinner()) == 'Group'){
            $winnerId = $winner->getGroupID();
            $isTeam = true;
            if($u->inGroup($match->getGroup1())){
                $opponentId = $match->getGroup1Id();
            } elseif($u->inGroup($match->getGroup2())) {
                $opponentId = $match->getGroup2Id();
            }
        } else {
            $winnerId = $winner->getUserID();
            $isTeam = false;
            $opponentId = $u->getUserID();
        }

        if($winnerId == $opponentId){
            $winOrLoss = t('You won!');
        } else {
            $winOrLoss = t('You lost!');
        }

        $match_id = $match->getId();
        $ccm_token_confirm = Core::make('token')->generate('confirmResultUserMatch');
        $ccm_token_decline = Core::make('token')->generate('declineResultUserMatch');


        $score = '<strong>'.$match->getScore1().' : '.$match->getScore2();
        $text = t('Your Opponent has entered the following Result for your %s match: %s <br/>%s',$game_name, $score, $winOrLoss);
        $text .= "<input id='confirmResultUserMatchToken' type='hidden' value='".$ccm_token_confirm."'>";
        $text .= "<input id='declineResultUserMatchToken' type='hidden' value='".$ccm_token_decline."'>";
        $text .= '<div class="ccm-notification-inner-buttons form-inline">';
        $text .= '<button class="btn btn-success" onClick="Tfts.confirmResultUserMatch('.$match_id.','.$opponentId.','.$isTeam.')">'.t('Confirm').'</button>&nbsp;';
        $text .= '<button class="btn btn-danger" onClick="Tfts.declineResultUserMatch('.$match_id.','.$opponentId.','.$isTeam.')">'.t('Decline').'</button>';
        $text .= '</div>';

        $text = json_encode($text);
        $content = '<script type="text/javascript">$(function() {
            Tfts.challenge('.$text.');
        });</script>';

        return $content;
    }
}