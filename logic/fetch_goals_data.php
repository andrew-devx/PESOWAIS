<?php
// logic/fetch_goals_data.php

if (!isset($_SESSION['user_id'])) {
    return;
}

$user_id = $_SESSION['user_id'];
$stmt = $connection->prepare("SELECT goal_id, goal_name, target_amount, current_amount, deadline, status, created_at FROM goals WHERE user_id = ? ORDER BY status ASC, deadline ASC, created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$goals = [];
while ($row = $result->fetch_assoc()) {
    $goals[] = $row;
}
$stmt->close();

// Calculate totals
$totalGoals = count($goals);
$activeGoals = [];
$achievedGoals = [];
$totalTargetAmount = 0;
$totalCurrentAmount = 0;

foreach ($goals as $goal) {
    $totalTargetAmount += $goal['target_amount'];
    $totalCurrentAmount += $goal['current_amount'];
    
    if ($goal['status'] === 'Active') {
        $activeGoals[] = $goal;
    } else {
        $achievedGoals[] = $goal;
    }
}

$progressPercentage = $totalTargetAmount > 0 ? ($totalCurrentAmount / $totalTargetAmount) * 100 : 0;
?>
