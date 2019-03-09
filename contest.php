<?
    require "inc/db_inc.php";
    require_once "inc/class_cart_item.php";
    require_once "inc/class_user.php";
    require_once "inc/class_school.php";
    require_once "inc/class_schools.php";

    $schools = new schools();
    $schools->load_schools();
?>
<html>
<head>
<?	require "inc/cssstyle.php"; ?>
<META NAME="Keywords" CONTENT="Oregon City, Schools, School Donation, Donate, Donation, Education">
<?
    $pagename = "$config_contest_page_name";
    $help_msg_name = "config_contest_help";
    $help_msg = "$config_contest_help";
    $help_width = "$config_contest_help_width";
    $help_height = "$config_contest_help_height";
    require "inc/title.php";
?>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<? require "inc/jscript.inc"; ?>
</head>
<? require "inc/home_body_begin.inc"; ?>
<? include "inc/nav.php"; ?>
					<td width="655" align="left" valign="top">
<?
    if (!empty($message))
        echo "<center><b><font color='$color_error_message'>".stripslashes($message)."</font></b></center><br>";
    if (!empty($config_home_paragraph1)) {
#	include "inc/box_begin.htm";
        echo "$config_contest_paragraph1";
#	include "inc/box_end.htm";
    }
    if ($config_contest_show_results == "Y") {
        include "inc/box_begin.htm";
        echo "<table width='100%' border=0 cellspacing=3 cellpadding=4>";
        # echo "<tr><td align='center' colspan=4><b>Contest Results</b></td></tr>";
        echo "<tr><td width='25%' align='right'><b>School</b></td><td align='right'><b>Goal</b></td><td align='right'><b>Points</b></td><td width='50%' align='Center'><b>Progress</b></td></tr>\n";

        $sql = "select school.school_id, school.contest_goal, district.district_name from school ";
        $sql .= " inner join district on district.district_id = school.district_id";
        $sql .= " where contest_goal > 0 and not school.inactive='Y'";
        $sql .= " order by district.district_name, school.school_name";
        $prevdistrict="";
        $results = $db_link->query($sql);
        while (list($schoolid, $goal, $district_name) = mysqli_fetch_row($results)) {

            if ($district_name != $prevdistrict) {
                echo "<tr><td colspan='100%'><b>$district_name</td></tr>\n";
                $prevdistrict = $district_name;
            }
            # Count Donors Registered
            $sql = "select count(user_id) cnt from user where setup_date >= '".date("Y-m-d",strtotime($config_contest_begindate))."' and setup_date < '".date("Y-m-d",strtotime($config_contest_enddate))."' and referral_schoolid = '$schoolid'";
            $cntresults = $db_link->query($sql);
            list($usercount) = mysqli_fetch_row($cntresults);
            $sql = "select distinct login_log.user_id from login_log inner join user on login_log.user_id = user.user_id where user.setup_date < '".date("Y-m-d",strtotime($config_contest_begindate))."' and login_log.login_date >= '".date("Y-m-d",strtotime($config_contest_begindate))."' and login_log.login_date < '".date("Y-m-d",strtotime($config_contest_enddate))."' and referral_schoolid = '$schoolid'";
            $cntresults = $db_link->query($sql);
            $usercount += mysqli_num_rows($cntresults);

            # Count Teachers Logged In
            $sql = "select U.user_id, max(LL.`login_date`) logindate from user as U inner join `user_affiliation` as UA on U.user_id = UA.user_id inner join login_log as LL on U.user_id = LL.user_id and LL.login_date >= '".date("Y-m-d",strtotime($config_contest_begindate))."' and LL.login_date < '".date("Y-m-d",strtotime($config_contest_enddate))."' Where UA.school_id = '$schoolid' And (U.user_type_id = 20 or U.user_type_id = 25) group by U.user_id";
            if ($cntresults = $db_link->query($sql))
                $teachercount = mysqli_num_rows($cntresults);
            else
                $teachercount = 0;
            mysqli_free_result($cntresults);

            # Count Requested entered
            $sql = "select count(project.project_id) from project where project.submitted_date >= '".date("Y-m-d",strtotime($config_contest_begindate))."' and project.submitted_date < '".date("Y-m-d",strtotime($config_contest_enddate))."' and project.school_id = '$schoolid'";
            if ($cntresults = $db_link->query($sql))
                list($requestcount) = mysqli_fetch_row($cntresults);
            else
                $requestcount = 0;
            mysqli_free_result($cntresults);

            # Count Donations Made
            $sql = "SELECT sum(donation_amount) cnt FROM donation INNER JOIN donation_project ON donation.donation_id = donation_project.donation_id INNER JOIN project ON project.project_id = donation_project.project_id";
//            $sql .= " WHERE donation.donation_date >= '".date("Y-m-d",strtotime($config_contest_begindate))."' and donation.donation_date < '".date("Y-m-d",strtotime($config_contest_enddate))."' AND donation.payment_received = 'Y' AND project.school_id = '$schoolid'";
            $sql .= " WHERE not donation.user_id in (1770,3001) and donation.donation_date >= '".date("Y-m-d",strtotime($config_contest_begindate))."' and donation.donation_date < '".date("Y-m-d",strtotime($config_contest_enddate))."' AND donation.payment_received = 'Y' AND project.school_id = '$schoolid'";
            $donresults = $db_link->query($sql);
            list($donations) = mysqli_fetch_row($donresults);
//            $donations = $donations * 1;
            echo "<tr><td width='35%' align='right'>".str_replace(" School","", $schools->school_name($schoolid))."</td><td align='right'>".$goal."</td><td align='right'>".round($usercount + $donations + $teachercount + $requestcount)."</td>";
            #echo "<td align='center'>".round($progress * 170)."</td>";
            echo "<td align='center'><img src=\"images/progress_end_gray.bmp\" width=2 height=15>";
            $progress = ($usercount + $donations + $teachercount + $requestcount)/$goal;
            if ($progress > 2) $progress = 2;
            if ($progress >= 1) {
                echo "<img src=\"images/gray_dot.bmp\" height=15 width=".round((1 * 85)-1).">";
                echo "<img src=\"images/red_dot.bmp\" height=15 width=2>";
                echo "<img src=\"images/gray_dot.bmp\" height=15 width=".round(($progress * 85)-86).">";
                if ($progress < 2)
                    echo "<img src=\"images/progress_middle_gray.bmp\" height=15 width=".round(85-(($progress * 85)-85)).">";
            } else {
                echo "<img src=\"images/gray_dot.bmp\" height=15 width=".round($progress * 85).">";
                echo "<img src=\"images/progress_middle_gray.bmp\" height=15 width=".round(85-($progress * 85)).">";
                echo "<img src=\"images/red_dot.bmp\" height=15 width=2>";
                echo "<img src=\"images/progress_middle_gray.bmp\" height=15 width=85>";
            }
            echo "<img src=\"images/progress_end_gray.bmp\" width=2 height=15></td>";
            echo "<td align='right'>".sprintf("%01.1f", ((($usercount + $donations + $teachercount+$requestcount)/$goal)*100))."%</td>";
            echo "</tr>\n";
        }
        echo "<tr><td colspan=4 align='right'><Font color='red'>* Red line indicates minimum goal.</font></td><td>&nbsp;</td></tr>";
        echo "</table>\n";
        include "inc/box_end.htm";
        mysqli_free_result($results);
    }
    if ($config_contest_banners == "Y") include "inc/banner_ads.php";
?>
					</td>
<? require "inc/body_end.inc"; ?>
</html>
