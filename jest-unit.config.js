const wpJestConfig = require( '@wordpress/scripts/config/jest-unit.config.js' );

const config = {
	...wpJestConfig,
	collectCoverageFrom: [
		'src/**',
	],
	coveragePathIgnorePatterns: [
		'**/specs/**/*.[jt]s?(x)',
		'**/?(*.)spec.[jt]s?(x)',
	],
};

module.exports = config;
