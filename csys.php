<?php
// Define the sales data
$sales = [
    ['id' => 1, 'type' => 'cash', 'amount' => 12.44],
    ['id' => 2, 'type' => 'card', 'amount' => 55.22],
    ['id' => 3, 'type' => 'cash', 'amount' => 4.98]
];

// Calculate totals
$total_cash = 0;
$total_card = 0;
$total_sales = 0;

foreach ($sales as $sale) {
    if ($sale['type'] == 'cash') {
        $total_cash += $sale['amount'];
    } else {
        $total_card += $sale['amount'];
    }
    $total_sales += $sale['amount'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cash and Credit Card Sales</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .total-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .amount {
            text-align: right;
        }
        .type-cash {
            color: #28a745;
        }
        .type-card {
            color: #007bff;
        }
    </style>
</head>
<body>
    <h1>Sales Transactions</h1>
    
    <table>
        <thead>
            <tr>
                <th>Sale ID</th>
                <th>Payment Type</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sales as $sale): ?>
            <tr>
                <td>Sale <?php echo htmlspecialchars($sale['id']); ?></td>
                <td class="type-<?php echo $sale['type']; ?>">
                    <?php echo ucfirst(htmlspecialchars($sale['type'])); ?>
                </td>
                <td class="amount">$<?php echo number_format($sale['amount'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="total-section">
        <h2>Sales Summary</h2>
        <p><strong>Total Cash Sales:</strong> $<?php echo number_format($total_cash, 2); ?></p>
        <p><strong>Total Credit Card Sales:</strong> $<?php echo number_format($total_card, 2); ?></p>
        <p><strong>Total Sales:</strong> $<?php echo number_format($total_sales, 2); ?></p>
    </div>
</body>
</html>
