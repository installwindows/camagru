function ft_pad(num, pad_size, base = 10)
{
	var s = num;
	var pad = '';
	if (typeof num === "number")
		s = num.toString(base);
	for (var i = s.length; i < pad_size; i++)
		pad += '0';
	return pad + '' + s;
}
