<?php
require_once 'inc/config.php';
require_once 'inc/db.php';

echo "Adding sample data...\n";

try {
    // Add sample customers if they don't exist
    $checkCustomers = $conn->query("SELECT COUNT(*) as count FROM customers");
    $customerCount = $checkCustomers->fetch_assoc()['count'];
    
    if ($customerCount == 0) {
        echo "Adding sample customers...\n";
        
        $customers = [
            ['ABC Corporation', 'abc@example.com', '9876543210', '123 Business Street, Mumbai', 'GST123456789'],
            ['XYZ Ltd', 'xyz@example.com', '9876543211', '456 Commercial Road, Delhi', 'GST987654321'],
            ['PQR Enterprises', 'pqr@example.com', '9876543212', '789 Industrial Area, Bangalore', 'GST456789123']
        ];
        
        $stmt = $conn->prepare("INSERT INTO customers (name, email, phone, address, gst_no) VALUES (?, ?, ?, ?, ?)");
        
        foreach ($customers as $customer) {
            $stmt->bind_param("sssss", $customer[0], $customer[1], $customer[2], $customer[3], $customer[4]);
            $stmt->execute();
            echo "Added customer: " . $customer[0] . "\n";
        }
    } else {
        echo "Customers already exist: $customerCount\n";
    }
    
    // Add sample invoices if they don't exist
    $checkInvoices = $conn->query("SELECT COUNT(*) as count FROM invoices");
    $invoiceCount = $checkInvoices->fetch_assoc()['count'];
    
    if ($invoiceCount == 0) {
        echo "Adding sample invoices...\n";
        
        // Get customer IDs
        $result = $conn->query("SELECT id FROM customers LIMIT 3");
        $customerIds = [];
        while ($row = $result->fetch_assoc()) {
            $customerIds[] = $row['id'];
        }
        
        if (count($customerIds) > 0) {
            $invoices = [
                ['INV-20241201-001', $customerIds[0], 'Mumbai', '2024-12-01', '2024-11-01', '2024-11-30', 1000.00, 180.00, 1180.00],
                ['INV-20241201-002', $customerIds[1] ?? $customerIds[0], 'Delhi', '2024-12-01', '2024-11-01', '2024-11-30', 1500.00, 270.00, 1770.00],
                ['INV-20241201-003', $customerIds[2] ?? $customerIds[0], 'Bangalore', '2024-12-01', '2024-11-01', '2024-11-30', 800.00, 144.00, 944.00]
            ];
            
            $stmt = $conn->prepare("INSERT INTO invoices (invoice_no, customer_id, destination, invoice_date, from_date, to_date, total_amount, gst_amount, grand_total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            foreach ($invoices as $invoice) {
                $stmt->bind_param("sissssddd", $invoice[0], $invoice[1], $invoice[2], $invoice[3], $invoice[4], $invoice[5], $invoice[6], $invoice[7], $invoice[8]);
                $stmt->execute();
                echo "Added invoice: " . $invoice[0] . "\n";
            }
        }
    } else {
        echo "Invoices already exist: $invoiceCount\n";
    }
    
    echo "Sample data setup complete!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
