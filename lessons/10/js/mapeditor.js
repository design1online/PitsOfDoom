$(document).ready(function() {

	var selectedTile;
	var hoverColor = "#FF9D1A";
	var hoverBg = "#FFEFD9";
	var selectedColor = "#00CC00";
	var selectedBg = "#CEF2CE";
	var basicColor = "#000";
	var basicBg = "#D3D3D3";
	var click = false;

	//if someone hovers over a tile box
	$(".tilebox").hover(
		function () {
			if ($(this).attr("name") != "selectedTile") {
				$(this).css("border","2px solid " + hoverColor);
				$(this).css("background-color", hoverBg);
			}
		}, 
		function () {
			if ($(this).attr("name") != "selectedTile") {
				$(this).css("border","2px solid " + basicColor);
				$(this).css("background-color", basicBg);
			}
		}
	);

	//if someone clicks on a tile box
	$(".tilebox").click(
		function() {

			//reset the last selected tile if any
			if (selectedTile != undefined)
			{
				selectedTile.attr("name", "");
				selectedTile.css("border","2px solid " + basicColor);
				selectedTile.css("background-color", basicBg);
			}

			//set the selected tile
			selectedTile = $(this);
			$(this).attr("name", "selectedTile");

			$(this).css("border","2px solid " + selectedColor);
			$(this).css("background-color", selectedBg);

			$("#selectedTileInfo").attr("innerHTML", "<b>Selected Tile:</b> " + $(this).attr("id").replace("_", ", "));

			tileName = "tilename" + $(this).attr("id");
			$("#tileType").val(eval("$('#" + tileName + "')").val());

		}
	);

	//selection to the type of tile this is
	$("#tileType").change( function() {
		if (selectedTile != undefined)
		{
			//change the text displayed in the div
			temp = eval("$('#" + selectedTile.attr("id") + "')").attr("innerHTML");
			temp = $("#tileType option:selected").text() + "\n" + temp.substr(temp.indexOf("\n"), temp.length-temp.indexOf("\n"));
			eval("$('#" + selectedTile.attr("id") + "')").attr("innerHTML", temp);

			//change the id of the tile
			eval("$('#tile" + selectedTile.attr("id") + "')").val($("#tileType option:selected").val());

			//change the tile name
			eval("$('#tilename" + selectedTile.attr("id") + "')").val($("#tileType option:selected").text());
		}
		else
			alert("Please select a tile to edit first.");
	});
	
	$("#tileimage").change(function()
	{
		if (selectedTile != undefined)
		{
			imageurl = $('#tileimage option:selected').attr("title");
			eval("$('#" + selectedTile.attr("id") + "')").css("background-image", "url(" + imageurl + ")");

			//change the tileimage
			eval("$('#image" + selectedTile.attr("id") + "')").val($("#tileimage option:selected").text().replace(",", "_"));
		}
		else
			alert("Please select a tile to edit first.");
			
		click = false;
	});
	
	$("#tileimage").click(function()
	{
		if (click == true)
		{
			if (selectedTile != undefined)
			{
				imageurl = $('#tileimage option:selected').attr("title");
				eval("$('#" + selectedTile.attr("id") + "')").css("background-image", "url(" + imageurl + ")");

				//change the tileimage
				eval("$('#image" + selectedTile.attr("id") + "')").val($("#tileimage option:selected").text().replace(",", "_"));
			}
			else
				alert("Please select a tile to edit first.");
		}
			
		click = !click;
	});

	try {
		oHandler = $(".sheet").msDropDown().data("dd");
		oHandler.visible(true);

		$("#ver").html($.msDropDown.version);
	}
	catch (err) {
		;
	}
});
