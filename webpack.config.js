const path = require('path');
const CompressionPlugin = require("compression-webpack-plugin");

module.exports = {
	entry: './index.ts',
	context: path.resolve(__dirname, 'media/plg_system_concordium/src/'),
	module: {
		rules: [
			{
				test: /\.tsx?$/,
				use: 'ts-loader',
				exclude: /node_modules/,
			},
		],
	},
	resolve: {
		extensions: ['.tsx', '.ts', '.js'],
		fallback: {
			"stream": require.resolve("stream-browserify")
		},
	},
	output: {
		filename: 'login.js',
		path: path.resolve(__dirname, 'media/plg_system_concordium/js'),
	},
	mode: 'development',
	devtool: 'inline-source-map',
	plugins: [
		new CompressionPlugin()
	]
};