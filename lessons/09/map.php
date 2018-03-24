<?php 

function loadMap(direction)
{
	var movement = "";

	if (direction)
		movement = "?move=" + direction;

	$.ajax({url: "ajax_park.php" + movement, success: function(msg){
		$("#mapdata").html(msg);
		}
});

</script>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" valign=top>
				<tr>
					<td width="60%" valign="top">
						<div id="msg"><?php if ($message) echo "<center>$message</center>"; ?></div>
						<div id="mapdata"></div>
					</td>
					<td width="40%" valign="top" align="center">
						<table width="200" cellpadding=0 cellspacing=0 valign=top>
							<div id="navmenu">
												<tr>
								<td colspan="3" align="center">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="3" align="center">
									<input type="submit" value="Up" id="forward" />
								</td>
							</tr>
							<tr>
								<td align="right">
									<input type="submit" value="Left" id="left" />
								</td>
								<td align="center">
								</td>
								<td align="left">
									<input type="submit" value="right" id="right" />
								</td>
							</tr>
							<tr>
								<td colspan="3" align="center">
									<input type="submit" value="Down" id="back" />
								</td>
							</tr>
							</table>
						</div>
				</td></tr>
			</table>
</blockquote>
