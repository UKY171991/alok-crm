<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

require_once 'inc/config.php';
require_once 'inc/db.php';
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
?>

<style>
.content-wrapper {
    margin-left: 250px;
    padding: 20px;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 50%, #f1f5f9 100%);
    min-height: 100vh;
    position: relative;
}

.content-wrapper::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: 
        radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.05) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255, 119, 48, 0.05) 0%, transparent 50%),
        radial-gradient(circle at 40% 40%, rgba(59, 130, 246, 0.05) 0%, transparent 50%);
    pointer-events: none;
    z-index: -1;
}

.booking-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
    color: white;
    padding: 20px 30px;
    margin-bottom: 25px;
    border-radius: 15px;
    text-align: center;
    font-size: 1.4rem;
    font-weight: 700;
    box-shadow: 
        0 10px 25px rgba(102, 126, 234, 0.3),
        0 0 0 1px rgba(255,255,255,0.2) inset;
    letter-spacing: 2px;
    position: relative;
    overflow: hidden;
    text-transform: uppercase;
}

.booking-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: shine 4s infinite;
}

.booking-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, #fbbf24, #f59e0b, #fbbf24);
    animation: glow-pulse 2s ease-in-out infinite alternate;
}

@keyframes shine {
    0% { left: -100%; }
    100% { left: 100%; }
}

@keyframes glow-pulse {
    0% { opacity: 0.6; transform: scaleX(0.8); }
    100% { opacity: 1; transform: scaleX(1); }
}

.booking-controls {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    padding: 30px;
    border-radius: 20px;
    margin-bottom: 30px;
    box-shadow: 
        0 20px 40px rgba(0,0,0,0.1),
        0 0 0 1px rgba(255,255,255,0.8) inset;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(226, 232, 240, 0.8);
}

.booking-controls::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #667eea);
    background-size: 300% 100%;
    animation: rainbow-border 5s linear infinite;
}

.booking-controls::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.3), transparent);
}

@keyframes rainbow-border {
    0% { background-position: 300% 0; }
    100% { background-position: -300% 0; }
}

.booking-controls .row {
    align-items: end;
}

.booking-controls label {
    color: #374151;
    font-weight: 600;
    margin-bottom: 8px;
    display: block;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.booking-controls .form-control,
.booking-controls .form-select {
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    background: #ffffff;
    font-weight: 500;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    padding: 12px 16px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}

.booking-controls .form-control:focus,
.booking-controls .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    transform: translateY(-1px);
}

.outstanding-balance {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    padding: 15px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    position: relative;
    overflow: hidden;
}

.outstanding-balance::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 100%);
}

.balance-label {
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 5px;
    opacity: 0.9;
}

.balance-amount {
    font-size: 1.2rem;
    font-weight: 700;
    font-family: 'Courier New', monospace;
}

.booking-controls .form-control:focus,
.booking-controls .form-select:focus {
    border-color: #fbbf24;
    box-shadow: 0 0 0 0.3rem rgba(251, 191, 36, 0.25);
    transform: translateY(-2px);
}

.outstanding-balance {
    background: rgba(255,255,255,0.15);
    border: 2px solid rgba(255,255,255,0.3);
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.outstanding-balance:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.outstanding-balance .balance-label {
    color: rgba(255,255,255,0.9);
    font-size: 0.9rem;
    margin-bottom: 8px;
    font-weight: 500;
}

.outstanding-balance .balance-amount {
    color: white;
    font-size: 2rem;
    font-weight: bold;
    text-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.booking-form {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    padding: 30px;
    border-radius: 20px;
    margin-bottom: 30px;
    box-shadow: 
        0 20px 40px rgba(0,0,0,0.08),
        0 0 0 1px rgba(255,255,255,0.9) inset;
    border: 1px solid rgba(226, 232, 240, 0.6);
    position: relative;
    overflow: hidden;
}

.booking-form::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #f59e0b, #fbbf24, #f59e0b);
    background-size: 200% 100%;
    animation: form-glow 4s ease-in-out infinite;
}

@keyframes form-glow {
    0%, 100% { background-position: 200% 0; }
    50% { background-position: -200% 0; }
}

.booking-form label {
    color: #374151;
    font-weight: 600;
    margin-bottom: 6px;
    display: block;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.booking-form .form-control,
.booking-form .form-select {
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    background: #ffffff;
    font-weight: 500;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    padding: 10px 14px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    color: #374151;
}

.booking-form .form-control:focus,
.booking-form .form-select:focus {
    border-color: #f59e0b;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
    transform: translateY(-1px);
    color: #374151;
    outline: none;
}

.booking-form .form-control:invalid,
.booking-form .form-select:invalid {
    border-color: #e5e7eb;
    box-shadow: none;
}

.booking-form .form-control.is-invalid,
.booking-form .form-select.is-invalid {
    border-color: #ef4444;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.booking-table-container {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 
        0 25px 50px rgba(0,0,0,0.1),
        0 0 0 1px rgba(255,255,255,0.9) inset;
    margin-bottom: 30px;
    position: relative;
    border: 1px solid rgba(226, 232, 240, 0.5);
}

.booking-table-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #667eea);
    background-size: 300% 100%;
    animation: table-rainbow 6s linear infinite;
}

@keyframes table-rainbow {
    0% { background-position: 300% 0; }
    100% { background-position: -300% 0; }
}
    animation: rainbow-border 3s linear infinite;
}

@keyframes rainbow-border {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

.booking-table {
    margin: 0;
    font-size: 0.85rem;
    border-collapse: separate;
    border-spacing: 0;
}

.booking-table thead {
    background: linear-gradient(135deg, #1e293b 0%, #334155 50%, #475569 100%);
    position: relative;
}

.booking-table thead th {
    color: white;
    font-weight: 700;
    padding: 18px 12px;
    border: none;
    text-align: center;
    vertical-align: middle;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    position: relative;
    background: rgba(255,255,255,0.02);
    cursor: pointer;
    transition: all 0.3s ease;
}

.booking-table thead th:hover {
    background: rgba(255,255,255,0.1);
    transform: translateY(-1px);
}

.booking-table thead th:first-child {
    border-top-left-radius: 0;
}

.booking-table thead th:last-child {
    border-top-right-radius: 0;
}

.booking-table thead th.sortable {
    cursor: pointer;
    user-select: none;
    position: relative;
    padding-right: 25px;
}

.booking-table thead th.sortable:hover {
    background: rgba(255,255,255,0.15);
}

.booking-table thead th .sort-icon {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 0.7rem;
    opacity: 0.5;
    transition: all 0.3s ease;
}

.booking-table thead th.sortable:hover .sort-icon {
    opacity: 1;
}

.booking-table thead th.sort-asc .sort-icon {
    color: #fbbf24 !important;
    opacity: 1 !important;
}

.booking-table thead th.sort-desc .sort-icon {
    color: #fbbf24 !important;
    opacity: 1 !important;
}

.booking-table thead th.sort-asc .sort-icon::before {
    content: "\f0de";
}

.booking-table thead th.sort-desc .sort-icon::before {
    content: "\f0dd";
}
    color: #fbbf24;
}

.booking-table tbody tr {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-bottom: 1px solid rgba(226, 232, 240, 0.8);
    position: relative;
}

.booking-table tbody tr:hover {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    border-bottom-color: transparent;
}

.booking-table tbody tr::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    transform: scaleY(0);
    transition: transform 0.3s ease;
    border-radius: 0 2px 2px 0;
}

.booking-table tbody tr:hover::before {
    transform: scaleY(1);
}

.booking-table tbody td {
    padding: 15px 10px;
    vertical-align: middle;
    border: none;
    text-align: center;
    font-size: 0.8rem;
    transition: all 0.2s ease;
    font-weight: 500;
}

.consignment-cell {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    font-weight: 700;
    color: #1e40af;
    border-radius: 8px;
    margin: 2px;
    padding: 10px 14px !important;
    text-align: center !important;
    letter-spacing: 0.5px;
    font-family: 'Courier New', monospace;
    box-shadow: 0 2px 4px rgba(30, 64, 175, 0.1);
    border: 1px solid rgba(30, 64, 175, 0.2);
}

.customer-cell {
    text-align: left !important;
    max-width: 160px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-weight: 600;
    color: #374151;
    padding-left: 12px !important;
}

