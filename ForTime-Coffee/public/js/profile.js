/**
 * PROFILE JAVASCRIPT
 */

document.addEventListener('DOMContentLoaded', function() {
    // Xử lý Sidebar Toggle
    const sidebarCollapse = document.getElementById('sidebarCollapse');
    const sidebar = document.getElementById('sidebar');
    
    if(sidebarCollapse && sidebar) {
        sidebarCollapse.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }
});