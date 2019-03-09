<?	require "inc/db_inc.php";
	require_once "inc/class_user.php";
	require_once "inc/class_users.php";
	require_once "inc/class_user_friend.php";

if ($debug) {
	echo "loading user<br>";
	flush();
}

	$user = new user();
	$user->load_user($User_ID);

	$message_received = $message;
	$message = "";

	if ($REQUEST_METHOD == "POST" && $Submit == "Submit") {
            if ($Submit == "Submit") {
                // Check for existing emails to update.
                $errors = "";
                $fcount = count($friend_ids);
                for ($i = 0; $i< $fcount; $i++) {
                    if ($delete_friend[$i] == "DELETE") {
                        # Delete Friend
                        if (!$user->delete_friend($friend_ids[$i]))
                            $message .= "Delete of $email[$i] failed: ".$user->error_message."<BR>";
                    } else {
                        # Update a Friend
                        if ($name[$i] && $email[$i]) {
                            $user->update_friend($friend_ids[$i], $email[$i], $name[$i], $include[$i]);
                        }
                    }
                }
                // Now check for emails to add from group list.
                $address_array = split(";", str_replace(",",";",$addresses));
                while (list($id, $address) = each($address_array)) {
                    $address = str_replace("\"","",str_replace("\\\"","",str_replace("]",")",str_replace("[","(",str_replace(">",")",str_replace("<","(",$address))))));
                    if (strstr($address,"(")) {
                        $name_array = split("\(", $address);
                        $name = trim($name_array[0]);
                        $email1 = $name_array[1];
                        $email_array = split("\)", $email1);
                        $email = trim($email_array[0]);
                    } else {
                        $name = $email = trim($address);
                    }
                    if (!empty($name) && strpos($email,"@") != 0) {
                        if (!$user->add_friend($email, $name, "Y")) {
                            $message .= "Add Friend Error: $user->error_message<BR>";
                        }
                    }
                }
                if (!$user->save_friends())
                    $message .= "Save_Friends Error: $user->error_message<BR>";
            }
	}
?>
<html>
<head>
<?
	include "inc/cssstyle.php";
	$pagename = "$config_addressbook_page_name";
	$help_msg_name = "config_addressbook_help";
	$help_msg = "$config_addressbook_help";
	$help_width = "$config_addressbook_help_width";
	$help_height = "$config_addressbook_help_height";
	require "inc/title.php";
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? require "inc/jscript.inc"; ?>
</head>
<? require "inc/body_begin.inc"; ?>
<? include "inc/nav.php"; ?>
            <td width="655" align="left" valign="top">
<?
	if ($config_addressbook_banners == "Y") include "inc/banner_ads.php";
	if (!empty($config_addressbook_paragraph1)) {
            include "inc/box_begin.htm";
            echo "$config_addressbook_paragraph1";
            include "inc/box_end.htm";
	}
	if (!empty($message_received)) {
            include "inc/box_begin.htm";
            echo "<center><b><font color='$color_error_message'>".stripslashes($message_received)."</font></b></center>";
            include "inc/box_end.htm";
	}
	if (!empty($message)) {
            include "inc/box_begin.htm";
            echo "<center><b><font color='$color_error_message'>".stripslashes($message)."</font></b></center>";
            include "inc/box_end.htm";
	}
	include "inc/dark_box_begin.htm";
	echo "<font size='+1' color=\"$color_table_hdg_font\">User Address Book</font>";
	include "inc/box_middle.htm";
            echo "<font size='+1' color=\"$color_table_hdg_font\">$config_addressbook_page_name</font>";

            include "inc/box_middle.htm";
?>
                <TABLE ALIGN="left" BORDER=0 CELLSPACING=10 CELLPADDING=0 WIDTH="100%">
                        <Form Name='frmFriends' Method='POST'>
                        <TR><TD><B>Name</B></TD><TD><B>Email</B></TD><TD align='center'><B>Active</B></TD><TD align='center'><B>Delete</B></TD></TR>
<?	# List of friends
                $i = 0;
                reset($user->friend_list);
                while (list($friendid, $friend) = each($user->friend_list)) {
                    echo "\t<TR>\n";
                    echo "\t\t<TD><input type='hidden' name='friend_ids[$i]' value=\"".$friend->friend_id."\">\n";
                    echo "\t\t<input type=\"text\" name=\"name[$i]\" size=30 value=\"".$friend->name."\"></td>\n";
                    echo "\t\t<TD><input type=\"text\" name=\"email[$i]\" size=40 value=\"".$friend->email."\"></td>\n";
                    echo "\t\t<TD align='center'><input type=\"checkbox\" name=\"include[$i]\" value=\"Y\"".($friend->include == "Y" ? " CHECKED" : "")."></td>\n";
                    echo "\t\t<TD align='center'><input type=\"checkbox\" name=\"delete_friend[$i]\" value=\"DELETE\"></td>\n";
                    echo "\t</TR>\n";
                    $i += 1;
                }
                echo "\t<TR><TD>\n";
                echo "\t<input type='hidden' name='friend_ids[$i]' value='0'>\n";
                echo "\t\t<input type=\"text\" name=\"name[$i]\" size=30 value=\"\"></td>\n";
                echo "\t\t<TD><input type=\"text\" name=\"email[$i]\" size=40 value=\"\"></td>\n";
                echo "\t\t<TD align='center'><input type=\"checkbox\" name=\"include[$i]\" value=\"\"></td>\n";
                echo "\t\t<TD></td>\n";
                echo "\t</TR>\n";

                echo "\t<TR><TD colspan=4>\n";
                echo "Or add multiple addresses at once. (Hint: copy from the To: box of your favorite email program)<BR>Example: <B>John Smith (john@somewhere.com); Andy Smith (asmith@gmail.com)</B><BR>\n";
                echo "or: <B>\"Eric Jones\" &lt;eric.j@somedomain.com&gt;, \"Susan Anderson\" &lt;susan@domain.com&gt;<br>";
                echo "\t\t<textarea name=\"addresses\" rows=\"8\" cols=\"67\"></textarea></td>\n";
                echo "\t</TR>\n";
?>
                                <TR>
                                        <TD align="center" colspan=4>
                                                <Input Type="Submit" Class="nicebtns" Name="Submit" Value="Submit">
                                        </TD>
                                </TR>
<?
                echo "</TABLE>\n";
                include "inc/box_end.htm";
?>
                                        </TD>
                                </TR>
                        </Form>
                </TABLE>
<?
		include "inc/box_end.htm";

?>
              </td>
<? require "inc/body_end.inc"; ?>
</html>