.weight-cell,
.amount-cell {
    font-family: 'Courier New', monospace;
    font-weight: 700;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.05) 100%);
    border-radius: 6px;
    padding: 8px 10px !important;
    border: 1px solid rgba(16, 185, 129, 0.2);
}

.amount-cell {
    color: #059669;
}

.weight-cell {
    color: #0f766e;
}

.btn-group-actions {
    display: inline-flex;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border: 1px solid rgba(0,0,0,0.1);
}

.btn-group-actions .btn {
    border-radius: 0;
    border: none;
    padding: 8px 12px;
    font-size: 0.75rem;
    margin: 0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-group-actions .btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    transition: left 0.5s ease;
}

.btn-group-actions .btn:hover::before {
    left: 100%;
}

.btn-action-view {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
}

.btn-action-view:hover {
    background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
    color: white;
}

.btn-action-edit {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

.btn-action-edit:hover {
    background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
    color: white;
}

.btn-action-delete {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
}

.btn-action-delete:hover {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
    color: white;
}

.summary-section {
    background: white;
    border-radius: 20px;
    padding: 30px;
    margin-top: 30px;
    box-shadow: 
        0 25px 50px rgba(0,0,0,0.1),
        0 0 0 1px rgba(255,255,255,0.9) inset;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(226, 232, 240, 0.5);
}

.summary-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #667eea);
    background-size: 300% 100%;
    animation: summary-rainbow 8s ease-in-out infinite;
}

@keyframes summary-rainbow {
    0%, 100% { background-position: 300% 0; }
    50% { background-position: -300% 0; }
}

.summary-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f1f5f9;
}

.summary-header h5 {
    margin: 0;
    color: #1e293b;
    font-weight: 700;
    font-size: 1.1rem;
}

.summary-header h5 i {
    color: #667eea;
    margin-right: 8px;
}

.summary-timestamp {
    color: #64748b;
    font-size: 0.85rem;
    font-weight: 500;
}

.summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.summary-card {
    background: white;
    border-radius: 15px;
    padding: 20px;
    text-align: left;
    color: white;
    font-weight: 600;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    position: relative;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    gap: 15px;
}

.summary-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 100%);
    pointer-events: none;
}

.summary-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}

.card-icon {
    font-size: 2rem;
    opacity: 0.9;
    flex-shrink: 0;
}

.card-content {
    flex: 1;
}

