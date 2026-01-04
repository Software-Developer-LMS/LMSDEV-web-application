<?php
include '../../includes/db_connection.php';

// BASE URL for redirection
$base_url = "../index.php?page=students";

// Handle Add/Update Student
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_student'])) {
        $student_id = $_POST['student_id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $mobile_number = $_POST['mobile_number'];
        $raw_password = $_POST['password']; // Capture raw password for SMS
        $password = password_hash($raw_password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (student_id, name, email, mobile_number, password, role) VALUES ('$student_id', '$name', '$email', '$mobile_number', '$password', 'student')";
        if ($conn->query($sql) === TRUE) {
            $success_msg = "Student added successfully.";

            // --- SMS SENDING CODE (START) ---

            // SMSAPI.lk Configuration
            $api_key = "219|0DD8nldDs7J6YRTR1wDsFPuH5W43HJ5jKRCveK4M"; // User identified key
            $sender_id = "SMSAPI Demo"; // Check if this Sender ID is approved in your dashboard

            // Prepare Data
            // Sanitize Mobile: Remove all non-numeric characters (spaces, +, -)
            $mobile = preg_replace('/[^0-9]/', '', $mobile_number);

            // formatting: if starts with 0, replace with 94 (Sri Lanka)
            if (substr($mobile, 0, 1) == '0') {
                $mobile = '94' . substr($mobile, 1);
            }

            $sms_msg = "Welcome to SDLMS!\n" .
                "Dear $name,\n" .
                "Your login details are -\n" .
                "User: $email,\n" .
                "Pass: $raw_password.\n" .
                "Please login to SDLMS.";

            // API Endpoint (Corrected for SMS sending)
            $api_url = "https://dashboard.smsapi.lk/api/v3/sms/send";

            // Prepare Payload
            $data = [
                "recipient" => $mobile,
                "sender_id" => $sender_id,
                "type" => "plain",
                "message" => $sms_msg
            ];

            // Send SMS via cURL (POST)
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api_url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer $api_key",
                "Content-Type: application/json",
                "Accept: application/json"
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            // Logging/Feedback
            if ($http_code == 200) {
                // Check body for success status even if 200 OK
                $resp_json = json_decode($response, true);
                if (isset($resp_json['status']) && $resp_json['status'] == 'error') {
                    $success_msg .= " (SMS API Error: " . ($resp_json['message'] ?? 'Unknown Error') . ")";
                } else {
                    $success_msg .= " SMS Sent Successfully!";
                }
            } else {
                // Decode response to see error if needed
                $resp_json = json_decode($response, true);
                $err_detail = isset($resp_json['message']) ? $resp_json['message'] : "HTTP $http_code";
                $success_msg .= " (SMS failed: $err_detail)";
            }

            // --- SMS SENDING CODE (END) ---

            header("Location: $base_url&msg=" . urlencode($success_msg));
            exit();

        } else {
            $error_msg = "Error: " . $conn->error;
            header("Location: $base_url&error=" . urlencode($error_msg));
            exit();
        }
    } elseif (isset($_POST['update_student'])) {
        $id = intval($_POST['id']);
        $student_id = $_POST['student_id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $mobile_number = $_POST['mobile_number'];

        $sql = "UPDATE users SET student_id='$student_id', name='$name', email='$email', mobile_number='$mobile_number' WHERE id=$id";

        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $sql = "UPDATE users SET student_id='$student_id', name='$name', email='$email', mobile_number='$mobile_number', password='$password', change_password=1 WHERE id=$id";
        }

        if ($conn->query($sql) === TRUE) {
            header("Location: $base_url&msg=updated");
            exit();
        } else {
            $error_msg = "Error: " . $conn->error;
            header("Location: $base_url&error=" . urlencode($error_msg));
            exit();
        }
    }
}

// Handle Delete Student
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);

    // Delete profile photo if exists
    $student_query = $conn->query("SELECT profile_photo FROM users WHERE id=$id");
    if ($student_query && $student = $student_query->fetch_assoc()) {
        if (!empty($student['profile_photo'])) {
            $photo_path = "../../uploads/profile_photos/" . $student['profile_photo']; // Adjusted path for actions folder
            if (file_exists($photo_path)) {
                unlink($photo_path);
            }
        }
    }

    $conn->query("DELETE FROM users WHERE id=$id");
    header("Location: $base_url&msg=deleted");
    exit();
}

// Handle Status Toggle
if (isset($_GET['toggle_id']) && isset($_GET['status'])) {
    $id = intval($_GET['toggle_id']);
    $new_status = $_GET['status'] == 'active' ? 'inactive' : 'active';
    $conn->query("UPDATE users SET status='$new_status' WHERE id=$id");
    header("Location: $base_url&msg=status_updated");
    exit();
}
?>