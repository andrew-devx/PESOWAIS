<?php
// logic/manage_goals.php
header('Content-Type: application/json');
session_start();
include '../includes/connection.php';

// 1. SECURITY: Check Login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. CHECK REQUEST METHOD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // We use an 'action' parameter to decide what to do
    $action = $_POST['action'] ?? '';

    // --- SCENARIO A: CREATE NEW GOAL ---
    if ($action === 'create') {
        $goal_name = trim($_POST['goal_name']);
        $target_amount = floatval($_POST['target_amount']);
        $deadline = !empty($_POST['deadline']) ? $_POST['deadline'] : NULL;
        $current_amount = isset($_POST['current_amount']) ? floatval($_POST['current_amount']) : 0;

        $stmt = $connection->prepare("INSERT INTO goals (user_id, goal_name, target_amount, current_amount, deadline) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isdds", $user_id, $goal_name, $target_amount, $current_amount, $deadline);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "New goal set! Kayang-kaya yan."]);
        } else {
            echo json_encode(["status" => "error", "message" => "SQL Error: " . $stmt->error]);
        }
        $stmt->close();
    }

    // --- SCENARIO B: ADD SAVINGS TO GOAL ---
    elseif ($action === 'add_money') {
        $goal_id = intval($_POST['goal_id']);
        $amount_to_add = floatval($_POST['amount']);

        // First, get current status
        $checkQuery = $connection->prepare("SELECT current_amount, target_amount FROM goals WHERE goal_id = ? AND user_id = ?");
        $checkQuery->bind_param("ii", $goal_id, $user_id);
        $checkQuery->execute();
        $result = $checkQuery->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $new_total = $row['current_amount'] + $amount_to_add;
            $target = $row['target_amount'];
            
            // Auto-complete logic
            $status = ($new_total >= $target) ? 'Achieved' : 'Active';

            // Update the database
            $updateStmt = $connection->prepare("UPDATE goals SET current_amount = ?, status = ? WHERE goal_id = ? AND user_id = ?");
            $updateStmt->bind_param("dsii", $new_total, $status, $goal_id, $user_id);
            
            if ($updateStmt->execute()) {
                $msg = ($status === 'Achieved') ? "Congrats! Goal Reached! 🎉" : "Savings added! Keep going.";
                echo json_encode(["status" => "success", "message" => $msg]);
            } else {
                echo json_encode(["status" => "error", "message" => "Update failed"]);
            }
            $updateStmt->close();
        } else {
            echo json_encode(["status" => "error", "message" => "Goal not found"]);
        }
        $checkQuery->close();
    }

    // --- SCENARIO C: DELETE GOAL ---
    elseif ($action === 'delete') {
        $goal_id = intval($_POST['goal_id']);

        $stmt = $connection->prepare("DELETE FROM goals WHERE goal_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $goal_id, $user_id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Goal deleted."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Could not delete goal."]);
        }
        $stmt->close();
    }

    // --- INVALID ACTION ---
    else {
        echo json_encode(["status" => "error", "message" => "Invalid action parameter"]);
    }

} else {
    echo json_encode(["status" => "error", "message" => "Invalid Request Method"]);
}

$connection->close();
?>
   