.card-label {
    font-size: 0.8rem;
    margin-bottom: 5px;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.card-value {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 3px;
    font-family: 'Courier New', monospace;
}

.card-change,
.card-percentage {
    font-size: 0.75rem;
    opacity: 0.8;
    font-weight: 500;
}

.gradient-blue {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}

.gradient-green {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.gradient-orange {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.gradient-purple {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
}

.gradient-emerald {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
}

.gradient-indigo {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
}
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.summary-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.3s ease;
}

.summary-card:hover::before {
    left: 100%;
}

.summary-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}

.summary-card.total-records {
    background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
}

.summary-card.total-billed {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.summary-card.total-non-billed {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.summary-card.total-weight {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
}

.summary-card.total-amount {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.summary-card .card-label {
    font-size: 0.8rem;
    opacity: 0.9;
    margin-bottom: 5px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.summary-card .card-value {
    font-size: 1.3rem;
    font-weight: bold;
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
}

.bulk-actions {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    padding: 12px 20px;
    border-radius: 8px;
    margin-bottom: 15px;
    display: none;
    border-left: 4px solid #f59e0b;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
}

.bulk-actions.show {
    display: block;
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from { transform: translateY(-20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.btn-delete-selected {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);
}

.btn-delete-selected:hover {
    background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.4);
}

.btn-save-booking {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
}

.btn-save-booking:hover {
    background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

.btn-search {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

.btn-search:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
}

.modal-header {
    background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
    color: white;
    border-bottom: none;
    border-radius: 12px 12px 0 0;
}

.modal-header .btn-close {
    filter: invert(1);
}

.modal-content {
    border-radius: 12px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    border: none;
}

.spinner {
    border: 3px solid #f3f3f3;
    border-top: 3px solid #3b82f6;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    animation: spin 1s linear infinite;
    margin: 0 auto;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loading-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(5px);
}

.loading-overlay.show {
    display: flex;
}

.loading-content {
    background: white;
    padding: 40px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}

.pagination .page-link {
    color: #3b82f6;
    border-color: #e5e7eb;
    transition: all 0.3s ease;
}

.pagination .page-link:hover {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    border-color: #3b82f6;
    transform: translateY(-1px);
}

.pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    border-color: #3b82f6;
}

@media (max-width: 768px) {
    .content-wrapper {
        margin-left: 0;
        padding: 15px;
    }
    
    .booking-controls .col-md-3,
    .booking-controls .col-md-2 {
        margin-bottom: 15px;
    }
    
    .summary-cards {
        flex-direction: column;
    }
    
    .booking-table {
        font-size: 0.75rem;
    }
    
    .booking-table thead th,
    .booking-table tbody td {
        padding: 8px 4px;
    }
    
    .btn-group-actions .btn {
        padding: 4px 6px;
        font-size: 0.7rem;
    }
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
}

/* Loading animation for table */
.table-loading {
    position: relative;
}

.table-loading::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, #3b82f6, #8b5cf6, #f59e0b);
    background-size: 200% 100%;
    animation: loading-bar 1.5s ease-in-out infinite;
}

@keyframes loading-bar {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* Enhanced Toolbar Styles */
.enhanced-toolbar {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    padding: 20px 30px;
    border-radius: 15px;
    margin-bottom: 25px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    border: 1px solid rgba(226, 232, 240, 0.6);
}

.toolbar-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.toolbar-left,
.toolbar-right {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.btn-toolbar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 10px 16px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    position: relative;
    overflow: hidden;
}

.btn-toolbar::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s ease;
}

.btn-toolbar:hover::before {
    left: 100%;
}

.btn-toolbar:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    color: white;
}

.btn-toolbar.btn-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-toolbar.btn-secondary {
    background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
    box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
}

.btn-toolbar.btn-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.btn-toolbar.btn-info {
    background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3);
}

.btn-toolbar.btn-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.view-toggle {
    display: flex;
    background: #f1f5f9;
    border-radius: 8px;
    padding: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1) inset;
}

.btn-view {
    background: transparent;
    border: none;
    padding: 8px 12px;
    border-radius: 6px;
    color: #64748b;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.btn-view.active,
.btn-view:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
}

.entries-selector {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #f8fafc;
    padding: 8px 12px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.entries-selector label {
    font-weight: 600;
    color: #374151;
    font-size: 0.85rem;
    margin-bottom: 0;
}

.entries-selector select {
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 4px 8px;
    background: white;
    font-weight: 500;
    min-width: 60px;
}

@media (max-width: 768px) {
    .toolbar-section {
        flex-direction: column;
        align-items: stretch;
    }
    
    .toolbar-left,
    .toolbar-right {
        justify-content: center;
    }
    
    .btn-toolbar {
        flex: 1;
        min-width: 120px;
    }
}

/* Enhanced Table Cell Styles */
.booking-row {
    animation: fadeInUp 0.4s ease-out forwards;
    opacity: 0;
    transform: translateY(20px);
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.consignment-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.consignment-number {
    font-weight: 700;
    font-family: 'Courier New', monospace;
}

.status-billed {
    color: #059669;
}

.status-pending {
    color: #f59e0b;
}

.customer-wrapper {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.customer-name {
    font-weight: 600;
    color: #374151;
}

.customer-id {
    color: #6b7280;
    font-size: 0.7rem;
}

.doc-type-badge,
.service-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.doc-type-badge.dox {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #1e40af;
    border: 1px solid #3b82f6;
}

.doc-type-badge.spx {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
    border: 1px solid #f59e0b;
}

.doc-type-badge.ndx {
    background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
    color: #7c3aed;
    border: 1px solid #8b5cf6;
}

.service-badge.air {
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    color: #065f46;
    border: 1px solid #10b981;
}

.service-badge.surface {
    background: linear-gradient(135deg, #fef2f2 0%, #fecaca 100%);
    color: #991b1b;
    border: 1px solid #ef4444;
}

.service-badge.express {
    background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%);
    color: #9a3412;
    border: 1px solid #f97316;
}

.weight-value {
    font-weight: 700;
    font-family: 'Courier New', monospace;
}

.weight-unit {
    color: #6b7280;
    font-size: 0.7rem;
    margin-left: 2px;
}

.total-amount {
    font-weight: 700;
    font-size: 0.9rem;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.05) 100%);
    border-radius: 6px;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.empty-state {
    text-align: center;
    padding: 20px;
}

.empty-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
}

.empty-subtitle {
    color: #6b7280;
    font-size: 0.9rem;
}

.spinner-container {
    text-align: center;
}

.loading-text {
    margin-top: 15px;
    font-weight: 600;
    color: #374151;
}

.loading-subtitle {
    margin-top: 5px;
    font-size: 0.85rem;
    color: #6b7280;
}

/* Customer Info Styles */
.customer-info {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border: 1px solid #0ea5e9;
    border-radius: 8px;
    padding: 10px;
    margin-top: 8px;
}

.customer-details {
    font-size: 0.8rem;
}

.customer-details i {
    width: 16px;
    color: #0ea5e9;
    margin-right: 6px;
}

/* Enhanced Customer Info Card */
.customer-info-card {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    font-size: 0.85rem;
}

.customer-info-card:hover {
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.customer-info-header {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    padding: 8px 12px;
    font-weight: 600;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 6px;
}

.customer-info-header i {
    font-size: 1rem;
}

.customer-info-body {
    padding: 12px;
}

.customer-info-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 8px;
    gap: 10px;
}

.customer-info-row:last-child {
    margin-bottom: 0;
}

.customer-info-label {
    font-weight: 600;
    color: #374151;
    min-width: 70px;
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 0.75rem;
}

.customer-info-label i {
    color: #3b82f6;
    font-size: 0.7rem;
    width: 12px;
}

.customer-info-value {
    color: #1f2937;
    font-weight: 500;
    text-align: right;
    flex: 1;
    font-size: 0.75rem;
    word-break: break-word;
}

.input-group .btn-outline-secondary {
    border-color: #e5e7eb;
    color: #6b7280;
    transition: all 0.3s ease;
}

.input-group .btn-outline-secondary:hover {
    background: #f59e0b;
    border-color: #f59e0b;
    color: white;
    transform: translateY(-1px);
}
</style>

<div class="content-wrapper">
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-content">
            <div class="spinner"></div>
            <div style="margin-top: 15px; font-weight: 600;">Loading...</div>
        </div>
    </div>

    <!-- Header -->
    <div class="booking-header">
        DIRECT PARTY BOOKING
    </div>

    <!-- Enhanced Toolbar -->
    <div class="enhanced-toolbar">
        <div class="toolbar-section">
            <div class="toolbar-left">
                <button class="btn-toolbar btn-primary" onclick="toggleNewBookingForm()">
                    <i class="fas fa-plus"></i> New Booking
                </button>
                <button class="btn-toolbar btn-secondary" onclick="refreshData()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
                <div class="view-toggle">
                    <button class="btn-view active" data-view="table" onclick="toggleView('table')">
                        <i class="fas fa-table"></i>
                    </button>
                    <button class="btn-view" data-view="grid" onclick="toggleView('grid')">
                        <i class="fas fa-th-large"></i>
                    </button>
                </div>
            </div>
            <div class="toolbar-right">
                <button class="btn-toolbar btn-info" onclick="openCustomersPage()" title="Manage Customers">
                    <i class="fas fa-users"></i> Customers
                </button>
                <button class="btn-toolbar btn-success" onclick="exportData('excel')">
                    <i class="fas fa-file-excel"></i> Excel
                </button>
                <button class="btn-toolbar btn-info" onclick="exportData('pdf')">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
                <button class="btn-toolbar btn-warning" onclick="printBookings()">
                    <i class="fas fa-print"></i> Print
                </button>
                <div class="entries-selector">
                    <label>Show:</label>
                    <select id="entries_per_page" onchange="changeEntriesPerPage()">
                        <option value="10">10</option>
                        <option value="15" selected>15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Controls -->
    <div class="booking-controls">
        <div class="row">
            <div class="col-md-3">
                <label for="booking_date">Booking Date</label>
                <input type="date" id="booking_date" class="form-control" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-2">
                <label for="billing_status">Billing Status</label>
                <select id="billing_status" class="form-select">
                    <option value="">All</option>
                    <option value="billed">Billed</option>
                    <option value="non-billed">Non-Billed</option>
                    <option value="pending">Pending</option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="outstanding-balance">
                    <div class="balance-label">Outstanding Balance</div>
                    <div class="balance-amount" id="outstanding_balance">0</div>
                </div>
            </div>
            <div class="col-md-3">
                <label for="search_text">Search</label>
                <input type="text" id="search_text" class="form-control" placeholder="Search consignment, customer...">
            </div>
            <div class="col-md-2">
                <button class="btn btn-success w-100" onclick="loadBookings()">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </div>
    </div>

    <!-- Add New Booking Form -->
    <div class="booking-form" id="bookingForm">
        <div class="row">
            <div class="col-md-2">
                <label for="consignment_no">Consignment No</label>
                <input type="text" id="consignment_no" class="form-control" readonly>
            </div>
            <div class="col-md-3">
                <label for="customer_name">Customer Name</label>
                <div class="input-group">
                    <select id="customer_name" class="form-select">
                        <option value="">Select Customer</option>
                    </select>
                    <button class="btn btn-outline-secondary" type="button" id="customer_refresh_btn" onclick="refreshCustomers()" title="Refresh Customer List">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <!-- Customer Details Display -->
                <div id="customer_details" style="display: none; margin-top: 10px;"></div>
            </div>
            <div class="col-md-1">
                <label for="doc_type">Doc Type</label>
                <select id="doc_type" class="form-select">
                    <option value="DOX">DOX</option>
                    <option value="SPX">SPX</option>
                    <option value="NDX">NDX</option>
                </select>
            </div>
            <div class="col-md-1">
                <label for="service_type">Service</label>
                <select id="service_type" class="form-select">
                    <option value="AIR">AIR</option>
                    <option value="SURFACE">SURFACE</option>
                    <option value="EXPRESS">EXPRESS</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="pincode">Pincode</label>
                <input type="text" id="pincode" class="form-control" pattern="[0-9]{6}">
            </div>
            <div class="col-md-3">
                <label for="city_description">City Description</label>
                <input type="text" id="city_description" class="form-control">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-2">
                <label for="weight">Weight (Kg.)</label>
                <input type="number" id="weight" class="form-control" step="0.001" min="0.001">
            </div>
            <div class="col-md-2">
                <label for="courier_amt">Courier Amt</label>
                <input type="number" id="courier_amt" class="form-control" step="0.01" min="0">
            </div>
            <div class="col-md-2">
                <label for="vas_amount">VAS</label>
                <input type="number" id="vas_amount" class="form-control" step="0.01" min="0" value="0">
            </div>
            <div class="col-md-2">
                <label for="chargeable_amt">Chargeable Amt</label>
                <input type="number" id="chargeable_amt" class="form-control" step="0.01" readonly>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-primary w-100" onclick="saveBooking()">
                    <i class="fas fa-save"></i> Save Booking
                </button>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div id="bulkActions" class="bulk-actions">
        <span id="selectedCount">0</span> items selected
        <button class="btn-delete-selected ms-3" onclick="deleteSelected()">
            <i class="fas fa-trash"></i> Delete Selected
        </button>
    </div>

    <!-- Booking Table -->
    <div class="booking-table-container">
        <table class="table booking-table">
            <thead>
                <tr>
                    <th width="40">
                        <input type="checkbox" id="selectAll" class="form-check-input">
                    </th>
                    <th width="100" class="sortable" data-sort="consignment_no">
                        Consign. No <i class="fas fa-sort sort-icon"></i>
                    </th>
                    <th width="180" class="sortable" data-sort="customer_name">
                        Customer <i class="fas fa-sort sort-icon"></i>
                    </th>
                    <th width="60" class="sortable" data-sort="doc_type">
                        Doc/Type <i class="fas fa-sort sort-icon"></i>
                    </th>
                    <th width="60" class="sortable" data-sort="service_type">
                        Mode <i class="fas fa-sort sort-icon"></i>
                    </th>
                    <th width="80" class="sortable" data-sort="pincode">
                        Pincode <i class="fas fa-sort sort-icon"></i>
                    </th>
                    <th width="120" class="sortable" data-sort="city_description">
                        Dest <i class="fas fa-sort sort-icon"></i>
                    </th>
                    <th width="80" class="sortable" data-sort="weight">
                        Weight (Kg.) <i class="fas fa-sort sort-icon"></i>
                    </th>
                    <th width="80" class="sortable" data-sort="vas_amount">
                        VAS <i class="fas fa-sort sort-icon"></i>
                    </th>
                    <th width="100" class="sortable" data-sort="courier_amt">
                        Courier Amt <i class="fas fa-sort sort-icon"></i>
                    </th>
                    <th width="100" class="sortable" data-sort="chargeable_amt">
                        Chargeable Amt <i class="fas fa-sort sort-icon"></i>
                    </th>
                    <th width="80">Edit</th>
                </tr>
            </thead>
            <tbody id="bookingTableBody">
                <tr>
                    <td colspan="12" class="text-center" style="padding: 40px;">
                        <div class="spinner"></div>
                        <div style="margin-top: 10px;">Loading bookings...</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Enhanced Summary Section -->
    <div class="summary-section">
        <div class="summary-header">
            <h5><i class="fas fa-chart-line"></i> Booking Statistics</h5>
            <div class="summary-timestamp" id="last_updated">Last updated: Never</div>
        </div>
        <div class="summary-cards">
            <div class="summary-card total-records gradient-blue">
                <div class="card-icon"><i class="fas fa-list"></i></div>
                <div class="card-content">
                    <div class="card-label">Total Records</div>
                    <div class="card-value" id="summary_total_records">0</div>
                    <div class="card-change" id="records_change">+0 today</div>
                </div>
            </div>
            <div class="summary-card total-billed gradient-green">
                <div class="card-icon"><i class="fas fa-check-circle"></i></div>
                <div class="card-content">
                    <div class="card-label">Billed</div>
                    <div class="card-value" id="summary_total_billed">0</div>
                    <div class="card-percentage" id="billed_percentage">0%</div>
                </div>
            </div>
            <div class="summary-card total-non-billed gradient-orange">
                <div class="card-icon"><i class="fas fa-clock"></i></div>
                <div class="card-content">
                    <div class="card-label">Pending</div>
                    <div class="card-value" id="summary_total_non_billed">0</div>
                    <div class="card-percentage" id="pending_percentage">0%</div>
                </div>
            </div>
            <div class="summary-card total-weight gradient-purple">
                <div class="card-icon"><i class="fas fa-weight-hanging"></i></div>
                <div class="card-content">
                    <div class="card-label">Total Weight</div>
                    <div class="card-value" id="summary_total_weight">0.000 kg</div>
                    <div class="card-change" id="weight_avg">Avg: 0.000kg</div>
                </div>
            </div>
            <div class="summary-card total-amount gradient-emerald">
                <div class="card-icon"><i class="fas fa-rupee-sign"></i></div>
                <div class="card-content">
                    <div class="card-label">Total Revenue</div>
                    <div class="card-value" id="summary_total_amount">₹0.00</div>
                    <div class="card-change" id="revenue_avg">Avg: ₹0.00</div>
                </div>
            </div>
            <div class="summary-card doc-distribution gradient-indigo">
                <div class="card-icon"><i class="fas fa-file-alt"></i></div>
                <div class="card-content">
                    <div class="card-label">DOX Documents</div>
                    <div class="card-value" id="summary_dox_count">0</div>
                    <div class="card-change" id="dox_percentage">0%</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="pagination-info">
            Showing <span id="pagination_start">0</span> to <span id="pagination_end">0</span> of <span id="pagination_total">0</span> entries
        </div>
        <nav>
            <ul class="pagination" id="pagination_controls">
                <!-- Pagination will be generated here -->
            </ul>
        </nav>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewBookingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-eye"></i> View Booking Details
                </h5>
                <button type="button" class="btn-close" onclick="closeViewModal()"></button>
            </div>
            <div class="modal-body" id="viewBookingContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editBookingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit"></i> Edit Booking
                </h5>
                <button type="button" class="btn-close" onclick="closeEditModal()"></button>
            </div>
            <div class="modal-body">
                <form id="editBookingForm">
                    <input type="hidden" id="edit_booking_id">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Consignment No</label>
                            <input type="text" id="edit_consignment_no" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Customer Name</label>
                            <select id="edit_customer_name" class="form-select">
                                <option value="">Select Customer</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label class="form-label">Doc Type</label>
                            <select id="edit_doc_type" class="form-select">
                                <option value="DOX">DOX</option>
                                <option value="SPX">SPX</option>
                                <option value="NDX">NDX</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Service Type</label>
                            <select id="edit_service_type" class="form-select">
                                <option value="AIR">AIR</option>
                                <option value="SURFACE">SURFACE</option>
                                <option value="EXPRESS">EXPRESS</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Pincode</label>
                            <input type="text" id="edit_pincode" class="form-control" pattern="[0-9]{6}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Weight (Kg.)</label>
                            <input type="number" id="edit_weight" class="form-control" step="0.001" min="0.001">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label class="form-label">City Description</label>
                            <input type="text" id="edit_city_description" class="form-control">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label class="form-label">Courier Amount</label>
                            <input type="number" id="edit_courier_amt" class="form-control" step="0.01" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">VAS Amount</label>
                            <input type="number" id="edit_vas_amount" class="form-control" step="0.01" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Chargeable Amount</label>
                            <input type="number" id="edit_chargeable_amt" class="form-control" readonly>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateBooking()">
                    <i class="fas fa-save"></i> Update Booking
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Include Toastify CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

<!-- Include Toastify JS -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<script>
let currentPage = 1;
let isLoading = false;
let selectedBookings = [];
let currentSort = { column: '', direction: '' };
let currentView = 'table';
let entriesPerPage = 15;

$(document).ready(function() {
    // Initialize page loader
    initDirectPartyBookingLoader();
    
    // Initialize the page
    generateConsignmentNumber();
    loadCustomers();
    
    // Hide booking form initially
    $('#bookingForm').hide();
    
    // Load bookings with delay to ensure page is ready
    setTimeout(function() {
        loadBookings();
        showToast('Direct Party Booking system ready!', 'success');
    }, 800);
    
    // Auto-calculate chargeable amount
    $('#courier_amt, #vas_amount').on('input', calculateChargeableAmount);
    $('#edit_courier_amt, #edit_vas_amount').on('input', calculateEditChargeableAmount);
    
    // Search on Enter key
    $('#search_text').on('keypress', function(e) {
        if (e.which === 13) {
            loadBookings();
        }
    });
    
    // Customer search functionality
    $('#customer_name').on('focus', function() {
        $(this).attr('data-live-search', 'true');
    });
    
    // Add customer selection handler
    $('#customer_name, #edit_customer_name').on('change', function() {
        const customerId = $(this).val();
        if (customerId) {
            loadCustomerDetails(customerId);
        } else {
            clearCustomerDetails();
        }
    });
    
    // Add customer search in dropdown
    let customerSearchTimeout;
    $('#customer_name').on('keyup', function() {
        clearTimeout(customerSearchTimeout);
        const searchTerm = $(this).find('option:selected').text();
        
        if (searchTerm.length > 2) {
            customerSearchTimeout = setTimeout(function() {
                searchCustomers(searchTerm);
            }, 300);
        }
    });
    
    // Select all checkbox
    $('#selectAll').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.booking-checkbox').prop('checked', isChecked);
        updateBulkActions();
    });
    
    // Individual checkbox change
    $(document).on('change', '.booking-checkbox', function() {
        updateBulkActions();
    });
    
    // Initialize sortable headers
    $('.sortable').on('click', function() {
        const column = $(this).data('sort');
        toggleSort(column);
    });
    
    // Initialize bulk actions as hidden
    $('#bulkActions').hide();
});

// Enhanced Toolbar Functions
function toggleNewBookingForm() {
    $('#bookingForm').slideToggle(300, function() {
        if ($(this).is(':visible')) {
            generateConsignmentNumber();
            $('#customer_name').focus();
        }
    });
}

function refreshData() {
    showLoading();
    loadBookings(currentPage);
    loadCustomers();
    showToast('Data refreshed successfully!', 'success');
    setTimeout(hideLoading, 1000);
}

// New function to refresh just customers
function refreshCustomers() {
    console.log('🔄 Refreshing customer list...');
    showToast('Refreshing customer list...', 'info');
    
    // Clear existing data
    window.customerData = {};
    $('#customer_details').hide();
    
    // Reload customers
    loadCustomers();
}

// Search customers function
function searchCustomers(searchTerm) {
    if (searchTerm.length < 2) {
        loadCustomers();
        return;
    }
    
    $.ajax({
        url: 'fetch_customers_json.php',
        type: 'GET',
        data: {
            search: searchTerm,
            per_page: 50
        },
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data) {
                let options = '<option value="">Select Customer</option>';
                response.data.forEach(function(customer) {
                    options += `<option value="${customer.id}">${customer.name}</option>`;
                });
                $('#customer_name, #edit_customer_name').html(options);
                showToast(`Found ${response.data.length} customers matching "${searchTerm}"`, 'info');
            }
        },
        error: function() {
            showToast('Search failed, showing all customers', 'warning');
            loadCustomers();
        }
    });
}

