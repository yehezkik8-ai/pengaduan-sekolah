document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('form').forEach((form) => {
    form.addEventListener('submit', () => {
      const button = form.querySelector('button[type="submit"]');
      if (button && !button.disabled) {
        button.dataset.originalText = button.textContent;
        button.textContent = 'Memproses...';
        // Do not disable the button: browser must submit the form normally.
      }
    });
  });
});
