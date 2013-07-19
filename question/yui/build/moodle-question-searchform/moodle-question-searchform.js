YUI.add('moodle-question-searchform', function (Y, NAME) {

YUI.add('moodle-question-searchform', function (Y, NAME) {

    var SELECTORS = {
            OPTIONS: '.searchoptions'
        },
        NS;

    M.question = M.question || {};
    NS = M.question.searchform = {};

    NS.init = function (args) {

        NS.showmore = args.showmore;
        NS.showless = args.showless;
        NS.advsearch = Y.one('#advancedsearch');

        if (!args.showadv) {
            NS.advsearch.hide();
        }
        NS.chkshowhideadvsearch = Y.one('#showhideadvsearch');
        NS.chkshowhideadvsearch.on('click', this.advancedsearch_click);
        Y.delegate('change', this.option_changed, Y.config.doc, SELECTORS.OPTIONS, this);
    };

    NS.option_changed = function (e) {
        e.target.getDOMNode().form.submit();
    };

    NS.advancedsearch_click = function (e) {
        NS.advsearch.toggleView();
        if (NS.advsearch.getAttribute('hidden') || NS.advsearch.getStyle('display') === 'none') {
            NS.chkshowhideadvsearch.setHTML(NS.showmore);
            Y.one('#showadv').set('value', 0);
        } else {
            NS.chkshowhideadvsearch.setHTML(NS.showless);
            Y.one('#showadv').set('value', 1);
        }
    };

}, '@VERSION@', {"requires": ["base", "node"]});


}, '@VERSION@', {"requires": ["base", "node"]});
