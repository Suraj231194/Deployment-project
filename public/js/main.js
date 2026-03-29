document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[data-modal-close]').forEach(function (button) {
    button.addEventListener('click', function () {
      const targetId = button.getAttribute('data-modal-close');
      const modal = targetId ? document.getElementById(targetId) : null;
      if (modal) {
        modal.classList.add('hidden');
      }
    });
  });

  document.querySelectorAll('[data-open-modal]').forEach(function (button) {
    button.addEventListener('click', function () {
      const targetId = button.getAttribute('data-open-modal');
      const modal = targetId ? document.getElementById(targetId) : null;
      if (modal) {
        modal.classList.remove('hidden');
      }
    });
  });

  document.querySelectorAll('[role="dialog"]').forEach(function (modal) {
    modal.addEventListener('click', function (event) {
      if (event.target === modal) {
        modal.classList.add('hidden');
      }
    });
  });
});
