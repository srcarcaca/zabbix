<?php
/* 
** ZABBIX
** Copyright (C) 2000-2005 SIA Zabbix
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
**/
?>
<?php
	include "include/config.inc.php";
	$page["title"] = "S_SCREENS";
	$page["file"] = "screenconf.php";
	show_header($page["title"],0,0);
	insert_confirm_javascript();
?>

<?php
	show_table_header(S_CONFIGURATION_OF_SCREENS_BIG);
	echo "<br>";
?>

<?php
	if(!check_right("Screen","U",0))
	{
//		show_table_header("<font color=\"AA0000\">No permissions !</font>");
//		show_footer();
//		exit;
	}
	update_profile("web.menu.config.last",$page["file"]);
?>

<?php
	if(isset($_REQUEST["register"]))
	{
		if($_REQUEST["register"]=="add")
		{
			$result=add_screen($_REQUEST["name"],$_REQUEST["cols"],$_REQUEST["rows"]);
			show_messages($result,S_SCREEN_ADDED,S_CANNOT_ADD_SCREEN);
		}
		if($_REQUEST["register"]=="update")
		{
			$result=update_screen($_REQUEST["screenid"],$_REQUEST["name"],$_REQUEST["cols"],$_REQUEST["rows"]);
			show_messages($result, S_SCREEN_UPDATED, S_CANNOT_UPDATE_SCREEN);
		}
		if($_REQUEST["register"]=="delete")
		{
			$result=delete_screen($_REQUEST["screenid"]);
			show_messages($result, S_SCREEN_DELETED, S_CANNOT_DELETE_SCREEN);
			unset($_REQUEST["screenid"]);
		}
	}
?>

<?php
	show_table_header("SCREENS");
	$table = new Ctable(S_NO_SCREENS_DEFINED);
	$table->setHeader(array(S_ID,S_NAME,S_COLUMNS,S_ROWS,S_ACTIONS));

	$result=DBselect("select screenid,name,cols,rows from screens order by name");
	while($row=DBfetch($result))
	{
		if(!check_right("Screen","R",$row["screenid"]))
		{
			continue;
		}
		$table->addRow(array(
			$row["screenid"],
			"<a href=\"screenedit.php?screenid=".$row["screenid"]."\">".$row["name"]."</a>",
			$row["cols"],
			$row["rows"],
			"<A HREF=\"screenconf.php?screenid=".$row["screenid"]."#form\">".S_CHANGE."</A>"
			));
	}
	$table->show();
?>

<?php
	echo "<a name=\"form\"></a>";

	if(isset($_REQUEST["screenid"]))
	{
		$result=DBselect("select screenid,name,cols,rows from screens g where screenid=".$_REQUEST["screenid"]);
		$row=DBfetch($result);
		$name=$row["name"];
		$cols=$row["cols"];
		$rows=$row["rows"];
	}
	else
	{
		$name="";
		$cols=1;
		$rows=1;
	}

	show_form_begin("screenconf.screen");
	echo S_SCREEN;
	$col=0;

	show_table2_v_delimiter($col++);
	echo "<form method=\"get\" action=\"screenconf.php\">";
	if(isset($_REQUEST["screenid"]))
	{
		echo "<input class=\"biginput\" name=\"screenid\" type=\"hidden\" value=".$_REQUEST["screenid"].">";
	}
	echo S_NAME;
	show_table2_h_delimiter();
	echo "<input class=\"biginput\" name=\"name\" value=\"$name\" size=32>";

	show_table2_v_delimiter($col++);
	echo S_COLUMNS;
	show_table2_h_delimiter();
	echo "<input class=\"biginput\" name=\"cols\" size=5 value=\"$cols\">";

	show_table2_v_delimiter($col++);
	echo S_ROWS;
	show_table2_h_delimiter();
	echo "<input class=\"biginput\" name=\"rows\" size=5 value=\"$rows\">";

	show_table2_v_delimiter2();
	echo "<input class=\"button\" type=\"submit\" name=\"register\" value=\"add\">";
	if(isset($_REQUEST["screenid"]))
	{
		echo "<input class=\"button\" type=\"submit\" name=\"register\" value=\"update\">";
		echo "<input class=\"button\" type=\"submit\" name=\"register\" value=\"delete\" onClick=\"return Confirm('".S_DELETE_SCREEN_Q."');\">";
	}

	show_table2_header_end();
?>

<?php
	show_footer();
?>
