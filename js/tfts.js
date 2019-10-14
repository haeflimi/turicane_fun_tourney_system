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
                new PNotify({
                    type: 'success',
                    icon: 'fa fa-thumbs-up',
                    title: 'Success',
                    text: 'You are now subscribed to this TFTS Pool',
                    hide: true,
                });
                window.location.reload();
            }).fail(function () {
            this.error();
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
                this.success('You are now unsubscribed from this TFTS Pool');
                window.location.reload();
            }).fail(function () {
                this.error('test');
        });
    },
    challengeUser: function (challanger_id, challenged_id, game_id, ccm_token, challenged_name) {
        $.post(
            "/tfts/api/action", {
                challanger_id: challanger_id,
                challenged_id: challenged_id,
                game_id: game_id,
                ccm_token: ccm_token,
                action: 'challengeUser'
            }, function () {
                this.success('You a challenge has been sent to '+challenged_name);
                window.location.reload();
            }).fail(function () {
                this.error();
        });
    },
    acceptUserChallenge: function ( match_id, challenged_id, ccm_token ) {
        $.post(
            "/tfts/api/action", {
                matchi_id: match_id,
                challenged_id: challenged_id,
                ccm_token: ccm_token,
                action: 'acceptUserChallenge'
            }, function () {
                PNotify.removeAll()
                this.success('Challenge Accepted');
                window.location.reload();
            }).fail(function () {
            this.error();
        });
    },
    declineUserChallenge: function ( match_id, challenged_id, ccm_token ) {
        $.post(
            "/tfts/api/action", {
                matchi_id: match_id,
                challenged_id: challenged_id,
                ccm_token: ccm_token,
                action: 'declineUserChallenge'
            }, function () {
                PNotify.removeAll()
                this.success('Challenge Declined');
                window.location.reload();
            }).fail(function () {
            this.error();
        });
    },
    reportMatchResults: function () {
        $.post(
            "/tfts/api/action", {
                matchi_id: match_id,
                challenged_id: challenged_id,
                ccm_token: ccm_token,
                action: 'reportMatchResults'
            }, function () {
                PNotify.removeAll()
                this.success('Results reported.');
                window.location.reload();
            }).fail(function () {
            this.error();
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
        if(texth.length == 0){
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
    }
}