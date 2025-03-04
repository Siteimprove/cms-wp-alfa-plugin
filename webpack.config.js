const path = require("path")
const TerserPlugin = require("terser-webpack-plugin")

module.exports = {
  mode: "development",
  entry: {
    "scan-panel": "./siteimprove-alfa/assets/scan-panel.entry.js",
    "issues": "./siteimprove-alfa/assets/issues.entry.js",
    "reports": "./siteimprove-alfa/assets/reports.entry.js",
    "gutenberg": "./siteimprove-alfa/assets/gutenberg.entry.js",
  },
  output: {
    path: path.resolve(__dirname, ""),
    filename: "siteimprove-alfa/assets/[name].bundle.js",
  },
  module: {
    rules: [
      {
        test: /\.(js|jsx)$/,
        exclude: /node_modules\/(?!@siteimprove\/accessibility-cms-components)/,
        use: {
          loader: "babel-loader",
          options: {
            presets: ["@babel/preset-env", "@babel/preset-react"],
          },
        },
      },
      {
        test: /\.css$/,
        use: ["style-loader", "css-loader"]
      },
    ],
  },
  optimization: {
    minimizer: [new TerserPlugin({ extractComments: false })],
  },
  externals: {
    jquery: "jQuery",
    react: "React",
    "react-dom": "ReactDOM",
  },
  resolve: {
    extensions: [".js", ".jsx"],
  },
  devtool: "source-map",
}
