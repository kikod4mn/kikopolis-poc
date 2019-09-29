const path = require('path');
const TerserJSPlugin = require('terser-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin');

module.exports = {
    optimization: {
        minimizer: [new TerserJSPlugin({}), new OptimizeCSSAssetsPlugin({})],
        // splitChunks: {
        //     cacheGroups: {
        //         styles: {
        //             name: 'styles',
        //             test: /\.css$/,
        //             chunks: 'all',
        //             enforce: true,
        //         },
        //     },
        // },
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: 'css/[name].css',
            chunkName: 'css/[name].css'
        }),
    ],
    entry:{
        app: __dirname + "/source/js/app.js",
        frontend: __dirname + "/source/scss/frontend.scss",
        editor:__dirname + "/source/scss/editor.scss"
    },
    output: {
        filename: 'js/[name].js',
        path: __dirname + '/public'
    },
    module: {
        rules:  [
            {test: /\.s[ac]ss$/i,
                loaders: [
                    MiniCssExtractPlugin.loader,
                    'css-loader',
                    {
                        loader: 'sass-loader',
                        options: {
                            precision: 8,
                            outputStyle: 'expanded'
                        }
                    },
                    {
                        loader: 'resolve-url-loader',
                        options: {
                            sourceMap: false,
                            // engine: 'rework',
                            engine: 'postcss'
                        }
                    }
                ]
                // use: [
                //     // {loader: 'file-loader',
                //     //     options: {
                //     //         name: 'public/css/[name].css',
                //     //     }
                //     // },
                //     {loader: MiniCssExtractPlugin.loader},
                //     {loader: 'extract-loader'},
                //     {loader: 'css-loader?-url'},
                //     {loader: 'postcss-loader'},
                //     {loader: 'sass-loader'}
                // ]
            },
            {test: /\.js$/i,
                loader: "babel-loader",
                exclude: /node_modules/,
                query: {presets:[
                    "@babel/env"
                    ]}
            }
        ]
    },
};