const savedTheme = localStorage.getItem('theme');

// Apply the saved theme or default to light theme
if (savedTheme) {
    document.body.classList.add(savedTheme); 
} else {
    // Default to 'light' theme if no preference is saved
    document.body.classList.add('light');
}

// Add event listener to toggle the theme on button click
const toggleButton = document.getElementById('theme-toggle');

toggleButton.addEventListener('click', () => {
    // Check current theme and toggle
    if (document.body.classList.contains('light')) {
        document.body.classList.remove('light');
        document.body.classList.add('dark');
        localStorage.setItem('theme', 'dark'); // Save 'dark' to localStorage
    } else {
        document.body.classList.remove('dark');
        document.body.classList.add('light');
        localStorage.setItem('theme', 'light'); // Save 'light' to localStorage
    }
});
