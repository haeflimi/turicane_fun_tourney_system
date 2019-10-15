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
            }).fail(function ( response ) {
            Tfts.error(response.responseJSON.error.message);
        });
    },
    leaveUserPool: function (user_id, game_id, ccm_token) {
        $.post(
            "/tfts/api/action", {
                game_id: game_id,
                user_id: user_id,
                ccm_token: ccm_token,
                action: 'leaveUserPool'
            }, function () {
                Tfts.success('You are now unsubscribed from this TFTS Pool');
                window.location.reload();
            }).fail(function ( response ) {
            Tfts.error(response.responseJSON.error.message);
        });
    },
    challengeUser: function (challanger_id, challenged_id, game_id, ccm_token, challenged_name, is_team) {
        $.post(
            "/tfts/api/action", {
                challenger_id: challanger_id,
                challenged_id: challenged_id,
                is_team: is_team,
                game_id: game_id,
                ccm_token: ccm_token,
                action: 'challengeUser'
            }, function () {
                Tfts.success('You a challenge has been sent to '+challenged_name);
                //window.location.reload();
            }).fail(function ( response ) {
                Tfts.error(response.responseJSON.error.message);
        });
    },
    acceptUserChallenge: function ( match_id, challenged_id ) {
        $.post(
            "/tfts/api/action", {
                match_id: match_id,
                challenged_id: challenged_id,
                ccm_token: $('#acceptUserChallengeToken').val(),
                action: 'acceptUserChallenge'
            }, function ( data ) {
                PNotify.removeAll()
                Tfts.success('Challenge Accepted');
                window.location.reload();
            }).fail(function ( response ) {
            Tfts.error(response.responseJSON.error.message);
        });
    },
    declineUserChallenge: function ( match_id, challenged_id ) {
        $.post(
            "/tfts/api/action", {
                match_id: match_id,
                challenged_id: challenged_id,
                ccm_token: $('#declineUserChallengeToken').val(),
                action: 'declineUserChallenge'
            }, function () {
                PNotify.removeAll()
                Tfts.success('Challenge Declined');
                window.location.reload();
            }).fail(function ( response ) {
            Tfts.error(response.responseJSON.error.message);
        });
    },
    reportResultUserMatch: function ( match_id, user_id, is_team ) {
        var data = new FormData(document.querySelector('#resultForm'));
        if(data.get('user1_score').length == 0 || data.get('user1_score').length == 0){
            Tfts.error('Score can not be empty');
            return;
        }
        $.post(
            "/tfts/api/action", {
                match_id: match_id,
                user_id: user_id,
                ccm_token: data.get('ccm_token'),
                user1_score: data.get('user1_score'),
                user2_score: data.get('user2_score'),
                is_team: is_team,
                action: 'reportResultUserMatch'
            }, function () {
                PNotify.removeAll()
                Tfts.success('Result reported');
                window.location.reload();
            }).fail(function ( response ) {
            Tfts.error(response.responseJSON.error.message);
        });
    },
    confirmResultUserMatch: function( match_id, user_id) {
        $.post(
            "/tfts/api/action", {
                match_id: match_id,
                user_id: user_id,
                ccm_token: $('#confirmResultUserMatchToken').val(),
                action: 'confirmResultUserMatch'
            }, function () {
                PNotify.removeAll()
                Tfts.success('Result confirmed');
                window.location.reload();
            }).fail(function ( response ) {
            Tfts.error(response.responseJSON.error.message);
        });
    },
    declineResultUserMatch: function( match_id, user_id) {
        $.post(
            "/tfts/api/action", {
                match_id: match_id,
                user_id: user_id,
                ccm_token: $('#declineResultUserMatchToken').val(),
                action: 'declineResultUserMatch'
            }, function () {
                PNotify.removeAll()
                Tfts.success('Result reported');
                window.location.reload();
            }).fail(function ( response ) {
            Tfts.error(response.responseJSON.error.message);
        });
    },
    /*
    Helper Methods
     */
    consumeAlerts: function() {
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
    error: function ( text = false ) {
        if(text.length == 0){
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
    success: function ( text ) {
        new PNotify({
            type: 'success',
            icon: 'fa fa-thumbs-up',
            title: 'Success',
            text: text,
            hide: true,
        });
    },
    challenge: function( text ) {
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
    confirm: function( text ) {
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