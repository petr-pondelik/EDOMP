const path = require('path');
const webpack = require('webpack');
const autoprefixer = require('autoprefixer');
const WebpackCleanupPlugin = require('webpack-cleanup-plugin');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const ManifestPlugin = require('webpack-manifest-plugin');

const ROOT_DIR = path.resolve(__dirname, './../');

module.exports = {
    entry: {
        student: path.resolve(ROOT_DIR, 'resources', 'student.js'),
        teacher: path.resolve(ROOT_DIR, 'resources', 'teacher.js'),
        netteAjaxHistory: path.resolve(ROOT_DIR, 'resources', 'nette-ajax-history.js')
    },
    resolve: {
        modules: [
            'node_modules',
        ],
        extensions: ['.js', '.jsx', '.css', '.scss']
    },
    module: {
        rules: [
            // css
            {
                test: /\.(scss|css)$/,
                use: [
                    // Enables to generate CSS bundle for every JS bundle separately
                    {
                        loader: MiniCssExtractPlugin.loader,
                        options: {
                        },
                    },
                    {
                        loader: 'css-loader',
                        options: {
                            sourceMap: true,
                        },
                    },
                    {
                        loader: 'postcss-loader',
                        options: {
                            plugins: [autoprefixer('last 2 version')],
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
        // clean dist folder
        new WebpackCleanupPlugin(),
        new ManifestPlugin(),
        new MiniCssExtractPlugin({
            filename: '[name].[contenthash].css',
            chunkFilename: '[name].[contenthash].css',
        }),
        new webpack.ProvidePlugin({
            Nette: 'nette-forms',
            'window.Nette': 'nette-forms',
            '$.nette.ajax': 'nette.ajax.js',
            $: 'jquery',
            jQuery: 'jquery',
            'window.jQuery': 'jquery',
            'window.FilePond': 'filepond',
            'FilePond': 'filepond',
        })
    ]
};