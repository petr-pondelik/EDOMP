const path = require('path');
const webpack = require('webpack');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const WebpackCleanupPlugin = require('webpack-cleanup-plugin');
const ManifestPlugin = require('webpack-manifest-plugin');

const ROOT_DIR = path.resolve(__dirname, './../');
const WWW_DIR = path.resolve(ROOT_DIR, 'www');
const DIST_DIR = path.resolve(WWW_DIR, 'assets', 'dist');

module.exports = {
    entry: {
        app: path.resolve(ROOT_DIR, 'resources', 'app', 'app.jsx'),
    },
    output: {
        path: DIST_DIR,
        filename: '[name].[contenthash].js',
        chunkFilename: '[name].[contenthash].js'
    },
    devServer: {
        inline: false,
        contentBase: WWW_DIR,
        port: 8080,
        headers: {
            'Access-Control-Allow-Origin': '*',
        }
    },
    module: {
        rules: [
            // css
            {
                test: /\.(scss|css)$/,
                use: [
                    {
                        loader: MiniCssExtractPlugin.loader
                    },
                    {
                        loader: 'css-loader',
                        options: {
                            // Enables browsers to identify source files and lines from where styles come from (in source code)
                            sourceMap: true,
                        },
                    },
                    {
                        loader: 'sass-loader',
                        options: {
                            sourceMap: true,
                        },
                    }
                ],
            },
            // images
            {
                test: /\.(png|ico|gif|svg|jpe?g)(\?[a-z0-9]+)?$/,
                use: 'url-loader',
            },
            // fonts
            {
                test: /\.(ttf|eot|woff|woff2)?(\?v=[0-9]\.[0-9]\.[0-9])?$/,
                loader: 'url-loader',
                options: {
                    limit: 10000
                },
            }
        ]
    },
    optimization: {
        runtimeChunk: false,
        splitChunks: {
            cacheGroups: {
                vendors: {
                    test: /[\\/]node_modules[\\/]/,
                    name: 'vendors',
                    enforce: true,
                    chunks: 'all'
                }
            }
        }
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: '[name].[contenthash].css',
            chunkFilename: '[name].[contenthash].css'
        }),
        new WebpackCleanupPlugin(),
        new ManifestPlugin(),
        new webpack.ProvidePlugin({
            Nette: 'nette-forms',
            'window.Nette': 'nette-forms',
            $: 'jquery',
            jQuery: 'jquery',
            'window.jQuery': 'jquery',
            'window.FilePond': 'filepond',
            'FilePond': 'filepond',
        })
    ]
};