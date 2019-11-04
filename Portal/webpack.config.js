const path = require('path');
const webpack = require('webpack');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = {
  entry: './main.js',
  output: {
    filename: './js/out.js'
  },
  module: {
     rules: [
       {
         test: /\.css$/,
         use: [
           'style-loader',
           'css-loader'
         ]
       },
       {
        test: require.resolve('jquery'),
        use: [
          {
            loader: 'expose-loader',
            query: 'jQuery',
          },
          {
            loader: 'expose-loader',
            query: '$',
          },
        ]
       },
       {
        test: /.(png|ttf|otf|eot|svg|woff(2)?)(\?[a-z0-9]+)?$/,
        use: [{
          loader: 'file-loader',
          options: {
            name: '[name].[ext]',
            outputPath: 'fonts/',    // where the fonts will go
            publicPath: '../'       // override the default path
          }
        }]
      },
      {
        test: /\.scss$/,
        use: [
             MiniCssExtractPlugin.loader,
            "css-loader", // translates CSS into CommonJS
            "sass-loader" // compiles Sass to CSS, using Node Sass by default
        ]
      }
     ]
   },
   plugins: [
        new webpack.ProvidePlugin({
            '$': 'jquery'
         }),
         new MiniCssExtractPlugin({
          // Options similar to the same options in webpackOptions.output
          // both options are optional
          filename: "./css/[name].css",
          chunkFilename: "./css/[id].css"
      })
   ],
   mode: 'production'
   
};


