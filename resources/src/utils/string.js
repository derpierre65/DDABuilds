function ucfirst(value) {
	if (value === null) {
		return '';
	}
	else if (Array.isArray(value)) {
		return value.map((str) => ucfirst(str));
	}
	else if (typeof value === 'boolean') {
		value = value.toString();
	}

	return value.substring(0, 1).toUpperCase() + value.substring(1);
}

function lcfirst(value) {
	if (value === null) {
		return '';
	}
	else if (Array.isArray(value)) {
		return value.map((str) => lcfirst(str));
	}
	else if (typeof value === 'boolean') {
		return value.toString();
	}

	return value.substring(0, 1).toLowerCase() + value.substring(1);
}

function formatSEOTitle(title) {
	title = title
		.toLowerCase()
		.replace(/[^\p{L}\p{N}]+/ug, '-')
		.substring(0, 80);
	if (title[title.length - 1] === '-') {
		title = title.substr(0, title.length - 1);
	}

	return title;
}

function formatString(name, replace = '_') {
	return name.replace(/[A-Z]/g, letter => replace + letter);
}

function hasSymbols(str) {
	return /[!"#%&'()*+,./:;<=>?@[\\\]^`{|}]/u.exec(str); // without " ", "$", "-" and "_"
}

function isPascalCase(str) {
	return !!hasSymbols(str) || /^[a-z]/u.exec(str) || /-|_|\s/u.exec(str); // kebab or snake or space
}

function camelCase(str) {
	if (isPascalCase(str)) {
		return str.charAt(0).toLowerCase() + str.slice(1);
	}
	return str.replace(/[-_](\w)/gu, (_, c) => (c ? c.toUpperCase() : ''));
}

function kebabCase(str) {
	return str
		.replace(/_/gu, '-')
		.replace(/\B([A-Z])/gu, '-$1')
		.toLowerCase();
}

export {
	lcfirst,
	ucfirst,
	formatSEOTitle,
	formatString,
	kebabCase,
	camelCase,
};