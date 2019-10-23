
alert_S = $('#alert_s');
alert_W = $('#alert_w');
alert_D = $('#alert_d');

function add_element_array(array, text, separator = null) {
	let i = array.length,
		j = 0,
		k;

	if (separator) {
		text = text.split(separator);
		k = text.length + i;
		while (i < k)
			array[i++] = text[j++];
	} else
		array[i] = text;
	return (array);
}