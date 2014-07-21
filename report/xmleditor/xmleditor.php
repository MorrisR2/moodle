<?php
require_once(dirname(__FILE__) . '/../../config.php');
require_login();
?>

<script type="text/javascript">
	baseurl = '<?php global $baseurl; echo($baseurl) ?>';
</script>
<script type="text/javascript" src="exp_collapse.js"></script>
<script type="text/javascript" src="importnode.js"></script>

<style type="text/css">
dl
{
	margin: 1em;
    margin-left: 3em;
	padding: 0;
	display:-moz-inline-stack;
	display:inline-block;
	zoom:1;
	*display:inline;
}

dt
{
	background-color: #FFEFE0;
	color: #202020;
	padding: .5em;
	font-weight: bold;
	border: 1px solid #131210;
	min-width: 8em;
}

dd
{
	margin: 0 0 1em 0;
	background: #FFF9F0;
	text-align: center;
	padding: .5em .5em;
	border: 1px solid #131210;
}

dl.attributes { vertical-align: top; }
.attributes dt { background: #ffe0e0; color: #000000; }
.attributes dd { background: #fff0f0; color: #000000; }

.hide { display: none; }
.show { display: block;}

button.delete {
		background: url('Andy_Trash_Can_18.png');
        background-repeat: no-repeat;
        background-position:center;
        height: 20px; width: 20px;
		float: right;
        }

.expandable button.expandcollapse { 
		background: url('plus-8.png'); 
        background-repeat: no-repeat;
        background-position:center;
        height: 20px; width: 20px;
		border: 1px outset;
		}

.collapsible button.expandcollapse {
        background: url('minus-8.png');
        background-repeat: no-repeat;
        background-position:center;
        height: 20px; width: 20px;
		border: 1px outset;
}

dd dd { background: #ECF0F0; }
dd dd dd { background: #DBD8D8; }
dd dd dd dd { background: #ECF0F0; }
dd dd dd dd dd { background: #DBD8D8; }
dd dd dd dd dd dd { background: #ECF0F0; }
dd dd dd dd dd dd dd { background: #DBD8D8; }
dd dd dd dd dd dd dd dd { background: #ECF0F0; }
dd dd dd dd dd dd dd dd dd { background: #DBD8D8; }

/* #savexml { display: none; } */

</style>

	<form action="index.php" METHOD="POST">
		User ID: <input name="userid" id="userid" value="<?php echo $_REQUEST['userid'] ?>">
        <input type="hidden" name="choosecourse" value="<?php echo $course_id ?>">
		<input type="submit" value="Retrieve XML">
	</form>

	<p>Click an element to expand or close it.</p>
	<div id="xmltohtml"></div>

	<p>Edit either the tree &uarr; or the source &darr;</p>
	<form name="saveform" id="saveform" method="POST" action="<?php echo $baseurl ?>">
		<input type="hidden" name="req" value="updatecourseinfo_backup">
		<input type="hidden" name="fromflash" value="true">
		<input type="hidden" name="user_un" id="saveuserid" value="<?php echo $_REQUEST['userid'] ?>">
        <input type="hidden" id="choosecourse" name="course_id" value="<?php echo $course_id ?>">
		<textarea name="cxml" id="savexml" cols="100" rows="40"></textarea>
	</form>

 <button onclick="saveXml('<?php echo $_REQUEST['userid'] . "', '" . $course_id ?>')">Save Changes</button>
 <p><small>Saving changes will also download a backup file. In case of problems, please send the backup to developers.</small></p>

