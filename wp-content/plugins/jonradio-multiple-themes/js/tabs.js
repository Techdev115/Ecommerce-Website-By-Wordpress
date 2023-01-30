/*	Hide all but the Tab selected.
	<div> is used in the HTML to enclose the HTML peculiar to a Tab,
	with id=jr-mt-settingsn where the last "n" is a unique number from 1 to the number of Tabs.
	onClick is used on a <a> to activate this function.
	
	Bold only the Tab selected,
	actually it is Bolder and Bold, instead of Bold and Normal.
*/
function jrMtTabs( tab, ntabs ) {
	for ( i = 1; i <= ntabs; i++ ) {
		if ( i === tab ) {
			show = 'block';
			classes = 'nav-tab nav-tab-active';
		} else {
			show = 'none';
			classes = 'nav-tab';
		}
		document.getElementById( 'jr-mt-settings' + i ).style.display = show;
		document.getElementById( 'jr-mt-tabs' + i ).className = classes;
	}
};
