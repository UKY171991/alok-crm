/**
 * Debug script to verify loader system is working
 * Add this to any page to test loader functionality
 */

// Check if loader system is loaded
function checkLoaderSystem() {
    const checks = {
        'Loader CSS': document.querySelector('link[href*="loader.css"]') !== null,
        'Loader JS': typeof window.CRMLoader !== 'undefined',
        'Show Loader Function': typeof showLoader !== 'undefined',
        'Hide Loader Function': typeof hideLoader !== 'undefined',
        'Action Loader': typeof showActionLoader !== 'undefined',
        'jQuery Integration': typeof $ !== 'undefined' && $.fn.setLoading !== 'undefined'
    };
    
    console.log('🔍 Loader System Debug Check:');
    console.table(checks);
    
    // Visual indicator
    const allWorking = Object.values(checks).every(check => check === true);
    const message = allWorking 
        ? '✅ All loader components are working!' 
        : '❌ Some loader components are missing!';
    
    console.log(message);
    
    // Create visual debug panel
    createDebugPanel(checks, allWorking);
    
    return allWorking;
}

function createDebugPanel(checks, allWorking) {
    // Remove existing debug panel
    const existingPanel = document.getElementById('loaderDebugPanel');
    if (existingPanel) {
        existingPanel.remove();
    }
    
    // Create debug panel
    const panel = document.createElement('div');
    panel.id = 'loaderDebugPanel';
    panel.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${allWorking ? '#d4edda' : '#f8d7da'};
        border: 1px solid ${allWorking ? '#c3e6cb' : '#f5c6cb'};
        color: ${allWorking ? '#155724' : '#721c24'};
        padding: 15px;
        border-radius: 8px;
        font-family: monospace;
        font-size: 12px;
        z-index: 100000;
        max-width: 300px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    `;
    
    panel.innerHTML = `
        <div style="font-weight: bold; margin-bottom: 10px;">
            🔍 Loader System Status
        </div>
        ${Object.entries(checks).map(([name, status]) => 
            `<div style="margin: 5px 0;">
                ${status ? '✅' : '❌'} ${name}
            </div>`
        ).join('')}
        <button onclick="this.parentElement.remove()" style="
            margin-top: 10px;
            background: transparent;
            border: 1px solid currentColor;
            color: inherit;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        ">Close</button>
    `;
    
    document.body.appendChild(panel);
    
    // Auto-remove after 10 seconds
    setTimeout(() => {
        if (panel.parentElement) {
            panel.remove();
        }
    }, 10000);
}

// Auto-run debug check when DOM is loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(checkLoaderSystem, 1000);
    });
} else {
    setTimeout(checkLoaderSystem, 1000);
}

// Make function globally available
window.checkLoaderSystem = checkLoaderSystem;
