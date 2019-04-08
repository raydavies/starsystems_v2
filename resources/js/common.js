function parseQueryString() {
	var match,
		urlParams = {},
		pl = /\+/g,
		search = /([^&=]+)=?([^&]*)/g,
		decode = function (s) {
			return decodeURIComponent(s.replace(pl, " "));
		},
		query = window.location.search.substring(1);

	while (match = search.exec(query)) {
		urlParams[decode(match[1])] = decode(match[2]);
	}
	return urlParams;
}

String.prototype.ucwords = function() {
	var str = this.toLowerCase();
	
	return str.replace(/(^([a-zA-Z]))|(\b([a-zA-Z]))/g, function($1) {
		return $1.toUpperCase();
	});
};