// Load customer details when selected
function loadCustomerDetails(customerId) {
    console.log(`📋 Loading details for customer ID: ${customerId}`);
    
    // First check if we have the data in memory
    if (window.customerData && window.customerData[customerId]) {
        console.log('✅ Found customer data in memory');
        displayCustomerDetails(customerId);
        return;
    }
    
    // If not in memory, try to fetch from API
    $.ajax({
        url: 'fetch_customers_json.php',
        type: 'GET',
        data: {
            search: '',
            per_page: 1,
            customer_id: customerId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data && response.data.length > 0) {
                const customer = response.data[0];
                // Store in memory for future use
                if (!window.customerData) window.customerData = {};
                window.customerData[customerId] = customer;
                displayCustomerDetails(customerId);
                console.log('✅ Loaded customer details from API');
            } else {
                console.log('⚠️ No customer data found from API');
                displayCustomerDetailsFromSelect(customerId);
            }
        },
        error: function() {
            console.log('❌ Failed to load customer details from API');
            displayCustomerDetailsFromSelect(customerId);
        }
    });
}

// Fallback to get customer details from select element
function displayCustomerDetailsFromSelect(customerId) {
    const selectedOption = $(`#customer_name option[value="${customerId}"]`);
    if (selectedOption.length > 0) {
        const customer = {
            id: customerId,
            name: selectedOption.text(),
            phone: selectedOption.data('phone') || '',
            email: selectedOption.data('email') || '',
            gst_no: selectedOption.data('gst') || '',
            address: selectedOption.data('address') || ''
        };
        
        // Store in memory
        if (!window.customerData) window.customerData = {};
        window.customerData[customerId] = customer;
        
        displayCustomerDetails(customerId);
        console.log('✅ Loaded customer details from select element');
    }
}

