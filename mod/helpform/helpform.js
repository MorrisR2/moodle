
M.mod_helpform = {};

M.mod_helpform.init = function(Y) {

    Y.Get.js('/teex/browsertest/acrobat_pdf_detect.js', function (err) {
        if (err) {
            Y.log('/teex/browsertest/acrobat_pdf_detect.js failed to load!');
        } else {
            M.mod_helpform.setPDFReader();
        }
    });

    browser = M.mod_helpform.detectBrowser(Y);
    document.getElementById('id_browser').value = browser.name;
    document.getElementById('id_useragent').value = browser.ua;

    Y.on('focus', M.mod_helpform.clearDefault, '#id_comments');
    Y.on('focus', M.mod_helpform.clearDefault, '#id_whatpage');
    Y.on('focus', M.mod_helpform.clearDefault, '#id_errormsg');
    /*
    if (document.getElementById('helpformform')) {
        var helpformform = document.getElementById('helpformform');
        Y.YUI2.util.Event.addListener('helpformform', "submit", function(e) {
            var error = false;
            if (document.getElementById('helpformform')) {
            }
            if (error) {
                // alert(M.str.helpform.questionsnotanswered);
                Y.YUI2.util.Event.preventDefault(e);
                return false;
            } else {
                return true;
            }
        });
    }
    */
};

M.mod_helpform.setPDFReader = function (Y) {
    pdfinfo = getAcrobatInfo();
    document.getElementById('id_pdfreader').value = pdfinfo.acrobatVersion;
    
};


M.mod_helpform.detectBrowser = function (Y) {
    var details;
    Y.each(Y.UA, function(v, k) {
        if (!Y.Lang.isFunction(v)) {
            var info = k + ': ' + v;
            details = details + ', ' + info;
            if (v) {
                if (Y.Lang.isNumber(v)) {
                    browsername = info;
                }
            }
        }
    });
    var browser = {};
    browser.name = Y.UA.os + ' ' + browsername;
    browser.ua = Y.UA.userAgent;
    browser.details = details;
    return browser;
};

M.mod_helpform.clearDefault = function (e) {
    if (this.get('value') == this.get('defaultValue')) {
        this.set('value', '');
    }
}

