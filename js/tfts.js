/**
 * Wrap all core frontend TFTS Functionality into this class for ease of use
 */
$(function () {
  Tfts.consumeAlerts();
});

var Tfts = {
  joinUserPool: function (user_id, game_id, ccm_token) {
    $.post(
            "/tfts/api/action", {
              game_id: game_id,
              user_id: user_id,
              ccm_token: ccm_token,
              action: 'joinUserPool'
            }, function () {
      Tfts.success('You are now subscribed to this TFTS Pool');
      window.location.reload();
    }).fail(function (response) {
      Tfts.error(response.responseJSON.error.message);
    });
  },
  leaveUserPool: function (user_id, game_id, ccm_token, is_team) {
    $.post(
            "/tfts/api/action", {
              game_id: game_id,
              user_id: user_id,
              ccm_token: ccm_token,
              is_team: is_team,
              action: 'leaveUserPool'
            }, function () {
      Tfts.success('You are now unsubscribed from this TFTS Pool');
      window.location.reload();
    }).fail(function (response) {
      Tfts.error(response.responseJSON.error.message);
    });
  },
  challengeUser: function (challenger_id, challenged_id, game_id, ccm_token, challenged_name) {
    $.post(
            "/tfts/api/action", {
              challenger_id: challenger_id,
              challenged_id: challenged_id,
              game_id: game_id,
              ccm_token: ccm_token,
              action: 'challengeUser'
            }, function () {
      Tfts.success('Your challenge has been sent to ' + challenged_name);
      window.location.reload();
    }).fail(function (response) {
      Tfts.error(response.responseJSON.error.message);
    });
  },
  withdrawUserChallenge: function (match_id, challenger_id, ccm_token) {
    $.post(
            "/tfts/api/action", {
              match_id: match_id,
              challenger_id: challenger_id,
              ccm_token: ccm_token,
              action: 'withdrawUserChallenge'
            }, function () {
      Tfts.success('Your challenge has been withdrawn');
      window.location.reload();
    }).fail(function (response) {
      Tfts.error(response.responseJSON.error.message);
    });
  },
  acceptUserChallenge: function (match_id, challenged_id) {
    $.post(
            "/tfts/api/action", {
              match_id: match_id,
              challenged_id: challenged_id,
              ccm_token: $('#acceptUserChallengeToken').val(),
              action: 'acceptUserChallenge'
            }, function (data) {
      PNotify.removeAll();
      Tfts.success('Challenge Accepted');
      window.location.reload();
    }).fail(function (response) {
      Tfts.error(response.responseJSON.error.message);
    });
  },
  declineUserChallenge: function (match_id, challenged_id) {
    $.post(
            "/tfts/api/action", {
              match_id: match_id,
              challenged_id: challenged_id,
              ccm_token: $('#declineUserChallengeToken').val(),
              action: 'declineUserChallenge'
            }, function () {
      PNotify.removeAll();
      Tfts.success('Challenge Declined');
      window.location.reload();
    }).fail(function (response) {
      Tfts.error(response.responseJSON.error.message);
    });
  },
  reportResultUserMatch: function (match_id, user_id) {
    var data = new FormData(document.querySelector('#resultForm'));
    if (data.get('score1').length == 0 || data.get('score2').length == 0) {
      Tfts.error('Score can not be empty');
      return;
    }
    $.post(
            "/tfts/api/action", {
              match_id: match_id,
              user_id: user_id,
              ccm_token: data.get('ccm_token'),
              score1: data.get('score1'),
              score2: data.get('score2'),
              action: 'reportResultUserMatch'
            }, function () {
      PNotify.removeAll();
      Tfts.success('Result reported');
      window.location.reload();
    }).fail(function (response) {
      Tfts.error(response.responseJSON.error.message);
    });
  },
  cancelUserMatch: function (match_id, user_id) {
    $.post(
            "/tfts/api/action", {
              match_id: match_id,
              user_id: user_id,
              ccm_token: $('#cancelUserMatchToken').val(),
              action: 'cancelUserMatch'
            }, function () {
      PNotify.removeAll();
      Tfts.success('Match cancelled');
      window.location.reload();
    }).fail(function (response) {
      Tfts.error(response.responseJSON.error.message);
    });
  },
  joinGroupPool: function (group_id, game_id, ccm_token) {
    $.post(
            "/tfts/api/action", {
              game_id: game_id,
              group_id: group_id,
              ccm_token: ccm_token,
              action: 'joinGroupPool'
            }, function () {
      Tfts.success('Your team is now subscribed to this TFTS Pool');
      window.location.reload();
    }).fail(function (response) {
      Tfts.error(response.responseJSON.error.message);
    });
  },
  leaveGroupPool: function (group_id, game_id, ccm_token) {
    $.post(
            "/tfts/api/action", {
              game_id: game_id,
              group_id: group_id,
              ccm_token: ccm_token,
              action: 'leaveGroupPool'
            }, function () {
      Tfts.success('Your team is now unsubscribed from this TFTS Pool');
      window.location.reload();
    }).fail(function (response) {
      Tfts.error(response.responseJSON.error.message);
    });
  },
  challengeGroup: function (challenger_id, challenged_id, game_id, ccm_token, challenged_name) {
    $.post(
            "/tfts/api/action", {
              challenger_id: challenger_id,
              challenged_id: challenged_id,
              game_id: game_id,
              ccm_token: ccm_token,
              action: 'challengeGroup'
            }, function () {
      Tfts.success('Your challenge has been sent to ' + challenged_name);
      window.location.reload();
    }).fail(function (response) {
      Tfts.error(response.responseJSON.error.message);
    });
  },
  withdrawGroupChallenge: function (match_id, challenger_id, ccm_token) {
    $.post(
            "/tfts/api/action", {
              match_id: match_id,
              challenger_id: challenger_id,
              ccm_token: ccm_token,
              action: 'withdrawGroupChallenge'
            }, function () {
      Tfts.success('Your challenge has been withdrawn');
      window.location.reload();
    }).fail(function (response) {
      Tfts.error(response.responseJSON.error.message);
    });
  },
  acceptGroupChallenge: function (match_id, challenged_id) {
    $.post(
            "/tfts/api/action", {
              match_id: match_id,
              challenged_id: challenged_id,
              ccm_token: $('#acceptGroupChallengeToken').val(),
              action: 'acceptGroupChallenge'
            }, function (data) {
      PNotify.removeAll();
      Tfts.success('Challenge Accepted');
      window.location.reload();
    }).fail(function (response) {
      Tfts.error(response.responseJSON.error.message);
    });
  },
  declineGroupChallenge: function (match_id, challenged_id) {
    $.post(
            "/tfts/api/action", {
              match_id: match_id,
              challenged_id: challenged_id,
              ccm_token: $('#declineGroupChallengeToken').val(),
              action: 'declineGroupChallenge'
            }, function () {
      PNotify.removeAll();
      Tfts.success('Challenge Declined');
      window.location.reload();
    }).fail(function (response) {
      Tfts.error(response.responseJSON.error.message);
    });
  },
  reportResultGroupMatch: function (match_id, group_id) {
    var data = new FormData(document.querySelector('#resultForm'));
    if (data.get('score1').length == 0 || data.get('score2').length == 0) {
      Tfts.error('Score can not be empty');
      return;
    }
    $.post(
            "/tfts/api/action", {
              match_id: match_id,
              group_id: group_id,
              user_ids: data.getAll('user_ids'),
              ccm_token: data.get('ccm_token'),
              score1: data.get('score1'),
              score2: data.get('score2'),
              action: 'reportResultGroupMatch'
            }, function () {
      PNotify.removeAll();
      Tfts.success('Result reported');
      window.location.reload();
    }).fail(function (response) {
      Tfts.error(response.responseJSON.error.message);
    });
  },
  cancelGroupMatch: function (match_id, group_id) {
    $.post(
            "/tfts/api/action", {
              match_id: match_id,
              group_id: group_id,
              ccm_token: $('#cancelGroupMatchToken').val(),
              action: 'cancelGroupMatch'
            }, function () {
      PNotify.removeAll();
      Tfts.success('Match cancelled');
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