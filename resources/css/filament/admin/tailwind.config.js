import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    "50": "#f5f7fa",
                    "100": "#eaeef4",
                    "200": "#d0dae7",
                    "300": "#a7bad2",
                    "400": "#7896b8",
                    "500": "#5779a0",
                    "600": "#436086",
                    "700": "#344966",
                    "800": "#31435b",
                    "900": "#2c394e",
                    "950": "#1e2633",
                  },
                  secondary: {
                    "50": "#f2f6fc",
                    "100": "#e1eaf8",
                    "200": "#cadbf3",
                    "300": "#b4cded",
                    "400": "#7ca5de",
                    "500": "#5d86d4",
                    "600": "#496cc7",
                    "700": "#3f5ab6",
                    "800": "#384b95",
                    "900": "#324176",
                    "950": "#222a49",
                  },
            },
        },
    },
}
