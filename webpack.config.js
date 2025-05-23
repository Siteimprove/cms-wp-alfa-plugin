const path = require("path")
const TerserPlugin = require("terser-webpack-plugin")

module.exports = {
  mode: "development",
  entry: {
    "scan-panel": "./siteimprove-accessibility/assets/scan-panel.entry.js",
    "issues": "./siteimprove-accessibility/assets/issues.entry.js",
    "reports": "./siteimprove-accessibility/assets/reports.entry.js",
    "gutenberg": "./siteimprove-accessibility/assets/gutenberg.entry.js",
  },
  output: {
    path: path.resolve(__dirname, ""),
    filename: "siteimprove-accessibility/assets/[name].bundle.js",
  },
  module: {
    rules: [
      {
        test: /\.(js|jsx)$/,
        exclude: /node_modules/,
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
    "@siteimprove/accessibility-cms-components": "SiteimproveAccessibilityCmsComponents",
  },
  resolve: {
    extensions: [".js", ".jsx"],
  },
  devtool: "source-map",
}
