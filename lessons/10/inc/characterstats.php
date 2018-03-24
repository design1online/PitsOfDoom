<?php
/****************
* File: characterstats.php
* Date: 12.10.2008
* Author: design1online.com
* Purpose: display all character statistics
*****************/
?>
<center><span class="errorMsg"></span></center><br/>

<table width="100%" cellpadding="2" cellspacing="2">
	<tr>
		<td align="center" colspan="4" class="errorMsg">
			<?php echo $_SESSION['member']->curcharacter->firstname . " " . 
				$_SESSION['member']->curcharacter->lastname;
			?>
		</td>
	</tr>
	<tr>
		<td colspan="4">&nbsp;</td>
	</tr>
	<tr>
		<td>Class:</td>
		<td><?php echo $_SESSION['member']->curcharacter->typename; ?></td>
		<td>Health:</td>
		<td><?php echo $_SESSION['member']->curcharacter->health; ?></td>
	</tr>
	<tr>
		<td>Fight Level:</td>
		<td><?php echo $_SESSION['member']->curcharacter->fightlvl; ?></td>
		<td>Magic Level:</td>
		<td><?php echo $_SESSION['member']->curcharacter->magiclvl; ?></td>
	</tr>
	<tr>
		<td>Magic Ability:</td>
		<td><?php echo $_SESSION['member']->curcharacter->health; ?></td>
		<td>Mana:</td>
		<td><?php echo $_SESSION['member']->curcharacter->magicamount; ?></td>
	</tr>
	<tr>
		<td>Strength:</td>
		<td><?php echo $_SESSION['member']->curcharacter->strength; ?></td>
		<td>Speed:</td>
		<td><?php echo $_SESSION['member']->curcharacter->speed; ?></td>
	</tr>
	<tr>
		<td>Agility:</td>
		<td><?php echo $_SESSION['member']->curcharacter->agility; ?></td>
		<td>Intelligence:</td>
		<td><?php echo $_SESSION['member']->curcharacter->intelligence; ?></td>
	</tr>
	<tr>
		<td>Weapon:</td>
		<td><?php echo $_SESSION['member']->curcharacter->weapon; ?></td>
		<td>Armor:</td>
		<td><?php echo $_SESSION['member']->curcharacter->armor; ?></td>
	</tr>
	<tr>
		<td>Spell:</td>
		<td><?php echo $_SESSION['member']->curcharacter->spell; ?></td>
		<td>Shield:</td>
		<td><?php echo $_SESSION['member']->curcharacter->shield; ?></td>
	</tr>
</table>