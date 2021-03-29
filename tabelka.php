<?php
	echo "<br><table><tr><th>Identyfikator</th><th>Nazwa</th><th>Poziom</th><th></th></tr>";
	echo "<tr>";
	$userss = get_users($users_file, $separator);
	 if(isset($_GET['topic'])) $topicI = $_GET['topic'];
	 else $topicI = "";
   foreach($userss as $k=>$v){
				$id = $v['userid'];
				$username = $v['username'];
				$perm = $v['permission'];
			
			echo "<td>".htmlspecialchars($id)."</td>";
			echo "<td>".htmlspecialchars($username)."</td>";
			echo "<td>$perm</td>";
			echo "<td>";
			if($id != "admin"){
				echo "<a style='margin-right: 3px;' href='?topic=$topicI&cmd=changeperm&userid=".htmlentities($id)."'>Zmień</a>";
				echo "<a class='danger' href='?topic=$topicI&cmd=deleteuser&userid=".htmlentities($id)."'>Kasuj</a>";
			}
			echo "</td>";
			echo "</tr>";
		 }
	echo "</table>";
?>
