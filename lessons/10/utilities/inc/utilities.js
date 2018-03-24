/****************
* File: utilities.js
* Date: 3.10.2009
* Author: jade@design1online.com
* Purpose: javascript for pits of doom utility files
*****************/
function setColor(object)
{
	object.style.backgroundColor = object.options[object.selectedIndex].style.backgroundColor;
	object.style.color = object.options[object.selectedIndex].style.color;
}

function resetColors(object)
{
	var i;
	
	for (i = 0; i < (object.length-2); i++)
			setColor(object[i]);
}