<?php
/****************
* File: monsterstats.php
* Date: 3.11.2009
* Author: design1online.com
* Purpose: display all monster information and fight options
*****************/

//check to see if they've run into a monster
$monsterid = hasMonster($_SESSION['member']->curcharacter->x, $_SESSION['member']->curcharacter->y, 
			   $_SESSION['member']->curcharacter->z, $_SESSION['member']->curcharacter->mapid);

//this monster isn't already loaded into the session, load it now		   
if ($monsterid && $monsterid != $_SESSION['monster']->id) {
	$_SESSION['monster'] = new monster($monsterid);
}
			   
if ($monsterid && $_SESSION['monster']->id) {
?>
<table width="100%" cellpadding="2" cellspacing="2">
	<tr>
		<td align="center" colspan="4" class="errorMsg">
			<?php echo "Monster " . $_SESSION['monster']->typename; ?>
		</td>
	</tr>
	<tr>
		<td colspan="4">&nbsp;</td>
	</tr>
	<tr>
		<td>Level:</td>
		<td><?php echo $_SESSION['monster']->fightlvl; ?></td>
		<td>Health:</td>
		<td><?php echo $_SESSION['monster']->health; ?></td>
	</tr>
	<tr>
		<td>Magic Ability:</td>
		<td><?php echo $_SESSION['monster']->magic; ?></td>
		<td>Mana:</td>
		<td><?php echo $_SESSION['monster']->magicamount; ?></td>
	</tr>
	<tr>
		<td>Weapon:</td>
		<td><?php echo $_SESSION['monster']->weapon; ?></td>
		<td>Spell:</td>
		<td><?php echo $_SESSION['monster']->spell; ?></td>
	</tr>
	<tr>
		<td>Armor:</td>
		<td><?php echo $_SESSION['monster']->armor; ?></td>
		<td>Shield:</td>
		<td><?php echo $_SESSION['monster']->shield; ?></td>
	</tr>
	<tr>
		<td colspan="4" align="center">
			<input type="submit" name="_attack" value="Attack!" />
		<?php
		//they have a shield, let them block too
		if ($_SESSION['member']->curcharacter->shield) {
			echo "<input type=\submit\" name=\"_block\" value=\"Block\" />";
		}
			
		//they have a magic spell, let them cast it
		if ($_SESSION['member']->curcharacter->spell) {
			echo "<input type=\"submit\" name=\"_cast\" value=\"Cast!\" />";
		}
		?>
		</td>
	</tr>
</table>
<?php
}