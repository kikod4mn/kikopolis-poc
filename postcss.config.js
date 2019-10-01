module.exports = {
    compile: {
        plugins: [{
            module: require('postcss-import'),
            options: {
                path: ['node_modules']
            }
        },
            require("tailwindcss")("./tailwind.config.js")
        ]
    },
    plugins: [
        // require('postcss-import'),
        require('tailwindcss'),
        require('postcss-nested'),
        require('postcss-custom-properties'),
        require('autoprefixer')
    ]
};