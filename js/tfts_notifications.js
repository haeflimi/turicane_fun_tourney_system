$(function(){
    /**
     * PNotify example code
     */
    var notice = PNotify.notice({
        title: 'Confirmation Needed',
        text: 'Are you sure?',
        icon: 'fas fa-question-circle',
        hide: false,
        stack: {
            'dir1': 'down',
            'modal': true,
            'firstpos1': 25
        },
        modules: {
            Confirm: {
                confirm: true
            },
            Buttons: {
                closer: false,
                sticker: false
            },
            History: {
                history: false
            },
        }
    });
    notice.on('pnotify.confirm', function() {
        alert('Ok, cool.');
    });
    notice.on('pnotify.cancel', function() {
        alert('Oh ok. Chicken, I see.');
    });
});

