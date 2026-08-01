/**
 * BrowserSync dev proxy for local WordPress (Docker :8080).
 *
 * Prefer http://localhost:3000 — but the theme also injects the BS client on
 * :8080 when this process is running (see inc/enqueue.php).
 *
 * Run via: npm run dev
 */
module.exports = {
	proxy: 'http://localhost:8080',
	port: 3000,
	open: 'local',
	notify: true,
	cors: true,
	injectChanges: true,
	reloadDelay: 150,
	reloadDebounce: 300,
	watchEvents: ['change', 'add'],
	// Theme injects the client so :8080 also hot-reloads — avoid double snippet.
	snippet: false,
	files: [
		'assets/css/**/*.css',
		'**/*.php',
		'theme.json',
		'acf-json/**/*.json',
	],
	ignore: ['node_modules', 'assets/css/**/*.map'],
	watchOptions: {
		ignoreInitial: true,
		awaitWriteFinish: {
			stabilityThreshold: 200,
			pollInterval: 50,
		},
	},
	rewriteRules: [
		{
			match: /http:\/\/localhost:8080/g,
			replace: 'http://localhost:3000',
		},
	],
};
