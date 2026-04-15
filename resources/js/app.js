import './bootstrap';

// Auto-dismiss flash alerts after 5 seconds
document.addEventListener('DOMContentLoaded', () => {
    const alerts = document.querySelectorAll('.alert-toast');
    alerts.forEach(alert => {
        setTimeout(() => alert.remove(), 5000);
    });
});
