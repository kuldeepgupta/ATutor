<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
	$page = 'tests';
	define('AT_INCLUDE_PATH', '../../include/');
	require(AT_INCLUDE_PATH.'vitals.inc.php');

	authenticate(AT_PRIV_TEST_CREATE);

	$_section[0][0] = _AT('tools');
	$_section[0][1] = 'tools/';
	$_section[1][0] = _AT('test_manager');
	$_section[1][1] = 'tools/tests/';
	$_section[2][0] = _AT('edit_test');

	$tid = intval($_GET['tid']);
	if ($tid == 0){
		$tid = intval($_POST['tid']);
	}

if (isset($_POST['cancel'])) {
	header('Location: index.php?f=' . AT_FEEDBACK_CANCELLED);
	exit;
} else if ($_POST['submit']) {
	$_POST['title']				= $addslashes(trim($_POST['title']));
	$_POST['format']			= intval($_POST['format']);
	$_POST['randomize_order']	= intval($_POST['randomize_order']);
	$_POST['num_questions']		= intval($_POST['num_questions']);
	$_POST['num_takes']			= intval($_POST['num_takes']);
	$_POST['anonymous']			= intval($_POST['anonymous']);
	$_POST['instructions']      = $addslashes($_POST['instructions']);

	/* avman */
	$_POST['difficulty'] = intval($_POST['difficulty']);
	if ($_POST['difficulty'] == '') {
		$_POST['difficulty'] = 0;
	}	    
	$_POST['content_id'] = intval($_POST['content_id']);
	if ($_POST['content_id'] == '') {
		$_POST['content_id'] = 0;
	}	  		

	$_POST['instructions'] = trim($_POST['instructions']);

	if ($_POST['title'] == '') {
		$errors[] = AT_ERROR_NO_TITLE;
	}

	$day_start	= intval($_POST['day_start']);
	$month_start= intval($_POST['month_start']);
	$year_start	= intval($_POST['year_start']);
	$hour_start	= intval($_POST['hour_start']);
	$min_start	= intval($_POST['min_start']);

	$day_end	= intval($_POST['day_end']);
	$month_end	= intval($_POST['month_end']);
	$year_end	= intval($_POST['year_end']);
	$hour_end	= intval($_POST['hour_end']);
	$min_end	= intval($_POST['min_end']);

	if (!checkdate($month_start, $day_start, $year_start)) {
		$errors[]= AT_ERROR_START_DATE_INVALID;
	}

	if (!checkdate($month_end, $day_end, $year_end)) {
		$errors[]=AT_ERROR_END_DATE_INVALID;
	}

	if (strlen($month_start) == 1){
		$month_start = "0$month_start";
	}
	if (strlen($day_start) == 1){
		$day_start = "0$day_start";
	}
	if (strlen($hour_start) == 1){
		$hour_start = "0$hour_start";
	}
	if (strlen($min_start) == 1){
		$min_start = "0$min_start";
	}
	if (strlen($month_end) == 1){
		$month_end = "0$month_end";
	}
	if (strlen($day_end) == 1){
		$day_end = "0$day_end";
	}
	if (strlen($hour_end) == 1){
		$hour_end = "0$hour_end";
	}
	if (strlen($min_end) == 1){
		$min_end = "0$min_end";
	}

	$start_date = "$year_start-$month_start-$day_start $hour_start:$min_start:00";
	$end_date	= "$year_end-$month_end-$day_end $hour_end:$min_end:00";

	if (!$errors) {
		$sql = "UPDATE ".TABLE_PREFIX."tests SET title='$_POST[title]', format=$_POST[format], start_date='$start_date', end_date='$end_date', randomize_order=$_POST[randomize_order], num_questions=$_POST[num_questions], instructions='$_POST[instructions]', content_id=$_POST[content_id],  automark=$_POST[automark], random=$_POST[random], difficulty=$_POST[difficulty], num_takes=$_POST[num_takes], anonymous=$_POST[anonymous] WHERE test_id=$tid AND course_id=$_SESSION[course_id]";

		$result = mysql_query($sql, $db);

		header('Location: index.php?f='.urlencode_feedback(AT_FEEDBACK_TEST_UPDATED));
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');

echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<a href="tools/" class="hide"><img src="images/icons/default/square-large-tools.gif"  class="menuimageh2" border="0" vspace="2" width="42" height="40" alt="" /></a>';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo ' <a href="tools/" class="hide">'._AT('tools').'</a>';
	}
echo '</h2>';

echo '<h3>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '&nbsp;<img src="images/icons/default/test-manager-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo '<a href="tools/tests/">'._AT('test_manager').'</a>';
	}
