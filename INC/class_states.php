<?
class states
{
	var $state_list;
	var $error_message;

	function add_state($row) {
		$newstate = new state();
		$newstate->state_code 	= $row["code"];
		$newstate->state_name	= $row["state"];
		$newstate->state_group	= $row["grp"];
		$this->state_list[$row["code"]] = $newstate;
	}

	function load_states($only_active = false) {
		global $db_link;
		if ($only_active)
		{
			$sql = "Select distinct states.* from states inner join school on school.state = states.code where school.inactive = 'N' order by states.sortorder";
		} else {
			$sql = "Select * from states order by sortorder, state";
		}
		$results = $db_link->query($sql);
		while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
			$this->add_state($row);
		}
		mysqli_free_result($results);
	}

	function state_name($state_code) {
		$state = $this->state_list[$state_code];
		return $state->state_name;
	}

	function count() {
		return count($this->state_list);
	}
}
?>