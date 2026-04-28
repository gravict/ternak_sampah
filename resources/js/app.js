import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const alerts = document.querySelectorAll('.alert-toast');
    alerts.forEach(alert => {
        setTimeout(() => alert.remove(), 5000);
    });
});