echo '</h3>';

	echo '<h3>'._AT('edit_test').'</h3>';

	if (!$_POST['submit']) {
		$sql	= "SELECT * FROM ".TABLE_PREFIX."tests WHERE test_id=$tid AND course_id=$_SESSION[course_id]";
		$result	= mysql_query($sql, $db);

		if (!($row = mysql_fetch_assoc($result))){
			$errors[]=AT_ERROR_TEST_NOT_FOUND;
			print_errors($errors);
			require (AT_INCLUDE_PATH.'footer.inc.php');
			exit;
		}

		$_POST	= $row;
	} else {
		$_POST['start_date'] = $start_date;
		$_POST['end_date']	 = $end_date;
	}
print_errors($errors);

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
	<input type="hidden" name="tid" value="<?php echo $tid; ?>" />
	<input type="hidden" name="format" value="0" />
	<input type="hidden" name="randomize_order" value="1" />
	<input type="hidden" name="instructions" value="" />
	<input type="hidden" name="difficulty" value="0" />

<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
<tr>
	<th colspan="2" class="left"><?php echo _AT('edit_test'); ?></th>
</tr>
<tr>
	<td class="row1" align="right"><label for="title"><b><?php echo _AT('title'); ?>:</b></label></td>
	<td class="row1"><input type="text" name="title" id="title" class="formfield" size="40"	value="<?php 
		echo stripslashes(htmlspecialchars($_POST['title'])); ?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="num_t"><b><?php echo _AT('num_takes_test'); ?>:</b></label></td>
	<td class="row1"><select name="num_takes" id="num_t">
				<option value="<?php echo AT_TESTS_TAKE_UNLIMITED; ?>" <?php if ($_POST['num_takes'] == AT_TESTS_TAKE_UNLIMITED) { echo 'selected="selected"'; } ?>><?php echo _AT('unlimited'); ?></option>
				<option value="1"<?php if ($_POST['num_takes'] == 1) { echo ' selected="selected"'; } ?>>1</option>
				<option value="2"<?php if ($_POST['num_takes'] == 2) { echo ' selected="selected"'; } ?>>2</option>
				<option value="3"<?php if ($_POST['num_takes'] == 3) { echo ' selected="selected"'; } ?>>3</option>
				<option value="4"<?php if ($_POST['num_takes'] == 4) { echo ' selected="selected"'; } ?>>4</option>
				<option value="5"<?php if ($_POST['num_takes'] == 5) { echo ' selected="selected"'; } ?>>5</option>
				<option value="6"<?php if ($_POST['num_takes'] == 6) { echo ' selected="selected"'; } ?>>6</option>
				<option value="7"<?php if ($_POST['num_takes'] == 7) { echo ' selected="selected"'; } ?>>7</option>
				<option value="8"<?php if ($_POST['num_takes'] == 8) { echo ' selected="selected"'; } ?>>8</option>
				<option value="9"<?php if ($_POST['num_takes'] == 9) { echo ' selected="selected"'; } ?>>9</option>
				<option value="10"<?php if ($_POST['num_takes'] == 10) { echo ' selected="selected"'; } ?>>10</option>
				<option value="15"<?php if ($_POST['num_takes'] == 15) { echo ' selected="selected"'; } ?>>15</option>
				<option value="20"<?php if ($_POST['num_takes'] == 20) { echo ' selected="selected"'; } ?>>20</option>
				<option value="25"<?php if ($_POST['num_takes'] == 25) { echo ' selected="selected"'; } ?>>25</option>
				<option value="30"<?php if ($_POST['num_takes'] == 30) { echo ' selected="selected"'; } ?>>30</option>
				<option value="35"<?php if ($_POST['num_takes'] == 35) { echo ' selected="selected"'; } ?>>35</option>
				<option value="40"<?php if ($_POST['num_takes'] == 40) { echo ' selected="selected"'; } ?>>40</option>
				<option value="45"<?php if ($_POST['num_takes'] == 45) { echo ' selected="selected"'; } ?>>45</option>
				<option value="50"<?php if ($_POST['num_takes'] == 50) { echo ' selected="selected"'; } ?>>50</option>
				<option value="60"<?php if ($_POST['num_takes'] == 60) { echo ' selected="selected"'; } ?>>60</option>
				<option value="70"<?php if ($_POST['num_takes'] == 70) { echo ' selected="selected"'; } ?>>70</option>
				<option value="80"<?php if ($_POST['num_takes'] == 80) { echo ' selected="selected"'; } ?>>80</option>
				<option value="90"<?php if ($_POST['num_takes'] == 90) { echo ' selected="selected"'; } ?>>90</option>
				<option value="100"<?php if ($_POST['num_takes'] == 100) { echo ' selected="selected"'; } ?>>100</option>
			</select>
	</td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><b><?php echo _AT('anonymous_test'); ?>:</b></td>
	<td class="row1"><?php 
		if ($_POST['anonymous'] == 1) {
			$y = 'checked="checked"';
			$n = '';
		} else {
			$y = '';
			$n = 'checked="checked"';
		}
	?>
	<input type="radio" name="anonymous" id="anonN" value="0" <?php echo $n; ?> /><label for="anonN"><?php echo _AT('no1'); ?></label> <input type="radio" name="anonymous" value="1" id="anonY" <?php echo $y; ?> /><label for="anonY"><?php echo _AT('yes1'); ?></label></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><b><?php echo _AT('marking'); ?>:</b></td>
	<td class="row1" nowrap="nowrap">
	<?php
		if ($_POST['automark'] == AT_MARK_INSTRUCTOR) {
			$check_mark_instructor = 'checked="checked"';
			$check_self_marking = $check_self_marking_uncounted = '';

		} else if ($_POST['automark'] == AT_MARK_SELF) {
			$check_mark_instructor = $check_dontmark = '';
			$check_self_marking = 'checked="checked"';

		} else if ($_POST['automark'] == AT_MARK_UNMARKED) {
			$check_mark_instructor = $check_self_marking = '';
			$check_dontmark = 'checked="checked"';
		}
	?>

		<input type="radio" name="automark" id="a1" value="<?php echo AT_MARK_INSTRUCTOR; ?>" <?php echo $check_mark_instructor; ?> /><label for="a1"><?php echo _AT('mark_instructor'); ?></label><br />
		<input type="radio" name="automark" id="a2" value="<?php echo AT_MARK_SELF; ?>" <?php echo $check_self_marking; ?> /><label for="a2"><?php echo _AT('self_marking'); ?></label><br />
		<input type="radio" name="automark" id="a3" value="<?php echo AT_MARK_UNMARKED; ?>" <?php echo $check_dontmark; ?>/><label for="a3"><?php echo _AT('dont_mark'); ?></label></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><b><?php echo _AT('randomize_questions'); ?>:</b></td>
	<td class="row1"><?php 
		if ($_POST['random'] == 1) {
			$y = 'checked="checked"';
			$n = $disabled = '';
		} else {
			$y = '';
			$n = 'checked="checked"';
			$disabled = 'disabled="disabled" ';
		}
	?>
	<input type="radio" name="random" id="random" value="0" checked="checked" onfocus="document.form.num_questions.disabled=true;" /><label for="random"><?php echo _AT('no1'); ?></label>. <input type="radio" name="random" value="1" id="ry" onfocus="document.form.num_questions.disabled=false;" <?php echo $y; ?> /><label for="ry"><?php echo _AT('yes1'); ?></label>, <input type="text" name="num_questions" id="num_questions" class="formfieldR" size="2" value="<?php echo $_POST['num_questions']; ?>" <?php echo $disabled . $n; ?> /> <label for="num_questions"><?php echo _AT('num_questions_per_test'); ?></label></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><b><?php echo _AT('start_date'); ?>:</b></td>
	<td class="row1"><?php
				
			$today_day   = substr($_POST['start_date'], 8, 2);
			$today_mon   = substr($_POST['start_date'], 5, 2);
			$today_year  = substr($_POST['start_date'], 0, 4);

			$today_hour  = substr($_POST['start_date'], 11, 2);
			$today_min   = substr($_POST['start_date'], 14, 2);

			$name = '_start';
			require(AT_INCLUDE_PATH.'html/release_date.inc.php');

	?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><b><?php echo _AT('end_date'); ?>:</b></td>
	<td class="row1"><?php
				
			$today_day   = substr($_POST['end_date'], 8, 2);
			$today_mon   = substr($_POST['end_date'], 5, 2);
			$today_year  = substr($_POST['end_date'], 0, 4);

			$today_hour  = substr($_POST['end_date'], 11, 2);
			$today_min   = substr($_POST['end_date'], 14, 2);

			$name = '_end';
			require(AT_INCLUDE_PATH.'html/release_date.inc.php');

	?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="inst"><b><?php echo _AT('special_instructions'); ?>:</b></label></td>
	<td class="row1"><textarea name="instructions" cols="35" rows="3" class="formfield" id="inst"><?php echo htmlspecialchars($_POST['instructions']); ?></textarea></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="center" colspan="2"><input type="submit" value="<?php echo _AT('save_test_properties');  ?> Alt-s" class="button" name="submit" accesskey="s" /> - <input type="submit" value="<?php echo _AT('cancel'); ?>" class="button" name="cancel" /></td>
</tr>
</table>
</form>
<br />
<?php
	require (AT_INCLUDE_PATH.'footer.inc.php');
?>