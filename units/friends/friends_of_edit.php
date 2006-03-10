<?php

	// Given a user ID as a parameter, will display a list of friends

	$url = url;
	
	if (isset($parameter[0])) {

		$user_id = (int) $parameter[0];
		
		$result = db_query("select users.* from friends
									join users on users.ident = friends.owner
									where friend = $user_id and users.user_type = 'person'");

		$body = <<< END
	<div class="networktable">
	<table>
		<tr>
END;
		$i = 1;
		if (sizeof ($result) > 0) {
			
			$icon = "default.png";
			$defaulticonparams = @getimagesize(path . "_icons/data/default.png");
			
			foreach($result as $key => $info) {
				list($width, $height, $type, $attr) = $defaulticonparams;
				// $info = $info[0];
				// if ($info->icon != -1) {
					$icon = db_query("select filename from icons where ident = " . $info->icon . " and owner = " . $info->ident);
					if (sizeof($icon) == 1) {
						$icon = $icon[0]->filename;
						if (!(list($width, $height, $type, $attr) = @getimagesize(path . "_icons/data/" . $icon))) {
							$icon = "default.png";
							list($width, $height, $type, $attr) = $defaulticonparams;
						}
					} else {
						$icon = "default.png";
					}
				// }
				
				if (sizeof($parameter[1]) > 4) {
					$width = round($width / 2);
					$height = round($height / 2);
				}
				$friends_username = stripslashes($info->username);
				$friends_name = htmlentities(stripslashes($info->name));
				$friends_menu = run("users:infobox:menu",array($info->ident));
				$body .= <<< END
		<td>
			<p>
			<a href="{$url}{$friends_username}/">
			<img src="{$url}_icons/data/{$icon}" width="{$width}" height="{$height}" alt="{$friends_name}" border="0" /></a><br />
			<span class="userdetails">
				{$friends_name}
				{$friends_menu}
			</span>
			</p>
		</td>
END;
				if ($i % 5 == 0) {
					$body .= "</tr><tr>";
				}
				$i++;
			}
		} else {
			if ($user_id == $_SESSION['userid']) {
				$body .= "<td><p>" . gettext("Nobody's listed you as a friend! Maybe you need to start chatting to some other users?") . "</p></td>";
			} else {
				$body .= "<td><p>" . gettext("This user isn't currently listed as anyone's friend. Maybe you could be the first?") . "</p></td>";
			}
		}
		$body .= <<< END
	</tr>
	</table>
	</div>
END;
	}

	$run_result .= $body;
		
?>