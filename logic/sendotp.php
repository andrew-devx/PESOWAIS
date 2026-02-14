<?php
ob_start(); // Start output buffering
error_reporting(0); // Disable error reporting for cleaner JSON
ini_set('display_errors', 0);
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once dirname(__DIR__) . '/libs/PHPMailer-master/src/Exception.php';
require_once dirname(__DIR__) . '/libs/PHPMailer-master/src/PHPMailer.php';
require_once dirname(__DIR__) . '/libs/PHPMailer-master/src/SMTP.php';
require_once dirname(__DIR__) . '/includes/config.php';

if(isset($_POST['sendotp'])){
    $email = $_POST['email'];
    $type = isset($_POST['type']) ? $_POST['type'] : 'verification'; // 'verification' or 'reset'
    
    // Validate email
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit;
    }
    
    // If it's a password reset, check if email exists
    if($type === 'reset') {
        require_once dirname(__DIR__) . '/includes/connection.php';
        $check_stmt = $connection->prepare("SELECT email FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if($result->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'No account found with this email address.']);
            $check_stmt->close();
            exit;
        }
        $check_stmt->close();
    }
    
    // Generate 6-digit OTP
    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp;
    $_SESSION['email'] = $email;
    $_SESSION['otp_time'] = time(); // Store timestamp for expiration
    
    // Create PHPMailer instance
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        
        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($email);
        
        // Content
        $mail->isHTML(true);
        
        if($type === 'reset') {
            $mail->Subject = 'PesoWais - Password Reset Code';
            $mail->Body = "
    <html>
    <body style='margin: 0; padding: 0; font-family: \"Segoe UI\", Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7fa; color: #334155;'>
        <table width='100%' cellpadding='0' cellspacing='0' style='background-color: #f4f7fa; padding-bottom: 40px;'>
            <tr>
                <td align='center'>
                    <table width='600' cellpadding='0' cellspacing='0' style='max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);'>
                        <!-- Header -->
                        <tr>
                            <td style='background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: white; padding: 40px 20px; text-align: center;'>
                                <h1 style='margin: 0; font-size: 28px; letter-spacing: 1px;'>PesoWais</h1>
                            </td>
                        </tr>
                        <!-- Content -->
                        <tr>
                            <td style='padding: 40px 30px; line-height: 1.6;'>
                                <h2 style='color: #1e293b; margin-top: 0;'>Reset Your Password</h2>
                                <p>Hello,</p>
                                <p>We received a request to reset your <strong>PesoWais</strong> account password. Use the code below to continue:</p>
                                
                                <!-- OTP Code -->
                                <div style='text-align: center; margin: 30px 0;'>
                                    <div style='display: inline-block; font-size: 36px; font-weight: 800; color: #2563eb; background-color: #dbeafe; padding: 15px 30px; border: 2px dashed #60a5fa; border-radius: 10px; letter-spacing: 8px;'>
                                        {$otp}
                                    </div>
                                </div>
                                
                                <p>This code is valid for <strong>10 minutes</strong>. For your security, never share this code with anyone.</p>
                                
                                <div style='font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 20px; margin-top: 20px;'>
                                    <p>If you did not request a password reset, please ignore this email or contact our support if you have concerns.</p>
                                </div>
                            </td>
                        </tr>
                        <!-- Footer -->
                        <tr>
                            <td style='text-align: center; padding: 20px; font-size: 13px; color: #94a3b8; background-color: #f8fafc;'>
                                <p style='margin: 5px 0;'>© " . date('Y') . " PesoWais Support • Cabuyao, Philippines</p>
                                <p style='margin: 5px 0;'>Smart Budgeting for Every Filipino</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>
";
        } else {
            $mail->Subject = 'PesoWais - Your OTP Code';
            $mail->Body = "
    <html>
    <body style='margin: 0; padding: 0; font-family: \"Segoe UI\", Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7fa; color: #334155;'>
        <table width='100%' cellpadding='0' cellspacing='0' style='background-color: #f4f7fa; padding-bottom: 40px;'>
            <tr>
                <td align='center'>
                    <table width='600' cellpadding='0' cellspacing='0' style='max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);'>
                        <!-- Header -->
                        <tr>
                            <td style='background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: white; padding: 40px 20px; text-align: center;'>
                                <h1 style='margin: 0; font-size: 28px; letter-spacing: 1px;'>PesoWais</h1>
                            </td>
                        </tr>
                        <!-- Content -->
                        <tr>
                            <td style='padding: 40px 30px; line-height: 1.6;'>
                                <h2 style='color: #1e293b; margin-top: 0;'>Verify Your Identity</h2>
                                <p>Hello,</p>
                                <p>To keep your <strong>PesoWais</strong> account secure, please use the One-Time Password (OTP) below to complete your verification:</p>
                                
                                <!-- OTP Code -->
                                <div style='text-align: center; margin: 30px 0;'>
                                    <div style='display: inline-block; font-size: 36px; font-weight: 800; color: #2563eb; background-color: #dbeafe; padding: 15px 30px; border: 2px dashed #60a5fa; border-radius: 10px; letter-spacing: 8px;'>
                                        {$otp}
                                    </div>
                                </div>
                                
                                <p>This code is valid for <strong>10 minutes</strong>. For your security, never share this code with anyone.</p>
                                
                                <div style='font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 20px; margin-top: 20px;'>
                                    <p>If you did not attempt to sign in to PesoWais, please secure your account immediately or contact our support.</p>
                                </div>
                            </td>
                        </tr>
                        <!-- Footer -->
                        <tr>
                            <td style='text-align: center; padding: 20px; font-size: 13px; color: #94a3b8; background-color: #f8fafc;'>
                                <p style='margin: 5px 0;'>© " . date('Y') . " PesoWais Support • Cabuyao, Philippines</p>
                                <p style='margin: 5px 0;'>Smart Budgeting for Every Filipino</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>
";
        }
        
        // Send email
        // Send email
        $mail->send();
        ob_clean();
        echo json_encode(['success' => true, 'message' => 'OTP sent to ' . $email]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to send OTP. Error: ' . $mail->ErrorInfo]);
    }
}