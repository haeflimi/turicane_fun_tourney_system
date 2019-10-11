/**
 * Wrap all core frontend TFTS Functionality into this class for ease of use
 */
var Tfts = {
    joinUserPool: function (game_id, user_id, ccm_token) {
        $.post(
            "/tfts/api/joinUserPool", {
                game_id: game_id,
                user_id: user_id,
                ccm_token: ccm_token,
                action: 'joinUserPool'
            }, function () {
                alert("success");
            }).fail(function () {
            alert("error");
        });
    },
    challengeUser: function (opponent_id) {
        alert('challange ' + opponent_id);
    },
    reportMatchResults: function () {
        alert('report');
    },
    acceptChallenge: function () {
        alert('accept');
    }
}