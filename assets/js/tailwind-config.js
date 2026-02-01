// Ensure tailwind namespace exists before configuring
window.tailwind = window.tailwind || {};

tailwind.config = {
    theme: {
        extend: {
            colors: {
                primary: '#1a1a2e',
                accent: '#16213e',
                highlight: '#0f3460',
            }
        }
    }
};