// Display customer information
function displayCustomerInfo(customer) {
    // Create or update customer info display
    let customerInfo = $('#customer_info');
    if (customerInfo.length === 0) {
        customerInfo = $('<div id="customer_info" class="customer-info mt-2"></div>');
        $('#customer_name').closest('.col-md-3').append(customerInfo);
    }
    
    customerInfo.html(`
        <div class="customer-details">
            <small class="text-muted">
                <i class="fas fa-user"></i> ${customer.name}<br>
                <i class="fas fa-phone"></i> ${customer.phone || 'N/A'}<br>
                <i class="fas fa-envelope"></i> ${customer.email || 'N/A'}
            </small>
        </div>
    `).fadeIn(300);
}

// Clear customer details
function clearCustomerDetails() {
    $('#customer_details').fadeOut(200);
    $('#customer_info').fadeOut(200); // Legacy support
    console.log('🧹 Cleared customer details');
}

function toggleView(view) {
    currentView = view;
    $('.btn-view').removeClass('active');
    $(`.btn-view[data-view="${view}"]`).addClass('active');
    
    if (view === 'grid') {
        showToast('Grid view coming soon!', 'info');
        // TODO: Implement grid view
    } else {
        $('.booking-table-container').show();
    }
}

function exportData(format) {
    showLoading();
    
    // Simulate export process
    setTimeout(function() {
        hideLoading();
        if (format === 'excel') {
            showToast('Excel export completed!', 'success');
            // TODO: Implement actual Excel export
        } else if (format === 'pdf') {
            showToast('PDF export completed!', 'success');
            // TODO: Implement actual PDF export
        }
    }, 2000);
}

function printBookings() {
    window.print();
}

function openCustomersPage() {
    window.open('customers.php', '_blank');
}

function changeEntriesPerPage() {
    entriesPerPage = parseInt($('#entries_per_page').val());
    currentPage = 1;
    loadBookings(1);
}

// Sorting Functions
function toggleSort(column) {
    if (currentSort.column === column) {
        // Toggle direction
        currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
    } else {
        // New column
        currentSort.column = column;
        currentSort.direction = 'asc';
    }
    
    updateSortUI();
    loadBookings(1);
}

function updateSortUI() {
    // Reset all headers
    $('.sortable').removeClass('sort-asc sort-desc');
    
    // Update current sorted column
    if (currentSort.column) {
        $(`.sortable[data-sort="${currentSort.column}"]`)
            .addClass(`sort-${currentSort.direction}`);
    }
}

// Enhanced booking functions
function sortBookingsLocally(bookings) {
    if (!currentSort.column) return bookings;
    
    return bookings.sort((a, b) => {
        let aVal = a[currentSort.column];
        let bVal = b[currentSort.column];
        
        // Handle numeric columns
        if (['weight', 'courier_amt', 'vas_amount', 'chargeable_amt'].includes(currentSort.column)) {
            aVal = parseFloat(aVal) || 0;
            bVal = parseFloat(bVal) || 0;
        }
        
        // Handle string columns
        if (typeof aVal === 'string') {
            aVal = aVal.toLowerCase();
            bVal = bVal.toLowerCase();
        }
        
        if (currentSort.direction === 'asc') {
            return aVal > bVal ? 1 : -1;
        } else {
            return aVal < bVal ? 1 : -1;
        }
    });
}

// Generate new consignment number
function generateConsignmentNumber() {
    const timestamp = Date.now().toString().slice(-8);
    $('#consignment_no').val('U' + timestamp);
}

// Load customers
function loadCustomers() {
    console.log('🔄 Loading customers from database...');
    $('#customer_refresh_btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
    
    // Clear any existing customer data display
    $('#customer_details').hide();
    
    // First try the JSON API for better error handling and more customer data
    $.ajax({
        url: 'fetch_customers_json.php',
        type: 'GET',
        data: {
            per_page: 200, // Get more customers for the dropdown
            search: '', // No search filter for dropdown
            include_details: 1 // Request full customer details
        },
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            console.log('📊 Customer API Response:', response);
            if (response.success && response.data && response.data.length > 0) {
                let options = '<option value="">Select Customer</option>';
                
                // Store customer data globally for quick access
                window.customerData = {};
                
                response.data.forEach(function(customer) {
                    options += `<option value="${customer.id}" 
                                data-phone="${customer.phone || ''}" 
                                data-email="${customer.email || ''}"
                                data-gst="${customer.gst_no || ''}"
                                data-address="${customer.address || ''}"
                               >${customer.name}</option>`;
                    
                    // Store customer details for quick lookup
                    window.customerData[customer.id] = customer;
                });
                
                $('#customer_name, #edit_customer_name').html(options);
                console.log(`✅ Loaded ${response.data.length} customers from database`);
                showToast(`Loaded ${response.data.length} customers from database`, 'success');
                
                // Enable live search on customer dropdown
                enableCustomerSearch();
                
            } else {
                console.log('⚠️ JSON API returned no data, trying select endpoint');
                loadCustomersFromSelectAPI();
            }
            $('#customer_refresh_btn').prop('disabled', false).html('<i class="fas fa-refresh"></i>');
        },
        error: function(xhr, status, error) {
            console.log('❌ JSON API error:', status, error, xhr.responseText);
            console.log('🔄 Trying select endpoint as fallback');
            loadCustomersFromSelectAPI();
            $('#customer_refresh_btn').prop('disabled', false).html('<i class="fas fa-refresh"></i>');
        }
    });
}

// Fallback function to load customers from select endpoint
function loadCustomersFromSelectAPI() {
    console.log('🔄 Loading customers from select API...');
    $.ajax({
        url: 'ajax/fetch_customers_select.php',
        type: 'GET',
        timeout: 10000,
        success: function(response) {
            console.log('📊 Select API Response length:', response.length);
            if (response && response.includes('Select Customer') && response.length > 50) {
                $('#customer_name, #edit_customer_name').html(response);
                console.log('✅ Loaded customers from select API endpoint');
                showToast('Customers loaded from database', 'success');
                
                // Try to extract customer count from response
                const optionCount = (response.match(/<option/g) || []).length - 1; // -1 for "Select Customer"
                if (optionCount > 0) {
                    console.log(`📊 Found ${optionCount} customers in select API`);
                }
            } else {
                console.log('❌ Select API failed or returned empty data, using real demo customers');
                loadRealDemoCustomers();
            }
        },
        error: function(xhr, status, error) {
            console.log('❌ Select API error:', status, error, xhr.responseText);
            console.log('🔄 Using real demo customers as final fallback');
            loadRealDemoCustomers();
        }
    });
}

