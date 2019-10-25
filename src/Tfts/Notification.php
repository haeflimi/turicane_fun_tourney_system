<?php

namespace Tfts;

use Core;
use Tfts\Match;

/**
 * To get Notifications to look and work like we want them to we need to extend the core UI Class
 * Class UserInterface
 */
class Notification {

  public function showChallenge(Match $match) {
    $ccm_token_accept = Core::make('token')->generate('acceptChallenge');
    $ccm_token_decline = Core::make('token')->generate('declineChallenge');

    $match_id = $match->getId();
    $id = $match->getChallengedId();
    $challenger_name = $match->getChallengerName();
    $game_name = $match->getGame()->getName();
    $is_team = $match->getGame()->isGroup() ? 1 : 0;

    $text = t('<strong>%s</strong> has challenged you to a game of <strong>%s</strong>!', $challenger_name, $game_name);
    $text .= "<input id='acceptChallengeToken' type='hidden' value='" . $ccm_token_accept . "'>";
    $text .= "<input id='declineChallengeToken' type='hidden' value='" . $ccm_token_decline . "'>";
    $text .= '<div class="ccm-notification-inner-buttons form-inline">';
    $text .= '<button class="btn btn-success" onClick="Tfts.acceptChallenge(' . $match_id . ',' . $id . ',' . $is_team . ')">' . t('Accept') . '</button>&nbsp;';
    $text .= '<button class="btn btn-danger" onClick="Tfts.declineChallenge(' . $match_id . ',' . $id . ',' . $is_team . ')">' . t('Decline') . '</button>';
    $text .= '</div>';
    $content = '<script type="text/javascript">$(function() {
            Tfts.challenge(' . json_encode($text) . ');
        });</script>';

    return $content;
  }

  public function confirmResult(Match $match, int $id) {
    $ccm_token_confirm = Core::make('token')->generate('confirmResultMatch');
    $ccm_token_decline = Core::make('token')->generate('declineResultMatch');

    $match_id = $match->getId();
    $game_name = $match->getGame()->getName();
    $is_team = $match->getGame()->isGroup() ? 1 : 0;
    $opponent_name = $match->getChallengerId() == $id ? $match->getChallengedName() : $match->getChallengerName();

    $score = ' <strong>' . $match->getScore1() . ':' . $match->getScore2() . '</strong>.';
    $winner_id = $match->getWinnerId();
    if ($winner_id == 0) {
      $result = t('It was a draw - ') . $score;
    } else if ($winner_id == $id) {
      $result = t('You won ') . $score;
    } else {
      $result = t('You lost ') . $score;
    }

    $text = t('<strong>%s</strong> has entered the following result for your <strong>%s</strong> match: <br/>%s', $opponent_name, $game_name, $result);
    $text .= "<input id='confirmResultUserMatchToken' type='hidden' value='" . $ccm_token_confirm . "'>";
    $text .= "<input id='declineResultUserMatchToken' type='hidden' value='" . $ccm_token_decline . "'>";
    $text .= '<div class="ccm-notification-inner-buttons form-inline">';
    $text .= '<button class="btn btn-success" onClick="Tfts.reportResultMatch(' . $match_id . ',' . $id . ',' . $is_team . ',' . $match->getScore1() . ',' . $match->getScore2() . ')">' . t('Confirm') . '</button>&nbsp;';
    $text .= '<button class="btn btn-danger" onClick="PNotify.removeAll()">' . t('Change result') . '</button>';
    $text .= '</div>';

    $content = '<script type="text/javascript">$(function() {
            Tfts.confirm(' . json_encode($text) . ');
        });</script>';

    return $content;
  }

}
