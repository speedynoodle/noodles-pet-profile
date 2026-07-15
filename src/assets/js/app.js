/* app.js – minimal JavaScript for Noodles' Pet Profiles */

// Highlight the current nav link
document.addEventListener('DOMContentLoaded', () => {
    const links = document.querySelectorAll('.site-nav a');
    links.forEach(link => {
        if (link.href === window.location.href) {
            link.style.background = 'var(--color-accent)';
            link.style.color = 'var(--color-primary-dk)';
        }
    });
});

// -------------------------------------------------------
// Toiletry Quick-Log Functionality
// -------------------------------------------------------

/**
 * Show the accident confirmation modal for a toiletry log action.
 * @param {string} token - The toiletry access token
 * @param {string} logType - Either 'pee' or 'poo'
 * @param {string} petName - The pet's name (for display)
 */
function showToiletryModal(token, logType, petName) {
    const modal = document.getElementById('toiletry-modal');
    if (!modal) return;

    document.getElementById('modal-log-type').textContent = logType === 'pee' ? 'Pee' : 'Poo';
    document.getElementById('modal-pet-name').textContent = petName;

    // Store the token and log_type in the modal for use when confirming
    modal.dataset.token = token;
    modal.dataset.logType = logType;

    // Reset accident checkbox
    document.getElementById('modal-is-accident').checked = false;

    // Show the modal
    modal.style.display = 'flex';
}

/**
 * Hide the toiletry modal.
 */
function hideToiletryModal() {
    const modal = document.getElementById('toiletry-modal');
    if (modal) {
        modal.style.display = 'none';
    }
}

/**
 * Submit the toiletry log via AJAX.
 */
async function submitToiletryLog() {
    const modal = document.getElementById('toiletry-modal');
    if (!modal) return;

    const token = modal.dataset.token;
    const logType = modal.dataset.logType;
    const isAccident = document.getElementById('modal-is-accident').checked;

    try {
        const formData = new FormData();
        formData.append('token', token);
        formData.append('log_type', logType);
        formData.append('is_accident', isAccident ? '1' : '0');

        const response = await fetch('/admin/toiletry_log_add.php', {
            method: 'POST',
            body: formData,
        });

        const data = await response.json();

        if (data.success) {
            // Show success message
            showToiletryNotification(data.log, true);
            hideToiletryModal();
            
            // Refresh the logs after a short delay
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            alert('Error: ' + (data.message || 'Failed to save toiletry log'));
        }
    } catch (error) {
        console.error('Error submitting toiletry log:', error);
        alert('Error: ' + error.message);
    }
}

/**
 * Show a brief notification about the logged toiletry action.
 * @param {object} log - The log object with pet_id, log_type, is_accident, display_time
 * @param {boolean} success - Whether the action was successful
 */
function showToiletryNotification(log, success = true) {
    const container = document.querySelector('.site-main') || document.body;
    
    const notification = document.createElement('div');
    notification.className = success ? 'notification notification--success' : 'notification notification--error';
    notification.innerHTML = `
        <strong>${success ? '✓' : '✗'}</strong>
        ${log.log_type === 'pee' ? '💧' : '💩'} 
        ${log.is_accident ? '(accident)' : ''} 
        logged at ${log.display_time}
    `;
    
    container.insertBefore(notification, container.firstChild);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

/**
 * Refresh the toiletry logs list for a given pet (if it exists on the page).
 * This would require a corresponding AJAX endpoint. For now, just reload the page.
 * @param {number} petId - The pet's database ID
 */
function refreshToiletryLogs(petId) {
    // Simple reload for now; could be enhanced to fetch logs via AJAX
    location.reload();
}

// Modal close button handlers
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('toiletry-modal');
    const closeBtn = document.getElementById('modal-close-btn');
    const cancelBtn = document.getElementById('modal-cancel-btn');

    // Close on X button
    if (closeBtn) {
        closeBtn.addEventListener('click', hideToiletryModal);
    }

    // Close on Cancel button
    if (cancelBtn) {
        cancelBtn.addEventListener('click', hideToiletryModal);
    }

    // Close on outside click
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                hideToiletryModal();
            }
        });
    }
});
