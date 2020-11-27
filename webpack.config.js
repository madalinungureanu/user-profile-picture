const TerserPlugin = require('terser-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const FixStyleOnlyEntriesPlugin = require("webpack-fix-style-only-entries");

module.exports = {
	mode: process.env.NODE_ENV,
	entry: {
		'blocks.build': ['./src/blocks.js'],
		'blocks.editor.build': ['./src/block/editor.scss'],
		'blocks.style.build': ['./src/block/style.scss'],
		'profile-picture': ['./src/scss/profile-picture.scss'],
		'admin': ['./src/scss/admin.scss'],
	},
	output: {
		filename: '[name].js',
	},
	externals: {
		react: 'React',
		'react-dom': 'ReactDOM',
	},
	module: {
		rules: [
			{
				test: /\.(png|jpg|gif)(\?v=\d+\.\d+\.\d+)?$/,
				use: [
					{
						loader: 'file-loader',
						options: {
							name: '[name].[ext]',
							outputPath: 'images/',
							esModule: false,
						},
					},
				],
			},
			{
				test: /\.svg$/,
				use: [
					{
						loader: 'svg-url-loader',
						options: {
							limit: 10000,
						},
					},
				],
			},
			{
				test: /\.scss$/,
				exclude: /(node_modules|bower_components)/,
				use: [
					{
						loader: MiniCssExtractPlugin.loader,
					},
					{
						loader: "css-loader",
						options: {
							sourceMap: true,
							url: false,
						},
					},
					"sass-loader",
				],
			},
			{
				test: /\.css$/,
				exclude: /(node_modules|bower_components)/,
				loader: 'babel-loader',
				options: {presets: ['@babel/env']},
			},
			{
				test: /\.(js|jsx)$/,
				exclude: /(node_modules|bower_components)/,
				loader: 'babel-loader',
				options: {
					presets: ['@babel/preset-env', '@babel/preset-react'],
					plugins: [
						'@babel/plugin-proposal-class-properties',
						'@babel/plugin-transform-arrow-functions',
					],
				},
			},
		],
	},
	devtool: "source-map",
	optimization: {
		minimize: true,
		minimizer: [
			new TerserPlugin({
				terserOptions: {
					ecma: undefined,
					parse: {},
					compress: true,
					mangle: false,
					module: false,
					output: null,
					toplevel: false,
					nameCache: null,
					ie8: false,
					keep_classnames: undefined,
					keep_fnames: false,
					safari10: false,
				},
			}),
		],
	},
	plugins: [
		new FixStyleOnlyEntriesPlugin(),
		new MiniCssExtractPlugin(),
	],
};
