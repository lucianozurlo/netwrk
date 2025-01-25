// postcss-scale-values.js
// Plugin PostCSS para reescalar valores en px, em, rem, vh, vw

const reValue = /(-?\d+(\.\d+)?)(px|em|rem|vh|vw)/gi;

module.exports = (opts = {}) => {
  const scaleFactor = opts.scaleFactor || 0.8;

  return {
    // Nombre del plugin (solo descriptivo)
    postcssPlugin: 'postcss-scale-values',

    // "Once" se ejecuta una vez que el árbol CSS está parseado,
    // pero antes de la serialización final
    Once (root) {
      root.walkDecls (decl => {
        // decl.value es lo que haya a la derecha de la propiedad
        // Ej: "16px", "10px 20px", "calc(100% - 20px)", etc.
        const newValue = decl.value.replace (
          reValue,
          (match, numPart, _, unit) => {
            const number = parseFloat (numPart);
            const scaled = number * scaleFactor;
            return scaled.toFixed (2) + unit; // Ej: "12.80px"
          }
        );

        decl.value = newValue;
      });
    },
  };
};

// Esta línea indica a PostCSS que es un plugin compatible con la nueva API
module.exports.postcss = true;
