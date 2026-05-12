import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";
import typography from "@tailwindcss/typography";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./vendor/laravel/jetstream/**/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    // Safelist dynamic classes used in Blade templates
    safelist: [
        "bg-blue-500/20",
        "bg-emerald-500/20",
        "bg-amber-500/20",
        "bg-purple-500/20",
        "bg-pink-500/20",
        "bg-cyan-500/20",
        "text-blue-400",
        "text-emerald-400",
        "text-amber-400",
        "text-purple-400",
        "text-pink-400",
        "text-cyan-400",
        "bg-blue-500/30",
        "bg-emerald-500/30",
        "bg-purple-500/30",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["var(--app-font, Inter)", ...defaultTheme.fontFamily.sans],
            },
            backdropBlur: {
                xl: "20px",
            },
        },
    },

    plugins: [forms, typography],
};
