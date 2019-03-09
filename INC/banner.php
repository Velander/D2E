<? if ($schools->sponsor_banner($project->school_id)) { ?>
	<table border=0 cellpadding=0 cellspacing=0 width="100%">
		<tr valign="middle">
			<td align="center">
				<? if ($schools->sponsor_url($project->school_id)) echo "<a href=\"".$schools->sponsor_url($project->school_id)."\" target=\"sponsor\">"; ?>
				<img src="<?=(empty($HTTPS)?$http_location:$https_location).$schools->sponsor_banner($project->school_id);?>" border=0 alt="<?=$schools->school_name($project->school_id);?> sponsored by <?=$schools->sponsor_name($project->school_id);?>">
				<? if ($schools->sponsor_url($project->school_id)) echo "</a>"; ?>
			</td>
		</tr>
	</table>
<? } elseif ($schools->sponsor_name($project->school_id) != "") { ?>
	<table border=0 cellpadding=0 cellspacing=0 width="100%">
		<tr valign="middle">
			<td align="center">
				<? if ($schools->sponsor_url($project->school_id)) echo "<a href=\"".$schools->sponsor_url($project->school_id)."\" target=\"sponsor\">"; ?>
				<H1><?=$schools->school_name($project->school_id);?> sponsored by <?=$schools->sponsor_name($project->school_id);?></H1>
				<? if ($schools->sponsor_url($project->school_id)) echo "</a>"; ?>
			</td>
		</tr>
	</table>
<? } ?>
