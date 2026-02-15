<?php
    // 1. Prevent any HTML output before JSON
    ob_start();
    
    // 2. Tell browser this is JSON (Critical for Fetch)
    header('Content-Type: application/json');
    
    // 3. Error handler to return JSON instead of HTML
    set_error_handler(function($errno, $errstr, $errfile, $errline) {
        ob_clean(); // Clear any previous output
        echo json_encode([
            "status" => "error", 
            "message" => "Server error occurred",
            "debug" => [
                "error" => $errstr,
                "file" => basename($errfile),
                "line" => $errline
            ]
        ]);
        exit();
    });

    // 4. Include required files with error checking
    $rootDir = dirname(__DIR__);
    $connectionFile = $rootDir . '/includes/connection.php';
    $authFile = $rootDir . '/includes/auth_check.php';
    
    if (!file_exists($connectionFile)) {
        ob_clean();
        echo json_encode(["status" => "error", "message" => "Connection file not found"]);
        exit();
    }
    
    if (!file_exists($authFile)) {
        ob_clean();
        echo json_encode(["status" => "error", "message" => "Auth file not found"]);
        exit();
    }
    
    require_once $connectionFile;
    require_once $authFile;
    
    // 5. Check session and authentication
    if (!isset($_SESSION['user_id'])) {
        ob_clean();
        echo json_encode(["status" => "error", "message" => "Unauthorized - Please log in"]);
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $timeframe = $_GET['timeframe'] ?? 'weekly';

    $startDate = null;
    $endDate = null;

    if ($timeframe === 'monthly') {
        $start = new DateTime('first day of this month');
        $end = (clone $start)->modify('+29 days');
        $lastDay = new DateTime('last day of this month');
        if ($end > $lastDay) {
            $end = $lastDay;
        }
        $startDate = $start->format('Y-m-d');
        $endDate = $end->format('Y-m-d');
    } else {
        // Default to Weekly (Mon-Sun)
        $start = new DateTime('monday this week');
        $end = new DateTime('sunday this week');
        $startDate = $start->format('Y-m-d');
        $endDate = $end->format('Y-m-d');
    }

    $query = "SELECT 
                DATE(transaction_date) as day,
                DATE_FORMAT(transaction_date, '%b %d') as label,
                SUM(amount) as expense
              FROM transactions 
              WHERE user_id = ? 
                AND type = 'Expense'
                AND DATE(transaction_date) BETWEEN ? AND ?
              GROUP BY DATE(transaction_date)
              ORDER BY DATE(transaction_date) ASC";
    
    $stmt = $connection->prepare($query); 
    $stmt->bind_param("iss", $user_id, $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();

    $chartData = [];
    $dataByDate = [];
    while ($row = $result->fetch_assoc()) {
        $dataByDate[$row['day']] = [
            'day' => $row['day'],
            'label' => $row['label'],
            'expense' => (float)$row['expense']
        ];
    }

    // Generate all dates in the range, even if no data
    $current = new DateTime($startDate);
    $end = new DateTime($endDate);
    
    while ($current <= $end) {
        $dateStr = $current->format('Y-m-d');
        $label = $current->format('M d');
        
        if (isset($dataByDate[$dateStr])) {
            $chartData[] = $dataByDate[$dateStr];
        } else {
            $chartData[] = [
                'day' => $dateStr,
                'label' => $label,
                'expense' => 0
            ];
        }
        
        $current->modify('+1 day');
    }

    echo json_encode([
        "status" => "success",
        "data" => $chartData
    ]);
    
    $stmt->close();
    $connection->close();
?>