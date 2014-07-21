<?php

require_once('../../config.php');
global $DB;

$parent_category = 759;
$dry_run  = 1;
$findreplace = array (

                         array('find'=>'/\s*style\s*=\s*"[^"]*"/iS', 'replace'=>''),
                         array('find'=>'/<span>(.*)<\/span>/iS', 'replace'=>'$1'),
                         array('find'=>'/^\s*<p>(.*)<\/p>$/iS', 'replace'=>'$1')
                     );


unhtml_questions($findreplace, $parent_category, $dry_run);
unhtml_answers($findreplace, $parent_category, $dry_run);

function  unhtml_questions($findreplace, $parent_category, $dry_run) {
    global $DB;
    $updatetable = 'question';
    $updatecolumn = 'questiontext';
    $records = $DB->get_records_sql("SELECT * FROM mdl_$updatetable WHERE category IN (SELECT id FROM mdl_question_categories WHERE parent=?) AND $updatecolumn LIKE '%<%'", array($parent_category));

    echo "<table>\n<tr><th>Old</th><th>New</th></tr>\n";
    regexreplace($records, $updatetable, $updatecolumn, $findreplace, $dry_run);
    echo "</table><br />\n";
}

function  unhtml_answers($findreplace, $parent_category, $dry_run) {
    global $DB;
    $updatetable = 'question_answers';
    $updatecolumn = 'answer';
    $records = $DB->get_records_sql("SELECT * FROM mdl_$updatetable WHERE question IN (select id FROM {question} WHERE category IN (SELECT id FROM mdl_question_categories WHERE parent=?) ) AND $updatecolumn LIKE '%<%'", array($parent_category));

    echo "<table>\n<tr><th>Old</th><th>New</th></tr>\n";
    regexreplace($records, $updatetable, $updatecolumn, $findreplace, $dry_run);
    echo "</table><br />\n";
}


function regexreplace($records, $updatetable, $updatecolumn, $findreplace, $dry_run) {
    global $DB;

    foreach ($records as $record) {
        if (empty($record->id)) {
            echo "$updatetable table has no 'id' column, aborting!\n";
            exit;
        }
        echo "<tr><td>".$record->$updatecolumn . "</td>\n";
        $record->$updatecolumn = replacements($findreplace, $record->$updatecolumn);
        if (!$dry_run) {
            $DB->update_record($updatetable, $record, true);
        }
        echo "<td>" . $record->$updatecolumn . "</td></tr>\n";
        // var_dump($record);;
        // exit;
    }
}

function replacements($findreplace, $value) {
    foreach ($findreplace as $fr) {
        while(preg_match($fr['find'], $value)) {
            $value = preg_replace ($fr['find'], $fr['replace'], $value);
        }
    }
    return $value;
}

