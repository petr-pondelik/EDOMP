const path = require('path');
const webpack = require('webpack');
const merge = require('webpack-merge');
const webpackConfig = require('./webpack.config');
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const TerserJSPlugin = require('terser-webpack-plugin');

const ROOT_DIR = path.resolve(__dirname, './../');
const WWW_DIR = path.resolve(ROOT_DIR, 'www');
const DIST_DIR = path.resolve(WWW_DIR, 'assets', 'dist');


module.exports = merge(webpackConfig, {
    mode: 'production',
    devtool: 'source-map',
    target: 'web',
    optimization: {
        minimizer: [new TerserJSPlugin(), new OptimizeCSSAssetsPlugin()]
    },
    plugins: [
        new webpack.optimize.OccurrenceOrderPlugin()
    ]
});