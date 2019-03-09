<?
  require_once "inc/class_schools.php";
  require_once "inc/class_banner.php";
  if (empty($schools)) {
  	$schools = new schools;
  	$schools->load_schools();
  }
  $left_banner = 0;
  $right_banner = 0;
?>
		<table border=0 cellpadding=0 cellspacing=0 width="100%">
			<tr valign="middle">
<?
  # Need to check to see if a project is displayed.
  if (!empty($project))
  {
	if ($schools->sponsor_half_banner($project->school_id))
	{
?>
				<td align="center">
					<? if ($schools->sponsor_url($project->school_id)) echo "<a href=\"".$schools->sponsor_url($project->school_id)."\" target=\"sponsor\">"; ?>
					<img src="<?=(empty($HTTPS)?$http_location:$https_location).$schools->sponsor_half_banner($project->school_id);?>" border=0 alt="<?=$schools->school_name($project->school_id);?> sponsored by <?=$schools->sponsor_name($project->school_id);?>">
					<? if ($schools->sponsor_url($project->school_id)) echo "</a>"; ?>
				</td>
<?
      $left_banner = 1;
    } elseif ($schools->sponsor_name($project->school_id))
    {
	  if ($schools->sponsor_banner($project->school_id))
	  {
		  $right_banner = 1;
?>
				<td align="center">
					<? if ($schools->sponsor_url($project->school_id)) echo "<a href=\"".$schools->sponsor_url($project->school_id)."\" target=\"sponsor\">"; ?>
					<img src="<?=(empty($HTTPS)?$http_location:$https_location).$schools->sponsor_banner($project->school_id);?>" border=0 alt="<?=$schools->school_name($project->school_id);?> sponsored by <?=$schools->sponsor_name($project->school_id);?>">
					<? if ($schools->sponsor_url($project->school_id)) echo "</a>"; ?>
				</td>
<?
      } else {
?>
				<td align="center">
					<? if ($schools->sponsor_url($project->school_id)) echo "<a href=\"".$schools->sponsor_url($project->school_id)."\" target=\"sponsor\">"; ?>
					<H1><?=$schools->school_name($project->school_id);?><br>sponsored by<br><?=$schools->sponsor_name($project->school_id);?></H1>
					<? if ($schools->sponsor_url($project->school_id)) echo "</a>"; ?>
				</td>
<?
      }
      $left_banner = 1;
	}
  # No project is displayed, check to see if a school_id is available.
  } elseif (!empty($f_school_id))  {
    # We have a school, so look for school sponsor.
    if ($schools->sponsor_name($f_school_id))
    {
      if ($schools->sponsor_half_banner($f_school_id))
      {
?>
				<td align="center">
<?					   if ($schools->sponsor_url($f_school_id)) echo "<a href=\"".$schools->sponsor_url($f_school_id)."\" target=\"sponsor\">";
?>
					<img src="<?=(empty($HTTPS)?$http_location:$https_location).$schools->sponsor_half_banner($f_school_id);?>" border=0 alt="<?=$schools->school_name($f_school_id);?> sponsored by <?=$schools->sponsor_name($f_school_id);?>">
<?                     if ($schools->sponsor_url($f_school_id)) echo "</a>";
?>
				</td>
<?
      } elseif ($schools->sponsor_banner($f_school_id))
      {
		  $right_banner = 1;
?>
				<td align="center">
<?					   if ($schools->sponsor_url($f_school_id)) echo "<a href=\"".$schools->sponsor_url($f_school_id)."\" target=\"sponsor\">";
?>
					<img src="<?=(empty($HTTPS)?$http_location:$https_location).$schools->sponsor_banner($f_school_id);?>" border=0 alt="<?=$schools->school_name($f_school_id);?> sponsored by <?=$schools->sponsor_name($f_school_id);?>">
<?                     if ($schools->sponsor_url($f_school_id)) echo "</a>";
?>
				</td>
<?
      } else {
?>
				<td align="center">
				<? if ($schools->sponsor_url($f_school_id)) echo "<a href=\"".$schools->sponsor_url($f_school_id)."\" target=\"sponsor\">"; ?>
					<H1><?=$schools->school_name($f_school_id);?><BR>sponsored by<BR><?=$schools->sponsor_name($f_school_id);?></H1>
					<? if ($schools->sponsor_url($f_school_id)) echo "</a>"; ?>
				</td>
<?
      }
      $left_banner = 1;
    }
  }
	# Now we need to build a list of all valid banners and display one of the oldest.
	$banners = array();
    if (!empty($project))
    {
      $f_school_id = $project->school_id;
      $f_project_id = $project->project_id;
    }
    $banner = new banner;
    $banners = $banner->banner_list($f_project_id, $f_school_id, $f_district_id);
    reset($banners);
	while (!$left_banner && list($bannerid, $lastdate) = each($banners)) {
		if ($banner->load_banner($bannerid)) {
			$user = new user;
			$user->load_user($banner->user_id);
			$banner->update_last_displayed($bannerid, $UserID, $pagename, $f_project_id)
?>
				<td align="center">
<?
					if ($user->half_banner_link) {
						if ($user->url) echo "<a href=\"".($user->url)."\" target=\"sponsor\">";
?>
					<img src="<?=(empty($HTTPS)?$http_location:$https_location).$user->half_banner_link;?>" border=0 alt="<?=$user->company;?>">
<?
						if ($schools->sponsor_url($f_school_id)) echo "</a>";
						$left_banner = 1;
					} elseif ($user->banner_link) { ?>
					<img src="<?=(empty($HTTPS)?$http_location:$https_location).$user->banner_link;?>" border=0 alt="<?=$user->company;?>">
<?
						if ($schools->sponsor_url($f_school_id)) echo "</a>";
						$left_banner = 1;
					}
?>
				</td>
<?
		}
	}
	while (!$right_banner && list($bannerid, $lastdate) = each($banners)) {
		if ($banner->load_banner($bannerid)) {
			$user = new user;
			$user->load_user($banner->user_id);
			$banner->update_last_displayed($bannerid, $UserID, $pagename, $f_project_id)
?>
				<td align="center">
<?
					if ($user->half_banner_link) {
						if ($user->url) echo "<a href=\"".($user->url)."\" target=\"sponsor\">";
?>
					<img src="<?=(empty($HTTPS)?$http_location:$https_location).$user->half_banner_link;?>" border=0 alt="<?=$user->company;?>">
<?
						if ($schools->sponsor_url($f_school_id)) echo "</a>";
						$right_banner = 1;
					} elseif ($user->banner_link) { ?>
					<img src="<?=(empty($HTTPS)?$http_location:$https_location).$user->banner_link;?>" border=0 alt="<?=$user->company;?>">
<?
						if ($schools->sponsor_url($f_school_id)) echo "</a>";
						$right_banner = 1;
					}
?>
				</td>
<?
	}
  }
?>
			</tr>
		</table>
