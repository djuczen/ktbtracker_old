
function colorScale(percent) {
	var r, g, b = 0;
	
	if (percent < 50) {
		r = 255;
		g = Math.round((5.10 * percent));
	} else {
		r = Math.round(510 - (5.10 * percent));
		g = 255;
	}
	
	var h = (r * 0x10000) + (g * 0x100) + (b * 0x1);
	
	return '#' + ('000000' + h.toString(16)).slice(-6);
}