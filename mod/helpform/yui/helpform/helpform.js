
YUI.add('moodle-mod_helpform-helpform', function(Y) {
    M.mod_helpform = M.mod_helpform || {};
    M.mod_helpform.init = function(Y) {
        Y.Get.js('/teex/browsertest/acrobat_pdf_detect.js', function (err) {
            if (err) {
                Y.log('/teex/browsertest/acrobat_pdf_detect.js failed to load!');
            } else {
                this.setPDFReader();
            }
        });


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
    };
);


M.mod_helpform.setPDFReader = checkPDF(Y) {
    pdfinfo = getAcrobatInfo();
    document.getElementById('pdfreader') = pdfinfo.acrobatVersion;
    
};


