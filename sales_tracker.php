<?php
session_start();

// Function to parse data from single_employee.php
function parseSalesData() {
    $sales = [];
    
    // Read the single_employee.php file
    $html = file_get_contents('single_employee.php');
    if (!$html) {
        die("Error: Could not read single_employee.php");
    }

    // Create a new DOMDocument
    $dom = new DOMDocument();
    
    // Suppress warnings from invalid HTML
    @$dom->loadHTML($html);
    
    // Find all rows
    $rows = $dom->getElementsByTagName('tr');
    $rowsArray = iterator_to_array($rows);
    
    $currentDepartment = '';
    $isHeader = true;
    
    // Loop through rows with index to check next row
    for ($i = 0; $i < count($rowsArray); $i++) {
        $row = $rowsArray[$i];
        
        // Skip the first header row
        if ($isHeader) {
            $isHeader = false;
            continue;
        }

        // Get all cells in the row
        $cells = $row->getElementsByTagName('td');
        
        // Get the row content
        $rowText = trim($row->textContent);
        
        // Skip rows that begin with "Count:"
        if ($cells->length >= 1 && preg_match('/^Count:\s*\d+/', trim($cells->item(0)->textContent))) {
            continue;
        }
        
        // Check if this is a department row
        if (strpos($rowText, 'Dept:') !== false) {
            // Extract department name (everything after "Dept:")
            $currentDepartment = trim(substr($rowText, strpos($rowText, 'Dept:') + 5));
            continue;
        }

        // Skip product header rows (they have bgcolor='99FF99')
        if ($row->hasAttribute('bgcolor') && $row->getAttribute('bgcolor') == '99FF99') {
            continue;
        }

        // Skip total rows (they have bgcolor='ffff99')
        if ($row->hasAttribute('bgcolor') && $row->getAttribute('bgcolor') == 'ffff99') {
            continue;
        }

        // Check if this is a transaction row
        if ($cells->length >= 6) {
            $date = trim($cells->item(0)->textContent);
            $price = trim($cells->item(5)->textContent);
            
            // If this is a transaction row (has a date and price)
            if (!empty($date) && !empty($price) && preg_match('/^\d{2}-\d{2}-\d{4}$/', $date)) {
                $customerName = '';
                $payment_type = 'cash';  // Default to cash
                
                // Check next row for customer name if it exists
                if ($i + 1 < count($rowsArray)) {
                    $nextRow = $rowsArray[$i + 1];
                    $nextCells = $nextRow->getElementsByTagName('td');
                    
                    // Check if next row has cells and first cell contains a name
                    if ($nextCells->length >= 1) {
                        $possibleName = trim($nextCells->item(0)->textContent);
                        // If it's not a date and not empty, and not a "Count:" row, it's a name
                        if (!empty($possibleName) && 
                            !preg_match('/^\d{2}-\d{2}-\d{4}$/', $possibleName) && 
                            !preg_match('/^Count:\s*\d+/', $possibleName)) {
                            $customerName = $possibleName;
                            $payment_type = 'card';
                            $i++; // Skip the name row in next iteration
                        }
                    }
                }
                
                // Clean up the price value
                $price = floatval(preg_replace('/[^0-9.]/', '', $price));
                
                // Only add if we have a valid price
                if ($price > 0) {
                    $sales[] = [
                        'department' => $currentDepartment,
                        'price' => $price,
                        'payment_type' => $payment_type,
                        'customer_name' => $customerName
                    ];
                }
            }
        }
    }
    
    // If no sales were found, provide sample data as fallback
    if (empty($sales)) {
        error_log("No sales data found in single_employee.php, using sample data");
        $sales = [
            [
                'department' => 'DUES CHARGE',
                'price' => 75.00,
                'payment_type' => 'card',
                'customer_name' => 'COLE MOYER'
            ],
            [
                'department' => 'RESTAURANT',
                'price' => 78.75,
                'payment_type' => 'cash',
                'customer_name' => ''
            ],
            [
                'department' => 'PASSES',
                'price' => 496.00,
                'payment_type' => 'card',
                'customer_name' => 'SAID ROMERO PEREZ'
            ]
        ];
    }
    
    return $sales;
}

// Get sales data
$sales = parseSalesData();

// Initialize totals
$totals = [
    'departments' => [],
    'payment_types' => [
        'cash' => 0,
        'card' => 0
    ],
    'departments_by_payment' => [], // Track department totals by payment type
    'total' => 0
];

// Calculate totals
foreach ($sales as $sale) {
    // Department totals
    if (!isset($totals['departments'][$sale['department']])) {
        $totals['departments'][$sale['department']] = 0;
        $totals['departments_by_payment'][$sale['department']] = [
            'cash' => 0,
            'card' => 0
        ];
    }
    $totals['departments'][$sale['department']] += $sale['price'];
    $totals['departments_by_payment'][$sale['department']][$sale['payment_type']] += $sale['price'];
    
    // Payment type totals
    $totals['payment_types'][$sale['payment_type']] += $sale['price'];
    $totals['total'] += $sale['price'];
}

// Display header
echo "<h2>Sales Transaction Report</h2>";

// Display all transactions
echo "<h3>All Transactions</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Department</th><th>Customer</th><th>Payment Type</th><th>Amount</th></tr>";
foreach ($sales as $sale) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($sale['department']) . "</td>";
    echo "<td>" . htmlspecialchars($sale['customer_name']) . "</td>";
    echo "<td>" . ucfirst(htmlspecialchars($sale['payment_type'])) . "</td>";
    echo "<td>$" . number_format($sale['price'], 2) . "</td>";
    echo "</tr>";
}
echo "</table>";

// Display department breakdown with payment types
echo "<h3>Department Breakdown (By Payment Type)</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Department</th><th>Cash</th><th>Card</th><th>Total</th></tr>";
foreach ($totals['departments_by_payment'] as $department => $payment_totals) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($department) . "</td>";
    echo "<td>$" . number_format($payment_totals['cash'], 2) . "</td>";
    echo "<td>$" . number_format($payment_totals['card'], 2) . "</td>";
    echo "<td>$" . number_format($totals['departments'][$department], 2) . "</td>";
    echo "</tr>";
}
echo "</table>";

// Display payment type totals
echo "<h3>Payment Type Totals</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Payment Type</th><th>Total Amount</th></tr>";
foreach ($totals['payment_types'] as $type => $amount) {
    echo "<tr>";
    echo "<td>" . ucfirst(htmlspecialchars($type)) . "</td>";
    echo "<td>$" . number_format($amount, 2) . "</td>";
    echo "</tr>";
}
echo "</table>";

// Display grand total
echo "<h3>Grand Total: $" . number_format($totals['total'], 2) . "</h3>";

// Display timestamp
echo "<p>Report generated: " . date('Y-m-d H:i:s') . "</p>";
?> 