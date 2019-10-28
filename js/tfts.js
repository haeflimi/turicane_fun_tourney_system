/**
 * Wrap all core frontend TFTS Functionality into this class for ease of use
 */
$(function () {
  Tfts.consumeAlerts();
});

var Tfts = {
  joinPool: function (game_id, id, is_team, ccm_token) {
    $.post(
            "/tfts/api/action", {
              game_id: game_id,
              id: id,
              is_team: is_team,
              ccm_token: ccm_token,
              action: 'joinPool'
            }, function () {
      Tfts.success('You have joined this TFTS Pool!');
      window.location.reload();
    }).fail(function (response) {
      Tfts.error(response.responseJSON.error.message);
    });
  },
  leavePool: function (game_id, id, is_team, ccm_token) {
    $.post(
            "/tfts/api/action", {
              game_id: game_id,
              id: id,
              is_team: is_team,
              ccm_token: ccm_token,
              action: 'leavePool'
            }, function () {
      Tfts.success('You have left this TFTS Pool!');
      window.location.reload();
    }).fail(function (response) {
      Tfts.error(response.responseJSON.error.message);
    });
  },
  createChallenge: function (game_id, challenger_id, challenged_id, challenged_name, is_team, ccm_token) {
    $.post(
            "/tfts/api/action", {
              game_id: game_id,
              challenger_id: challenger_id,
              challenged_id: challenged_id,
              is_team: is_team,
              ccm_token: ccm_token,
              action: 'createChallenge'
            }, function () {
      Tfts.success('Your challenge has been sent to ' + challenged_name + '!');
      window.location.reload();
    }).fail(function (response) {
      Tfts.error(response.responseJSON.error.message);
    });
  },
  withdrawChallenge: function (match_id, challenger_id, is_team, ccm_token) {
    $.post(
            "/tfts/api/action", {
              match_id: match_id,
              challenger_id: challenger_id,
              is_team: is_team,
              ccm_token: ccm_token,
              action: 'withdrawChallenge'
            }, function () {
      Tfts.success('Your challenge has been withdrawn!');
      window.location.reload();
    }).fail(function (response) {
      Tfts.error(response.responseJSON.error.message);
    });
  },
  acceptChallenge: function (match_id, challenged_id, is_team) {
    $.post(
            "/tfts/api/action", {
              match_id: match_id,
              challenged_id: challenged_id,
              is_team: is_team,
              ccm_token: $('#acceptChallengeToken').val(),
              action: 'acceptChallenge'
            }, function (data) {
      PNotify.removeAll();
      Tfts.success('You accepted the challenge!');
      window.location.reload();
    }).fail(function (response) {
      Tfts.error(response.responseJSON.error.message);
    });
  },
  declineChallenge: function (match_id, challenged_id, is_team) {
    $.post(
            "/tfts/api/action", {
              match_id: match_id,
              challenged_id: challenged_id,
              is_team: is_team,
              ccm_token: $('#declineChallengeToken').val(),
              action: 'declineChallenge'
            }, function () {
      PNotify.removeAll();
      Tfts.success('You declined the challenge!');
      window.location.reload();
    }).fail(function (response) {
      Tfts.error(response.responseJSON.error.message);
    });
  },
  reportResultMatch: function (match_id, id, is_team, score1, score2) {
    var query = document.querySelector('#resultForm' + match_id);
    var ccm_token = null;
    var user_ids = null;
    if (query !== null) {
      var data = new FormData(query);
      score1 = data.get('score1');
      score2 = data.get('score2');
      ccm_token = data.get('ccm_token');
      user_ids = data.getAll('user_ids');
    }
    $.post(
            "/tfts/api/action", {
              match_id: match_id,
              id: id,
              is_team: is_team,
              ccm_token: ccm_token,
              score1: score1,
              score2: score2,
              user_ids: user_ids,
              action: 'reportResultMatch'
            }, function () {
      PNotify.removeAll();
      Tfts.success('You reported the result for the match!');
      window.location.reload();
    }).fail(function (response) {
      Tfts.error(response.responseJSON.error.message);
    });
  },
  cancelMatch: function (match_id, id, is_team) {
    $.post(
            "/tfts/api/action", {
              match_id: match_id,
              id: id,
              is_team: is_team,
              ccm_token: $('#cancelMatchToken').val(),
              action: 'cancelMatch'
            }, function () {
      PNotify.removeAll();
      Tfts.success('You cancelled the match!');
      window.location.reload();
    }).fail(function (response) {
      Tfts.error(response.responseJSON.error.message);
    });
  },
  /*
   Helper Methods
   */
  consumeAlerts: function () {
    if (window._alert) {
      return;
    }
    window._alert = window.alert;
    window.alert = function alert(message) {
      new PNotify({
        type: 'info',
        icon: 'fa fa-close',
        title: 'Alert',
        text: message,
        hide: true,
      });
    };
  },
  error: function (text = false) {
    if (text.length == 0) {
      text = 'Something went wrong.';
    }
    new PNotify({
      type: 'error',
      icon: 'fa fa-close',
      title: 'Error',
      text: text,
      hide: true,
    });
  },
  success: function (text) {
    new PNotify({
      type: 'success',
      icon: 'fa fa-thumbs-up',
      title: 'Success',
      text: text,
      hide: true,
    });
  },
  challenge: function (text) {
    var notice = new PNotify({
      type: 'info',
      icon: 'fa fa-gamepad',
      title: 'Challenge',
      text: text,
      hide: false,
      buttons: {
        close: false
      }
    });
  },
  confirm: function (text) {
    var notice = new PNotify({
      type: 'info',
      icon: 'fa fa-tick',
      title: 'Confirm',
      text: text,
      hide: false,
      buttons: {
        close: false
      }
    });
  }
}