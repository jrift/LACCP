<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Tracker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="number"], select {
            padding: 8px;
            width: 200px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Sales Tracker</h1>

    <?php
    session_start();

    // Initialize sales array if it doesn't exist
    if (!isset($_SESSION['sales'])) {
        $_SESSION['sales'] = [];
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['amount']) && isset($_POST['payment_type'])) {
            $sale = [
                'amount' => floatval($_POST['amount']),
                'payment_type' => $_POST['payment_type'],
                'date' => date('Y-m-d H:i:s')
            ];
            $_SESSION['sales'][] = $sale;
        }
    }

    // Calculate totals
    $total_cash = 0;
    $total_credit = 0;
    foreach ($_SESSION['sales'] as $sale) {
        if ($sale['payment_type'] === 'cash') {
            $total_cash += $sale['amount'];
        } else {
            $total_credit += $sale['amount'];
        }
    }
    $total_sales = $total_cash + $total_credit;
    ?>

    <!-- Sales Entry Form -->
    <form method="POST">
        <div class="form-group">
            <label for="amount">Sale Amount ($):</label>
            <input type="number" step="0.01" name="amount" id="amount" required>
        </div>
        <div class="form-group">
            <label for="payment_type">Payment Type:</label>
            <select name="payment_type" id="payment_type" required>
                <option value="cash">Cash</option>
                <option value="credit">Credit Card</option>
            </select>
        </div>
        <button type="submit">Record Sale</button>
    </form>

    <!-- Summary Section -->
    <div class="summary">
        <h2>Sales Summary</h2>
        <p>Total Cash Sales: $<?php echo number_format($total_cash, 2); ?></p>
        <p>Total Credit Card Sales: $<?php echo number_format($total_credit, 2); ?></p>
        <p><strong>Total Sales: $<?php echo number_format($total_sales, 2); ?></strong></p>
    </div>

    <!-- Sales History Table -->
    <h2>Sales History</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Amount</th>
                <th>Payment Type</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (array_reverse($_SESSION['sales']) as $sale): ?>
            <tr>
                <td><?php echo htmlspecialchars($sale['date']); ?></td>
                <td>$<?php echo number_format($sale['amount'], 2); ?></td>
                <td><?php echo ucfirst(htmlspecialchars($sale['payment_type'])); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Reset Button -->
    <?php if (!empty($_SESSION['sales'])): ?>
    <form method="POST" style="margin-top: 20px;">
        <button type="submit" name="reset" onclick="return confirm('Are you sure you want to reset all sales data?');">
            Reset All Data
        </button>
    </form>
    <?php endif; ?>

    <?php
    // Handle reset
    if (isset($_POST['reset'])) {
        $_SESSION['sales'] = [];
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    ?>
</body>
</html> 