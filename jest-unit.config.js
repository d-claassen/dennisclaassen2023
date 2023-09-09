const wpJestConfig = require( '@wordpress/scripts/config/jest-unit.config.js' );

const config = {
	...wpJestConfig,
	collectCoverageFrom: [
		'src/**'
	],
	coveragePathIgnorePatterns: [
		'(/specs/)',
		'(\.spec\.[jt]s?$)'
	],
};

module.exports = config;
