// Task Management App - JavaScript

$(document).ready(function() {
    // Sidebar active link
    $('a.nav-link').each(function() {
        if ($(this).attr('href') === window.location.pathname) {
            $(this).addClass('active');
        }
    });
    
    // Modal functions
    window.openModal = function(modalId) {
        $('#' + modalId).addClass('show');
    };
    
    window.closeModal = function(modalId) {
        $('#' + modalId).removeClass('show');
    };
    
    // Close modal when clicking outside
    $(document).on('click', '.modal', function(e) {
        if (e.target === this) {
            $(this).removeClass('show');
        }
    });
    
    // Form submit handlers
    $('form').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const formData = new FormData(this);
        const action = form.attr('action');
        
        $.ajax({
            type: form.attr('method') || 'POST',
            url: action,
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    const data = JSON.parse(response);
                    if (data.success) {
                        showNotification(data.message || 'İşlem başarılı', 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showNotification(data.message || 'Bir hata oluştu', 'error');
                    }
                } catch (e) {
                    console.log(response);
                }
            },
            error: function(xhr) {
                showNotification('İstek başarısız oldu', 'error');
            }
        });
    });
    
    // Notification function
    window.showNotification = function(message, type = 'info') {
        const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
        const notification = $(`
            <div class="${bgColor} text-white px-6 py-3 rounded-lg shadow-lg fixed top-4 right-4 z-50">
                ${message}
            </div>
        `);
        
        $('body').append(notification);
        
        setTimeout(() => {
            notification.fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    };
    
    // Confirm delete
    window.confirmDelete = function(id, type) {
        if (confirm(`${type} silinecek. Emin misiniz?`)) {
            $.ajax({
                type: 'POST',
                url: `/api/${type}.php`,
                data: { action: 'delete', id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showNotification(response.message, 'success');
                        setTimeout(() => window.location.reload(), 1500);
                    }
                }
            });
        }
    };
});