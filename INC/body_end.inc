
                </tr>
				<tr>
                  <td width="100" align="center">
<? 	if (!empty($help_msg)) {
		echo "<a href=\"javascript:display_help('$help_msg_name','$pagename','".(empty($help_width) ? $default_help_width : $help_width)."','".(empty($help_height) ? $default_help_height : $help_height)."')\">";
		include "$path_root"."inc/buttons/help_button.htm";
		echo "</a>";
	}
?>
                  </td>
                  <td>
                  </td>
                </tr>
              </table>
			</td>
          </tr>
        </table>
      </div></td>
  </tr>
  <tr><td>
<?
	include "$path_root"."inc/bottom.php";
?>
</td>
</tr>
</table>
</body>