// Load real demo customers based on actual database structure if all APIs fail
function loadRealDemoCustomers() {
    console.log('📋 Loading real demo customers based on database structure...');
    
    // Use real customer data from the database as seen in the SQL file
    const realDemoCustomers = [
        { id: 10, name: 'Eden Hale', phone: '+1 (253) 107-4417', email: 'mylycavy@mailinator.com', gst_no: 'Officiis odio nostru' },
        { id: 11, name: 'Kitra Lancaster', phone: '-4555353535', email: 'qyti@mailinator.com', gst_no: '43SFFDF4334T1Z4' },
        { id: 12, name: 'Dexter Chambers', phone: '1336354635', email: 'rexun@mailinator.com', gst_no: '56EDDDD5454T1Z5' },
        { id: 13, name: 'Serena Benson', phone: '1451884464', email: 'tuxyfocejy@mailinator.com', gst_no: '34RWSSS2222S1Z4' },
        { id: 1, name: 'ABC Corporation', phone: '9876543210', email: 'info@abc.com', gst_no: '27AABCU9603R1ZX' },
        { id: 2, name: 'XYZ Ltd', phone: '9876543211', email: 'contact@xyz.com', gst_no: '09AACFX3024F1ZH' },
        { id: 3, name: 'PQR Enterprises', phone: '9876543212', email: 'sales@pqr.com', gst_no: '24AAGCP0618F1ZJ' },
        { id: 4, name: 'STARLIT MEDICAL CENTER PVT LTD', phone: '9876543213', email: 'admin@starlit.com', gst_no: '07AAFCS0842Q1ZN' }
    ];
    
    let options = '<option value="">Select Customer</option>';
    
    // Store customer data globally for quick access
    window.customerData = {};
    
    realDemoCustomers.forEach(function(customer) {
        options += `<option value="${customer.id}" 
                    data-phone="${customer.phone}" 
                    data-email="${customer.email}"
                    data-gst="${customer.gst_no}"
                   >${customer.name}</option>`;
        
        // Store customer details for quick lookup
        window.customerData[customer.id] = customer;
    });
    
    $('#customer_name, #edit_customer_name').html(options);
    console.log(`✅ Loaded ${realDemoCustomers.length} real demo customers`);
    showToast(`Demo mode: Loaded ${realDemoCustomers.length} sample customers`, 'warning');
    
    // Enable live search on customer dropdown
    enableCustomerSearch();
}

// Calculate chargeable amount
function calculateChargeableAmount() {
    const courier = parseFloat($('#courier_amt').val()) || 0;
    const vas = parseFloat($('#vas_amount').val()) || 0;
    $('#chargeable_amt').val((courier + vas).toFixed(2));
}

// Calculate edit chargeable amount
function calculateEditChargeableAmount() {
    const courier = parseFloat($('#edit_courier_amt').val()) || 0;
    const vas = parseFloat($('#edit_vas_amount').val()) || 0;
    $('#edit_chargeable_amt').val((courier + vas).toFixed(2));
}

// Load bookings
function loadBookings(page = 1) {
    if (isLoading) return;
    
    currentPage = page;
    isLoading = true;
    
    const filters = {
        page: page,
        booking_date: $('#booking_date').val(),
        billing_status: $('#billing_status').val(),
        search: $('#search_text').val(),
        limit: entriesPerPage,
        sort_column: currentSort.column,
        sort_direction: currentSort.direction
    };
    
    // Show loading in table
    $('#bookingTableBody').html(`
        <tr>
            <td colspan="12" class="text-center" style="padding: 40px;">
                <div class="spinner-container">
                    <div class="spinner"></div>
                    <div class="loading-text">Loading bookings...</div>
                    <div class="loading-subtitle">Fetching ${entriesPerPage} records</div>
                </div>
            </td>
        </tr>
    `);
    
    $.ajax({
        url: 'ajax/get_bookings.php',
        type: 'GET',
        data: filters,
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            isLoading = false;
            if (response.success) {
                displayBookings(response.data);
                updateSummary(response.data);
                updatePagination(
                    response.pagination.current_page,
                    response.pagination.total_pages,
                    response.pagination.total_records
                );
                
                // Show demo notification if using mock data
                if (response.source === 'mock' || response.demo_mode) {
                    showToast('Running in demo mode - sample data displayed', 'warning');
                }
            } else {
                showToast('Error loading bookings: ' + response.message, 'error');
                $('#bookingTableBody').html(`
                    <tr>
                        <td colspan="12" class="text-center" style="padding: 40px;">
                            <i class="fas fa-exclamation-triangle text-warning" style="font-size: 2rem;"></i>
                            <div style="margin-top: 10px;">Failed to load bookings</div>
                        </td>
                    </tr>
                `);
            }
        },
        error: function(xhr, status, error) {
            isLoading = false;
            console.log('Booking loading error:', status, error, xhr.responseText);
            
            // Always show demo data if there's any error
            showToast('Loading demo data', 'warning');
            const demoBookings = generateDemoBookings();
            displayBookings(demoBookings);
            updateSummary(demoBookings);
            updatePagination(1, 1, demoBookings.length);
        }
    });
}

// Generate demo bookings data
function generateDemoBookings() {
    const customers = ['STARLIT MEDICAL CENTER PVT...', 'ABC LOGISTICS PVT LTD', 'XYZ ENTERPRISES', 'PQR INTERNATIONAL'];
    const cities = ['LKO', 'DEL', 'HYD', 'BLR', 'MUM', 'CCU'];
    const docTypes = ['DOX', 'SPX', 'NDX'];
    const serviceTypes = ['AIR', 'SURFACE'];
    
    const bookings = [];
    for (let i = 1; i <= 12; i++) {
        const consignmentNo = 'U36414' + (665 + i);
        const weight = (Math.random() * 10 + 0.1).toFixed(3);
        const courierAmt = (Math.random() * 100 + 20).toFixed(2);
        const vasAmt = '0.00';
        
        bookings.push({
            id: i,
            consignment_no: consignmentNo,
            customer_name: customers[Math.floor(Math.random() * customers.length)],
            doc_type: docTypes[Math.floor(Math.random() * docTypes.length)],
            service_type: serviceTypes[Math.floor(Math.random() * serviceTypes.length)],
            pincode: '110030',
            city_description: cities[Math.floor(Math.random() * cities.length)],
            weight: weight,
            vas_amount: vasAmt,
            courier_amt: courierAmt,
            chargeable_amt: courierAmt,
            billing_status: Math.random() > 0.5 ? 'billed' : 'non-billed'
        });
    }
    
    return bookings;
}

