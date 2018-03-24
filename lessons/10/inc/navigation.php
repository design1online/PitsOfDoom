<?php
/****************
* File: navigation.php
* Date: 3.11.2009
* Author: design1online.com
* Purpose: show the character movement options
*****************/
?>
	<center>
			<table cellpadding="2" cellspacing="2">
				<tr>
					<td colspan="2" align="center">
						<input type="submit" name="forward" value="/\" />
					</td>
				</tr>
				<tr>
					<td align="center"><input type="submit" name="left" value="<-" /></td>
					<td align="center"><input type="submit" name="right" value="->" /></tr>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<input type="submit" name="back" value="\/" />
					</td>
				</tr>
			</table>
		<?php 
			if ($_SESSION['debug'])
				echo "<br>Current Position: " . $_SESSION['member']->curcharacter->x . ", " . 
					$_SESSION['member']->curcharacter->y . ", " . $_SESSION['member']->curcharacter->z;
		?>
			</center>