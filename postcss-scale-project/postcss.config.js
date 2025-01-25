// postcss.config.js
const scaleValues = require ('./postcss-scale-values.js');

module.exports = {
  plugins: [
    scaleValues ({scaleFactor: 0.8}),
    // ... otros plugins que necesites
  ],
};
