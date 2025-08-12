const preset = require('./vendor/filament/filament/tailwind.config.preset')

module.exports = {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './src/**/*.php',
        './resources/views/**/*.blade.php',
    ],
    safelist: [
        { pattern: /grid-cols-\d+/, variants: ['sm', 'md', 'lg', 'xl'] },
        { pattern: /md:grid-cols-\d+/ },
        'grid-cols-1',
        'grid-cols-2', 
        'grid-cols-3',
        'grid-cols-4',
        'grid-cols-5',
        'grid-cols-6',
        'md:grid-cols-1',
        'md:grid-cols-2',
        'md:grid-cols-3', 
        'md:grid-cols-4',
        'md:grid-cols-5',
        'md:grid-cols-6',
    ],
}
