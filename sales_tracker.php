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
echo "=== Sales Transaction Report ===\n\n";

// Display all transactions
echo "All Transactions:\n";
echo "----------------\n";
foreach ($_SESSION['sales'] as $sale) {
    echo sprintf(
        "Transaction #%d\n" .
        "Category: %s\n" .
        "Payment Type: %s\n" .
        "Amount: $%s\n",
        $sale['id'],
        $sale['category'],
        ucfirst($sale['type']),
        number_format($sale['amount'], 2)
    );
    echo "----------------\n";
}

// Display category breakdown
echo "\nCategory Breakdown:\n";
echo "----------------\n";
foreach ($totals['categories'] as $category => $amount) {
    echo sprintf(
        "%s: $%s\n",
        $category,
        number_format($amount, 2)
    );
}

// Display payment type totals
echo "\nPayment Type Totals:\n";
echo "----------------\n";
echo sprintf(
    "Cash Sales: $%s\n" .
    "Card Sales: $%s\n",
    number_format($totals['cash'], 2),
    number_format($totals['card'], 2)
);

// Display grand total
echo "\nGrand Total: $" . number_format($totals['total'], 2) . "\n";

// Display timestamp
echo "\nReport generated: " . date('Y-m-d H:i:s') . "\n";
?> 