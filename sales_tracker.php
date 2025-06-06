<?php
session_start();

// Check if sales data exists in session
if (!isset($_SESSION['sales']) || empty($_SESSION['sales'])) {
    echo "No sales data available. Please add sales through csys.php first.";
    exit;
}

// Initialize totals
$totals = [
    'categories' => [],
    'cash' => 0,
    'card' => 0,
    'total' => 0
];

// Calculate totals
foreach ($_SESSION['sales'] as $sale) {
    // Category totals
    if (!isset($totals['categories'][$sale['category']])) {
        $totals['categories'][$sale['category']] = 0;
    }
    $totals['categories'][$sale['category']] += $sale['amount'];
    
    // Payment type totals
    if ($sale['type'] === 'cash') {
        $totals['cash'] += $sale['amount'];
    } else {
        $totals['card'] += $sale['amount'];
    }
    $totals['total'] += $sale['amount'];
}

// Display header
echo "=== Sales Transaction Report ===<br><br>";

// Display all transactions
echo "All Transactions:<br>";
echo "----------------<br>";
foreach ($_SESSION['sales'] as $sale) {
    echo sprintf(
        "Transaction #%d<br>" .
        "Category: %s<br>" .
        "Payment Type: %s<br>" .
        "Amount: $%s<br>",
        $sale['id'],
        $sale['category'],
        ucfirst($sale['type']),
        number_format($sale['amount'], 2)
    );
    echo "----------------<br>";
}

// Display category breakdown
echo "<br>Category Breakdown:<br>";
echo "----------------<br>";
foreach ($totals['categories'] as $category => $amount) {
    echo sprintf(
        "%s: $%s<br>",
        $category,
        number_format($amount, 2)
    );
}

// Display payment type totals
echo "<br>Payment Type Totals:<br>";
echo "----------------<br>";
echo sprintf(
    "Cash Sales: $%s<br>" .
    "Card Sales: $%s<br>",
    number_format($totals['cash'], 2),
    number_format($totals['card'], 2)
);

// Display grand total
echo "<br>Grand Total: $" . number_format($totals['total'], 2) . "<br>";

// Display timestamp
echo "<br>Report generated: " . date('Y-m-d H:i:s') . "<br>";
?> 