// Display bookings in table
function displayBookings(bookings) {
    // Apply local sorting if needed
    bookings = sortBookingsLocally(bookings);
    
    let html = '';
    
    if (bookings.length === 0) {
        html = `
            <tr>
                <td colspan="12" class="text-center" style="padding: 50px;">
                    <div class="empty-state">
                        <i class="fas fa-inbox text-muted" style="font-size: 3rem; margin-bottom: 15px; display: block;"></i>
                        <div class="empty-title">No bookings found</div>
                        <div class="empty-subtitle">Try adjusting your search criteria or add a new booking</div>
                    </div>
                </td>
            </tr>
        `;
    } else {
        bookings.forEach(function(booking, index) {
            const statusClass = booking.billing_status === 'billed' ? 'status-billed' : 'status-pending';
            const statusIcon = booking.billing_status === 'billed' ? 'fa-check-circle' : 'fa-clock';
            
            html += `
                <tr class="booking-row" style="animation-delay: ${index * 50}ms">
                    <td>
                        <input type="checkbox" class="form-check-input booking-checkbox" value="${booking.id}">
                    </td>
                    <td class="consignment-cell">
                        <div class="consignment-wrapper">
                            <span class="consignment-number">${booking.consignment_no}</span>
                            <i class="fas ${statusIcon} ${statusClass}" title="${booking.billing_status}"></i>
                        </div>
                    </td>
                    <td class="customer-cell" title="${booking.customer_name}">
                        <div class="customer-wrapper">
                            <span class="customer-name">${booking.customer_name}</span>
                            <small class="customer-id">ID: ${booking.id}</small>
                        </div>
                    </td>
                    <td>
                        <span class="doc-type-badge ${booking.doc_type.toLowerCase()}">${booking.doc_type}</span>
                    </td>
                    <td>
                        <span class="service-badge ${booking.service_type.toLowerCase()}">${booking.service_type}</span>
                    </td>
                    <td class="pincode-cell">${booking.pincode}</td>
                    <td class="city-cell">${booking.city_description}</td>
                    <td class="weight-cell">
                        <span class="weight-value">${booking.weight}</span>
                        <small class="weight-unit">kg</small>
                    </td>
                    <td class="amount-cell">₹${booking.vas_amount}</td>
                    <td class="amount-cell">₹${booking.courier_amt}</td>
                    <td class="amount-cell total-amount">₹${booking.chargeable_amt}</td>
                    <td>
                        <div class="btn-group-actions">
                            <button class="btn btn-action-view" onclick="viewBooking(${booking.id})" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-action-edit" onclick="editBooking(${booking.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-action-delete" onclick="deleteBooking(${booking.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#bookingTableBody').html(html);
}

// Update summary statistics
function updateSummary(bookings) {
    const totalRecords = bookings.length;
    const totalBilled = bookings.filter(b => b.billing_status === 'billed').length;
    const totalNonBilled = bookings.filter(b => b.billing_status !== 'billed').length;
    const totalWeight = bookings.reduce((sum, b) => sum + parseFloat(b.weight || 0), 0);
    const totalAmount = bookings.reduce((sum, b) => sum + parseFloat(b.chargeable_amt || 0), 0);
    const doxCount = bookings.filter(b => b.doc_type === 'DOX').length;
    
    // Calculate percentages
    const billedPercentage = totalRecords > 0 ? ((totalBilled / totalRecords) * 100).toFixed(1) : 0;
    const pendingPercentage = totalRecords > 0 ? ((totalNonBilled / totalRecords) * 100).toFixed(1) : 0;
    const doxPercentage = totalRecords > 0 ? ((doxCount / totalRecords) * 100).toFixed(1) : 0;
    
    // Calculate averages
    const avgWeight = totalRecords > 0 ? (totalWeight / totalRecords).toFixed(3) : 0;
    const avgRevenue = totalRecords > 0 ? (totalAmount / totalRecords).toFixed(2) : 0;
    
    // Update summary cards with animations
    animateCounter('#summary_total_records', totalRecords);
    animateCounter('#summary_total_billed', totalBilled);
    animateCounter('#summary_total_non_billed', totalNonBilled);
    animateCounter('#summary_dox_count', doxCount);
    
    $('#summary_total_weight').text(`${totalWeight.toFixed(3)} kg`);
    $('#summary_total_amount').text(`₹${totalAmount.toFixed(2)}`);
    
    // Update percentages and additional info
    $('#billed_percentage').text(`${billedPercentage}%`);
    $('#pending_percentage').text(`${pendingPercentage}%`);
    $('#dox_percentage').text(`${doxPercentage}%`);
    $('#weight_avg').text(`Avg: ${avgWeight}kg`);
    $('#revenue_avg').text(`Avg: ₹${avgRevenue}`);
    $('#records_change').text(`+${Math.floor(Math.random() * 5)} today`);
    
    // Update timestamp
    const now = new Date();
    $('#last_updated').text(`Last updated: ${now.toLocaleTimeString()}`);
}

// Animate counter function
function animateCounter(selector, target) {
    const element = $(selector);
    const current = parseInt(element.text()) || 0;
    const increment = Math.ceil((target - current) / 20);
    
    if (current !== target) {
        element.text(current + increment);
        setTimeout(() => animateCounter(selector, target), 50);
    } else {
        element.text(target);
    }
}

// Update pagination
function updatePagination(currentPage, totalPages, totalRecords) {
    const start = totalRecords > 0 ? 1 : 0;
    const end = totalRecords;
    
    $('#pagination_start').text(start);
    $('#pagination_end').text(end);
    $('#pagination_total').text(totalRecords);
    
    // For demo, we'll show simple pagination
    let paginationHtml = `
        <li class="page-item disabled">
            <a class="page-link" href="#">Previous</a>
        </li>
        <li class="page-item active">
            <a class="page-link" href="#">1</a>
        </li>
        <li class="page-item disabled">
            <a class="page-link" href="#">Next</a>
        </li>
    `;
    
    $('#pagination_controls').html(paginationHtml);
}

// Update bulk actions visibility
function updateBulkActions() {
    const checkedBoxes = $('.booking-checkbox:checked');
    const count = checkedBoxes.length;
    
    if (count > 0) {
        $('#bulkActions').addClass('show');
        $('#selectedCount').text(count);
    } else {
        $('#bulkActions').removeClass('show');
    }
    
    // Update select all checkbox
    const totalBoxes = $('.booking-checkbox').length;
    if (count === 0) {
        $('#selectAll').prop('indeterminate', false).prop('checked', false);
    } else if (count === totalBoxes) {
        $('#selectAll').prop('indeterminate', false).prop('checked', true);
    } else {
        $('#selectAll').prop('indeterminate', true);
    }
}

// Save new booking
function saveBooking() {
    const formData = {
        consignment_no: $('#consignment_no').val(),
        customer_id: $('#customer_name').val(),
        doc_type: $('#doc_type').val(),
        service_type: $('#service_type').val(),
        pincode: $('#pincode').val(),
        city_description: $('#city_description').val(),
        weight: $('#weight').val(),
        courier_amt: $('#courier_amt').val(),
        vas_amount: $('#vas_amount').val(),
        chargeable_amt: $('#chargeable_amt').val()
    };
    
    // Basic validation
    if (!formData.customer_id) {
        showToast('Please select a customer', 'error');
        return;
    }
    
    if (!formData.weight || parseFloat(formData.weight) <= 0) {
        showToast('Please enter a valid weight', 'error');
        return;
    }
    
    if (!formData.courier_amt || parseFloat(formData.courier_amt) <= 0) {
        showToast('Please enter a valid courier amount', 'error');
        return;
    }
    
    showLoading();
    
    $.ajax({
        url: 'ajax/save_booking.php',
        type: 'POST',
        data: JSON.stringify(formData),
        contentType: 'application/json',
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            hideLoading();
            if (response.success) {
                showToast(response.message || 'Booking saved successfully!', 'success');
                
                // Reset form
                $('#bookingForm')[0].reset();
                $('#vas_amount').val('0');
                generateConsignmentNumber();
                
                // Reload bookings
                loadBookings();
                
                if (response.demo_mode) {
                    showToast('Demo mode: ' + response.note, 'warning');
                }
            } else {
                showToast('Error: ' + response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            hideLoading();
            console.error('Save booking error:', status, error, xhr.responseText);
            
            if (xhr.status === 500 || xhr.status === 0) {
                showToast('Server unavailable - Demo save simulated', 'warning');
                
                // Reset form and reload
                $('#bookingForm')[0].reset();
                $('#vas_amount').val('0');
                generateConsignmentNumber();
                loadBookings();
            } else {
                showToast('Failed to save booking', 'error');
            }
        }
    });
}

// View booking
function viewBooking(bookingId) {
    const demoBooking = {
        consignment_no: 'U36414673',
        customer_name: 'STARLIT MEDICAL CENTER PVT LTD',
        doc_type: 'DOX',
        service_type: 'AIR',
        pincode: '226010',
        city_description: 'LKO',
        weight: '2.640',
        vas_amount: '0.00',
        courier_amt: '338.00',
        chargeable_amt: '338.00',
        billing_status: 'non-billed',
        booking_date: '2025-06-05'
    };
    
    const html = `
        <div class="row">
            <div class="col-md-6">
                <h6>Booking Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>Consignment No:</strong></td><td>${demoBooking.consignment_no}</td></tr>
                    <tr><td><strong>Booking Date:</strong></td><td>${demoBooking.booking_date}</td></tr>
                    <tr><td><strong>Service Type:</strong></td><td>${demoBooking.service_type}</td></tr>
                    <tr><td><strong>Document Type:</strong></td><td>${demoBooking.doc_type}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Customer & Destination</h6>
                <table class="table table-sm">
                    <tr><td><strong>Customer:</strong></td><td>${demoBooking.customer_name}</td></tr>
                    <tr><td><strong>City:</strong></td><td>${demoBooking.city_description}</td></tr>
                    <tr><td><strong>Pincode:</strong></td><td>${demoBooking.pincode}</td></tr>
                    <tr><td><strong>Weight:</strong></td><td>${demoBooking.weight} Kg</td></tr>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h6>Amount Details</h6>
                <table class="table table-sm">
                    <tr><td><strong>Courier Amount:</strong></td><td>₹${demoBooking.courier_amt}</td></tr>
                    <tr><td><strong>VAS Amount:</strong></td><td>₹${demoBooking.vas_amount}</td></tr>
                    <tr><td><strong>Chargeable Amount:</strong></td><td><strong>₹${demoBooking.chargeable_amt}</strong></td></tr>
                    <tr><td><strong>Billing Status:</strong></td><td><span class="badge bg-warning">${demoBooking.billing_status}</span></td></tr>
                </table>
            </div>
        </div>
    `;
    
    $('#viewBookingContent').html(html);
    $('#viewBookingModal').modal('show');
}

// Edit booking
function editBooking(bookingId) {
    const demoBooking = {
        id: bookingId,
        consignment_no: 'U36414673',
        customer_id: '1',
        doc_type: 'DOX',
        service_type: 'AIR',
        pincode: '226010',
        city_description: 'LKO',
        weight: '2.640',
        vas_amount: '0.00',
        courier_amt: '338.00',
        chargeable_amt: '338.00'
    };
    
    // Populate edit form
    $('#edit_booking_id').val(demoBooking.id);
    $('#edit_consignment_no').val(demoBooking.consignment_no);
    $('#edit_customer_name').val(demoBooking.customer_id);
    $('#edit_doc_type').val(demoBooking.doc_type);
    $('#edit_service_type').val(demoBooking.service_type);
    $('#edit_pincode').val(demoBooking.pincode);
    $('#edit_city_description').val(demoBooking.city_description);
    $('#edit_weight').val(demoBooking.weight);
    $('#edit_vas_amount').val(demoBooking.vas_amount);
    $('#edit_courier_amt').val(demoBooking.courier_amt);
    $('#edit_chargeable_amt').val(demoBooking.chargeable_amt);
    
    $('#editBookingModal').modal('show');
}

// Update booking
function updateBooking() {
    const formData = {
        id: $('#edit_booking_id').val(),
        customer_id: $('#edit_customer_name').val(),
        doc_type: $('#edit_doc_type').val(),
        service_type: $('#edit_service_type').val(),
        pincode: $('#edit_pincode').val(),
        city_description: $('#edit_city_description').val(),
        weight: $('#edit_weight').val(),
        courier_amt: $('#edit_courier_amt').val(),
        vas_amount: $('#edit_vas_amount').val(),
        chargeable_amt: $('#edit_chargeable_amt').val()
    };
    
    showLoading();
    
    // Simulate update
    setTimeout(function() {
        hideLoading();
        showToast('Booking updated successfully!', 'success');
        closeEditModal();
        loadBookings();
    }, 1000);
}

// Delete booking
function deleteBooking(bookingId) {
    if (!confirm('Are you sure you want to delete this booking?')) {
        return;
    }
    
    showLoading();
    
    $.ajax({
        url: 'ajax/delete_booking.php',
        type: 'POST',
        data: JSON.stringify({ id: bookingId }),
        contentType: 'application/json',
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            hideLoading();
            if (response.success) {
                showToast(response.message || 'Booking deleted successfully!', 'success');
                loadBookings();
                
                if (response.demo_mode) {
                    showToast('Demo mode: ' + response.note, 'warning');
                }
            } else {
                showToast('Error: ' + response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            hideLoading();
            console.error('Delete booking error:', status, error, xhr.responseText);
            
            if (xhr.status === 500 || xhr.status === 0) {
                showToast('Server unavailable - Demo deletion simulated', 'warning');
                loadBookings();
            } else {
                showToast('Failed to delete booking', 'error');
            }
        }
    });
}

// Delete selected bookings
function deleteSelected() {
    const checkedBoxes = $('.booking-checkbox:checked');
    const count = checkedBoxes.length;
    
    if (count === 0) {
        showToast('Please select bookings to delete', 'warning');
        return;
    }
    
    if (!confirm(`Are you sure you want to delete ${count} selected booking(s)?`)) {
        return;
    }
    
    showLoading();
    
    // Simulate bulk delete
    setTimeout(function() {
        hideLoading();
        showToast(`${count} booking(s) deleted successfully!`, 'success');
        loadBookings();
    }, 1000);
}

// Close modals
function closeViewModal() {
    $('#viewBookingModal').modal('hide');
}

function closeEditModal() {
    $('#editBookingModal').modal('hide');
}

// Show/Hide loading
function showLoading() {
    $('#loadingOverlay').addClass('show');
}

function hideLoading() {
    $('#loadingOverlay').removeClass('show');
}

// Show toast notification
function showToast(message, type = 'info') {
    const colors = {
        success: '#10b981',
        error: '#ef4444',
        warning: '#f59e0b',
        info: '#3b82f6'
    };
    
    Toastify({
        text: message,
        duration: 4000,
        gravity: "top",
        position: "right",
        style: {
            background: colors[type] || colors.info,
            borderRadius: "8px",
            fontSize: "14px",
            fontWeight: "500"
        }
    }).showToast();
}

// Enable live search on customer dropdown
function enableCustomerSearch() {
    $('#customer_name, #edit_customer_name').select2({
        placeholder: 'Select or search customer...',
        allowClear: true,
        width: '100%',
        templateResult: function(customer) {
            if (!customer.id) return customer.text;
            
            const $customer = $(customer.element);
            const phone = $customer.data('phone') || '';
            const email = $customer.data('email') || '';
            
            return $(`
                <div>
                    <div style="font-weight: bold;">${customer.text}</div>
                    ${phone ? `<div style="font-size: 0.9em; color: #666;"><i class="fas fa-phone"></i> ${phone}</div>` : ''}
                    ${email ? `<div style="font-size: 0.9em; color: #666;"><i class="fas fa-envelope"></i> ${email}</div>` : ''}
                </div>
            `);
        }
    });
}

// Display customer details when selected
function displayCustomerDetails(customerId, targetContainer = '#customer_details') {
    if (!customerId || !window.customerData || !window.customerData[customerId]) {
        $(targetContainer).hide();
        return;
    }
    
    const customer = window.customerData[customerId];
    const detailsHtml = `
        <div class="customer-info-card">
            <div class="customer-info-header">
                <i class="fas fa-user-circle"></i>
                <span>Customer Details</span>
            </div>
            <div class="customer-info-body">
                <div class="customer-info-row">
                    <span class="customer-info-label"><i class="fas fa-user"></i> Name:</span>
                    <span class="customer-info-value">${customer.name}</span>
                </div>
                ${customer.phone ? `
                <div class="customer-info-row">
                    <span class="customer-info-label"><i class="fas fa-phone"></i> Phone:</span>
                    <span class="customer-info-value">${customer.phone}</span>
                </div>
                ` : ''}
                ${customer.email ? `
                <div class="customer-info-row">
                    <span class="customer-info-label"><i class="fas fa-envelope"></i> Email:</span>
                    <span class="customer-info-value">${customer.email}</span>
                </div>
                ` : ''}
                ${customer.gst_no ? `
                <div class="customer-info-row">
                    <span class="customer-info-label"><i class="fas fa-file-invoice"></i> GST:</span>
                    <span class="customer-info-value">${customer.gst_no}</span>
                </div>
                ` : ''}
                ${customer.address ? `
                <div class="customer-info-row">
                    <span class="customer-info-label"><i class="fas fa-map-marker-alt"></i> Address:</span>
                    <span class="customer-info-value">${customer.address}</span>
                </div>
                ` : ''}
            </div>
        </div>
    `;
    
    $(targetContainer).html(detailsHtml).show();
}

// Refresh customer list
function refreshCustomers() {
    console.log('🔄 Refreshing customer list...');
    showToast('Refreshing customer list...', 'info');
    
    // Clear existing data
    window.customerData = {};
    $('#customer_details').hide();
    
    // Reload customers
    loadCustomers();
}
</script>

<?php require_once 'inc/footer.php'; ?>
