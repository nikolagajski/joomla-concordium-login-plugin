const path = require('path');

module.exports = {
	entry: './src/index.ts',
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
		path: path.resolve(__dirname, 'js'),
	},
	mode: 'development',
	devtool: 'inline-source-map',
};