const TerserJSPlugin = require('terser-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const autoprefixer = require('autoprefixer');

module.exports = {
    optimization: {
        minimizer: [new TerserJSPlugin({}), new OptimizeCSSAssetsPlugin({})],
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: 'css/[name].css',
            chunkName: 'css/[name].css'
        }),
    ],
    entry:{
        app: __dirname + "/src/js/app.js",
        frontend: __dirname + "/src/scss/frontend.scss",
        editor:__dirname + "/src/scss/editor.scss"
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
                    { loader: 'css-loader', options: { importLoaders: 1 } },
                    {
                        loader: 'postcss-loader'
                    },
                    {
                        loader: 'sass-loader',
                        options: {
                            plugins: () => [autoprefixer()]
                        }
                    },
                    {
                        loader: 'resolve-url-loader',
                        options: {
                            sourceMap: false,
                            engine: 'postcss'
                        }
                    },
                ]
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