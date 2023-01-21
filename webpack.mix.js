const mix = require('laravel-mix');

process.env.BUILD_TIME = Math.floor(Date.now() / 1000).toString();

mix.disableNotifications();

if (mix.inProduction()) {
	mix.version();
}

mix.webpackConfig({
	output: {
		chunkFilename: 'assets/js/[name].[hash:4].js',
	},
});

mix.js('resources/src/main.js', 'public/assets/js').vue();
mix.sass('resources/style/style.scss', 'public/assets/css');