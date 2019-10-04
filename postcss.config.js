const cssnano = require('cssnano');
const tailwindcss = require("tailwindcss");
const purgecss = require('@fullhuman/postcss-purgecss')({

    // Specify the paths to all of the template files in your project
    content: [
        './App/Views/*.aura.php',
        './App/Views/**/*.aura.php',
        './App/Views/**/**/*.aura.php',
    ],
    // Include any special characters you're using in this regular expression
    defaultExtractor: content => content.match(/[\w-/:%]+(?<!:)/g) || []
});

module.exports = {
    compile: {
        plugins: [{
            module: require('postcss-import')(),
            options: {
                path: ['node_modules']
            },
        },
        ]
    },
    plugins: [
        tailwindcss("./tailwind.config.js"),
        cssnano({
            preset: 'default',
        }),
        require('postcss-nested')(),
        require('postcss-extend')(),
        require('postcss-custom-properties')(),
        require('autoprefixer')(),
        // purgecss
    ]
};
