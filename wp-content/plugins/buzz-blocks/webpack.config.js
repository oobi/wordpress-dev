// import the original config from the @wordpress/scripts package
const defaultConfig = require("@wordpress/scripts/config/webpack.config");
const path = require("path");

// add your custom config
const customConfig = {

}

// merge the two configs
module.exports = {
	...defaultConfig,
	...customConfig,
};
