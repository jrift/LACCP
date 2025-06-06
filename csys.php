<?php
session_start();

// Initialize sales array in session if it doesn't exist
if (!isset($_SESSION['sales'])) {
    $_SESSION['sales'] = [
        ['id' => 1, 'category' => 'Restaurant', 'type' => 'cash', 'amount' => 12.44],
        ['id' => 2, 'category' => 'Dues', 'type' => 'card', 'amount' => 55.22],
        ['id' => 3, 'category' => 'Guest Passes', 'type' => 'cash', 'amount' => 4.98]
    ];
}

// Handle new sale submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['amount'])) {
    $newSale = [
        'id' => count($_SESSION['sales']) + 1,
        'category' => $_POST['category'],
        'type' => strtolower($_POST['payment_type']),
        'amount' => floatval($_POST['amount'])
    ];
    $_SESSION['sales'][] = $newSale;
}

// Calculate totals
$totals = [
    'categories' => [],
    'cash' => 0,
    'card' => 0,
    'total' => 0
];

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

// Display form for new sales
echo "Add New Sale:<br><br>";
echo "<form method='post' action=''>";
echo "Amount: $<input type='number' name='amount' step='0.01' required><br><br>";
echo "Category: <select name='category' required>";
echo "  <option value='Restaurant'>Restaurant</option>";
echo "  <option value='Dues'>Dues</option>";
echo "  <option value='Guest Passes'>Guest Passes</option>";
echo "</select><br><br>";
echo "Payment Type: <select name='payment_type' required>";
echo "  <option value='cash'>Cash</option>";
echo "  <option value='card'>Card</option>";
echo "</select><br><br>";
echo "<input type='submit' value='Add Sale'>";
echo "</form><br><br>";

// Display all sales
echo "All Sales:<br>";
foreach ($_SESSION['sales'] as $sale) {
    echo "Sale {$sale['id']}: {$sale['category']} - " . 
         ucfirst($sale['type']) . " - $" . 
         number_format($sale['amount'], 2) . "<br>";
}

// Display category totals
echo "<br>Category Totals:<br>";
foreach ($totals['categories'] as $category => $amount) {
    echo "$category: $" . number_format($amount, 2) . "<br>";
}

// Display payment totals
echo "<br>Payment Totals:<br>";
echo "Cash Sales: $" . number_format($totals['cash'], 2) . "<br>";
echo "Card Sales: $" . number_format($totals['card'], 2) . "<br>";
echo "Total Sales: $" . number_format($totals['total'], 2) . "<br>";

// Add reset button
echo "<br><form method='post' action=''>";
echo "<input type='submit' name='reset' value='Reset All Sales'>";
echo "</form><br>";

// Handle reset
if (isset($_POST['reset'])) {
    unset($_SESSION['sales']);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>
