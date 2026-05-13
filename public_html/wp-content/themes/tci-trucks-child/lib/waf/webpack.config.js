const path = require('path');
module.exports = {
    mode : 'development',
    // host: '192.168.0.2',
    entry: {
      "all":"./src/js/all.js",
      "helpers": "./src/js/helpers.js",
      "simple": "./src/js/simple.js",
    },
    output: {
      path: path.resolve(__dirname,"assets"),
      filename: "js/[name].js",
      publicPath: "/assets"
    },
    node: {
      fs: 'empty'
    },
    devServer: {
      contentBase: './assets',
      host:"doyle",
      // https:true,
      writeToDisk: true,
      disableHostCheck: true
    },
    // watch: true,
    //fix handlebars warnings
    resolve:
    {
      alias: {
        'handlebars' : 'handlebars/dist/handlebars.js'
      }
    },
    module:{
      rules:[
        {
          test:/\.(s*)css$/,
          use:['style-loader','css-loader', 'sass-loader']
       }
     ]
  },
}