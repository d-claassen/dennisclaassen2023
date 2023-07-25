const wpJestConfig = require( '@wordpress/scripts/config/jest-unit.config.js' );

const config = {
	...wpJestConfig,
	collectCoverageFrom: [
		'src/**',
	],
};

module.exports = config;
