(function () {
  const storageKey = 'ogms-theme';
  const root = document.documentElement;

  function getSavedTheme() {
    return localStorage.getItem(storageKey) || 'light';
  }

  function applyTheme(theme) {
    const nextTheme = theme === 'dark' ? 'dark' : 'light';
    root.setAttribute('data-theme', nextTheme);
    localStorage.setItem(storageKey, nextTheme);

    const toggle = document.getElementById('themeToggleBtn');
    if (toggle) {
      toggle.textContent = nextTheme === 'dark' ? '☀️' : '🌙';
      toggle.setAttribute('aria-label', nextTheme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
      toggle.setAttribute('title', nextTheme === 'dark' ? 'Light mode' : 'Dark mode');
    }
  }

  function createToggle() {
    if (document.getElementById('themeToggleBtn')) {
      return;
    }

    const button = document.createElement('button');
    button.type = 'button';
    button.id = 'themeToggleBtn';
    button.className = 'theme-toggle-btn';
    button.addEventListener('click', function () {
      const currentTheme = root.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
      applyTheme(currentTheme === 'dark' ? 'light' : 'dark');
    });

    document.body.appendChild(button);
    applyTheme(getSavedTheme());
  }

  applyTheme(getSavedTheme());

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', createToggle);
  } else {
    createToggle();
  }
})();
