
sar = scormautoreturn = {};

sar.init = function(Y,courseid) {
    sar.courseid = courseid;
    // window.addEventListener('unload', sar.returnToCourse);
    Y.on('unload', sar.returnToCourse, Y.config.win); // better
};


sar.returnToCourse = function returnToCourse() {
    window.opener.location.replace(window.location.href.replace(/\/mod\/scorm.*/, '/course/view.php?id=' + sar.courseid));
}

