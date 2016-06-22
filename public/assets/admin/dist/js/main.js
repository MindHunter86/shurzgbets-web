$(document).ready(function() {
    var notifier = io.connect(SITE_URL, {secure: true});
    notifier.on('admin_cache_update', function (msg) {
        msg = JSON.parse(msg);
        $.notify(msg.text, {className: msg.type});
    });
});