<?php
    // This file is included from dashboard.php, so $connection and session are already available
    // No need to include connection.php or auth_check.php again

    $ai_message = "";
    $ai_mood = "neutral";

    $user_id = $_SESSION['user_id'] ?? null;

    if (!isset($connection) && $user_id) {
        include_once __DIR__ . '/../includes/connection.php';
    }

    if ($user_id && isset($connection)) {
        $thisWeekStart = (new DateTime('monday this week'))->format('Y-m-d');
        $thisWeekEnd = (new DateTime('sunday this week'))->format('Y-m-d');
        $lastWeekStart = (new DateTime('monday last week'))->format('Y-m-d');
        $lastWeekEnd = (new DateTime('sunday last week'))->format('Y-m-d');

        $stmt = $connection->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM transactions WHERE user_id = ? AND type = 'Expense' AND category = 'Food' AND DATE(transaction_date) BETWEEN ? AND ?");
        $stmt->bind_param("iss", $user_id, $thisWeekStart, $thisWeekEnd);
        $stmt->execute();
        $thisWeekFood = (float) $stmt->get_result()->fetch_assoc()['total'];
        $stmt->close();

        $stmt = $connection->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM transactions WHERE user_id = ? AND type = 'Expense' AND category = 'Food' AND DATE(transaction_date) BETWEEN ? AND ?");
        $stmt->bind_param("iss", $user_id, $lastWeekStart, $lastWeekEnd);
        $stmt->execute();
        $lastWeekFood = (float) $stmt->get_result()->fetch_assoc()['total'];
        $stmt->close();

        $computed_balance = $current_balance ?? (($totalIncome ?? 0) - ($totalExpenses ?? 0));

        if ($lastWeekFood > 0 && $thisWeekFood > ($lastWeekFood * 1.3)) {
            $ai_message = "⚠️ Notice: You've spent <strong>50% more on Food</strong> this week compared to last week. Time to cook at home?";
            $ai_mood = "warning";
        } elseif ($computed_balance < 500) {
            $ai_message = "📉 <strong>Critical:</strong> You have less than ₱500 left. Avoid 'Leisure' spending for now!";
            $ai_mood = "danger";
        } else {
            $ai_message = "✨ <strong>On Track:</strong> Your spending is stable. You are saving enough to hit your goal soon!";
            $ai_mood = "good";
        }
    } else {
        $ai_message = "✨ <strong>On Track:</strong> Your spending is stable. Keep it up!";
        $ai_mood = "good";
    }
?>