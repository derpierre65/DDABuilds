import {formatSEOTitle, kebabCase, ucfirst} from './string';

const STATUS_PUBLIC = 1;
const STATUS_UNLISTED = 2;
const STATUS_PRIVATE = 3;

function buildLinkParams(build) {
	return {
		id: build.id,
		title: formatSEOTitle(build.title),
	};
}

function getSteamAvatar(avatarHash, size = 'medium') {
	return 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/' + avatarHash.substring(0, 2) + '/' + avatarHash + '_' + size + '.jpg';
}

function buildListSearch(options = {}) {
	options = JSON.parse((JSON.stringify(options)));

	// clear empty fields
	for (const key of Object.keys(options)) {
		if (!options[key] || Array.isArray(options[key]) && options[key].length === 0) {
			delete options[key];
		}
	}

	for (const key of ['map', 'gameMode', 'difficulty']) {
		if (typeof options[key] === 'undefined') {
			continue;
		}

		if (Array.isArray(options[key])) {
			let values = [];
			for (const item of options[key]) {
				if (typeof item === 'string') {
					values.push(item);
				}
				else {
					values.push(item.value);
				}
			}

			options[key] = values;
		}

		options[key] = Array.isArray(options[key]) ? ucfirst(options[key]).join(',') : ucfirst(options[key]);
	}

	const params = {};
	for ( const key of Object.keys(options) ) {
		params[kebabCase(key)] = options[key];
	}

	return params;
}

export {
	STATUS_PUBLIC,
	STATUS_UNLISTED,
	STATUS_PRIVATE,
	buildLinkParams,
	buildListSearch,
	getSteamAvatar,
};