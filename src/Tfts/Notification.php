<?php

namespace Tfts;

use Concrete\Core\User\User;
use Core;
use Tfts\Match;

/**
 * To get Notifications to look and work like we want them to we need to extend the core UI Class
 * Class UserInterface
 */
class Notification {

  public function showUserChallenge(Match $match) {
    $me = new User();
    $match_id = $match->getId();
    $ccm_token_accept = Core::make('token')->generate('acceptUserChallenge');
    $ccm_token_decline = Core::make('token')->generate('declineUserChallenge');
    
    $user_id = $me->getUserID();
    $challenger_name = $match->getUser1()->getUserName();
    $game_name = $match->getGame()->getName();
    
    $text = t('%s has challenged you to a Game of %s', $challenger_name, $game_name);
    $text .= "<input id='acceptUserChallengeToken' type='hidden' value='" . $ccm_token_accept . "'>";
    $text .= "<input id='declineUserChallengeToken' type='hidden' value='" . $ccm_token_decline . "'>";
    $text .= '<div class="ccm-notification-inner-buttons form-inline">';
    $text .= '<button class="btn btn-success" onClick="Tfts.acceptChallenge(' . $match_id . ',' . $user_id . ',0)">' . t('Accept') . '</button>&nbsp;';
    $text .= '<button class="btn btn-danger" onClick="Tfts.declineChallenge(' . $match_id . ',' . $user_id . ',0)">' . t('Decline') . '</button>';
    $text .= '</div>';
    $content = '<script type="text/javascript">$(function() {
            Tfts.challenge(' . json_encode($text) . ');
        });</script>';

    return $content;
  }

  public function confirmUserResult(Match $match) {
    $user = new User();
    $user_id = $user->getUserID();

    $game_name = $match->getGame()->getName();
    
    $winner_id = $match->getWinnerId();
    if ($winner_id == 0) {
      $result = t('Draw!');
    } else if ($winner_id == $user_id) {
      $result = t('You won!');
    } else {
      $result = t('You lost!');
    }

    $match_id = $match->getId();
    $ccm_token_confirm = Core::make('token')->generate('confirmResultUserMatch');
    $ccm_token_decline = Core::make('token')->generate('declineResultUserMatch');

    $score = '<strong>' . $match->getScore1() . ' : ' . $match->getScore2();
    $text = t('Your Opponent has entered the following result for your %s match: %s <br/>%s', $game_name, $score, $result);
    $text .= "<input id='confirmResultUserMatchToken' type='hidden' value='" . $ccm_token_confirm . "'>";
    $text .= "<input id='declineResultUserMatchToken' type='hidden' value='" . $ccm_token_decline . "'>";
    $text .= '<div class="ccm-notification-inner-buttons form-inline">';
    $text .= '<button class="btn btn-success" onClick="Tfts.reportResultMatch(' . $match_id . ',' . $user_id . ',0,' . $match->getScore1() . ',' . $match->getScore2() . ')">' . t('Confirm') . '</button>&nbsp;';
    $text .= '<button class="btn btn-danger" onClick="PNotify.removeAll()">' . t('Change result') . '</button>';
    $text .= '</div>';

    $content = '<script type="text/javascript">$(function() {
            Tfts.confirm(' . json_encode($text) . ');
        });</script>';

    return $content;
  }

}
