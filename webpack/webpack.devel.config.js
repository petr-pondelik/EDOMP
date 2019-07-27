const path = require('path');
const webpack = require('webpack');
const merge = require('webpack-merge');
const webpackConfig = require('./webpack.config');

const ROOT_DIR = path.resolve(__dirname, './../');
const WWW_DIR = path.resolve(ROOT_DIR, 'www');
const DIST_DIR = path.resolve(WWW_DIR, 'assets', 'dist');

module.exports = merge(webpackConfig, {
    mode: 'development',
    devtool: 'source-map',
    devServer: {
        inline: false,
        contentBase: WWW_DIR,
        port: 8080,
        headers: {
            'Access-Control-Allow-Origin': '*',
        }
    },
    output: {
        path: DIST_DIR,
        filename: '[name].[hash].js',
        chunkFilename: '[name].[hash].js',
    },
    plugins: [
        new webpack.NamedModulesPlugin(),
        new webpack.HotModuleReplacementPlugin(),
    ]
});