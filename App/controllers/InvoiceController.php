<?php
require_once __DIR__ . "/../Controller.php";
require_once __DIR__ . "/../models/Payment.php";
require_once __DIR__ . "/../models/Subscription.php";
require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../models/Plan.php";

class InvoiceController extends Controller {
    
    public function downloadInvoice() {
        $this->requireLogin();
        $user_id = $_SESSION['user_id'];
        $subscription_id = $_GET['subscription_id'] ?? null;

        if (!$subscription_id) {
            header('Location: index.php?controller=Dashboard&action=payments');
            exit;
        }

        $payment = new Payment();
        $subscription = new Subscription();
        $user = new User();
        $plan = new Plan();

        // Get payment details
        $paymentDetails = $payment->getPaymentBySubscriptionId($subscription_id);
        $subscriptionDetails = $subscription->getSubscriptionById($subscription_id);
        $userDetails = $user->getMember($user_id);
        $planDetails = $plan->getPlanById($subscriptionDetails['plan_id']);

        if (!$paymentDetails) {
            die("Payment not found");
        }

        // Generate HTML Invoice
        $this->generateHTMLInvoice($paymentDetails, $subscriptionDetails, $userDetails, $planDetails);
    }

    public function downloadReceipt() {
        $this->requireLogin();
        session_start();
        $user_id = $_SESSION['user_id'];
        $subscription_id = $_GET['subscription_id'] ?? null;

        if (!$subscription_id) {
            header('Location: index.php?controller=Dashboard&action=payments');
            exit;
        }

        $payment = new Payment();
        $subscription = new Subscription();
        $user = new User();
        $plan = new Plan();

        // Get payment details
        $paymentDetails = $payment->getPaymentBySubscriptionId($subscription_id);
        $subscriptionDetails = $subscription->getSubscriptionById($subscription_id);
        $userDetails = $user->getMember($user_id);
        $planDetails = $plan->getPlanById($subscriptionDetails['plan_id']);

        if (!$paymentDetails || $paymentDetails['status'] != 'paid') {
            die("Receipt not available. Payment must be completed first.");
        }

        // Generate HTML Receipt
        $this->generateHTMLReceipt($paymentDetails, $subscriptionDetails, $userDetails, $planDetails);
    }

    private function generateHTMLInvoice($payment, $subscription, $user, $plan) {
        // Set headers for PDF download
        header('Content-Type: text/html; charset=utf-8');
        
        $invoice_number = "INV-" . str_pad($payment['payment_id'], 6, '0', STR_PAD_LEFT);
        $invoice_date = date('F d, Y', strtotime($payment['payment_date']));
        
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Invoice - <?= $invoice_number ?></title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 20px;
                    background: white;
                }
                .invoice-container {
                    max-width: 800px;
                    margin: 0 auto;
                    background: white;
                    padding: 40px;
                    border: 1px solid #ddd;
                }
                .header {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 40px;
                    border-bottom: 3px solid #2563eb;
                    padding-bottom: 20px;
                }
                .logo {
                    font-size: 32px;
                    font-weight: bold;
                    color: #2563eb;
                }
                .invoice-details {
                    text-align: right;
                }
                .section-title {
                    font-size: 18px;
                    font-weight: bold;
                    margin: 20px 0 10px 0;
                    color: #1f2937;
                }
                .info-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 30px;
                    margin-bottom: 30px;
                }
                .info-box {
                    padding: 15px;
                    background: #f9fafb;
                    border-radius: 8px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 20px 0;
                }
                th {
                    background: #2563eb;
                    color: white;
                    padding: 12px;
                    text-align: left;
                }
                td {
                    padding: 12px;
                    border-bottom: 1px solid #e5e7eb;
                }
                .total-section {
                    text-align: right;
                    margin-top: 30px;
                    padding-top: 20px;
                    border-top: 2px solid #e5e7eb;
                }
                .total-amount {
                    font-size: 24px;
                    font-weight: bold;
                    color: #2563eb;
                }
                .footer {
                    margin-top: 40px;
                    padding-top: 20px;
                    border-top: 1px solid #e5e7eb;
                    text-align: center;
                    color: #6b7280;
                    font-size: 12px;
                }
                .status-badge {
                    display: inline-block;
                    padding: 5px 15px;
                    border-radius: 20px;
                    font-size: 12px;
                    font-weight: bold;
                }
                .status-pending {
                    background: #fef3c7;
                    color: #92400e;
                }
                .status-paid {
                    background: #d1fae5;
                    color: #065f46;
                }
                @media print {
                    body { margin: 0; padding: 0; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="invoice-container">
                <!-- Header -->
                <div class="header">
                    <div>
                        <div class="logo">GYMAZING</div>
                        <p style="margin: 5px 0; color: #6b7280;">Fitness Center</p>
                    </div>
                    <div class="invoice-details">
                        <h1 style="margin: 0; color: #1f2937;">INVOICE</h1>
                        <p style="margin: 5px 0;"><strong>Invoice #:</strong> <?= $invoice_number ?></p>
                        <p style="margin: 5px 0;"><strong>Date:</strong> <?= $invoice_date ?></p>
                        <p style="margin: 5px 0;">
                            <span class="status-badge status-<?= $payment['status'] ?>">
                                <?= strtoupper($payment['status']) ?>
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Customer and Company Info -->
                <div class="info-grid">
                    <div class="info-box">
                        <div class="section-title">Bill To:</div>
                        <p style="margin: 5px 0;"><strong><?= htmlspecialchars($user['name']) ?></strong></p>
                        <p style="margin: 5px 0; color: #6b7280;"><?= htmlspecialchars($user['email']) ?></p>
                        <p style="margin: 5px 0; color: #6b7280;">Member ID: <?= $user['user_id'] ?></p>
                    </div>
                    <div class="info-box">
                        <div class="section-title">From:</div>
                        <p style="margin: 5px 0;"><strong>Gymazing Fitness Center</strong></p>
                        <p style="margin: 5px 0; color: #6b7280;">123 Fitness Street</p>
                        <p style="margin: 5px 0; color: #6b7280;">Zamboanga City, Zamboanga Del Sur</p>
                        <p style="margin: 5px 0; color: #6b7280;">Philippines</p>
                    </div>
                </div>

                <!-- Invoice Items -->
                <div class="section-title">Invoice Details</div>
                <table>
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Period</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($plan['plan_name']) ?> Membership</strong><br>
                                <span style="color: #6b7280; font-size: 14px;">Subscription ID: <?= $subscription['subscription_id'] ?></span>
                            </td>
                            <td>
                                <?= date('M d, Y', strtotime($subscription['start_date'])) ?><br>
                                <span style="color: #6b7280;">to</span><br>
                                <?= date('M d, Y', strtotime($subscription['end_date'])) ?>
                            </td>
                            <td><strong>₱<?= number_format($payment['amount'], 2) ?></strong></td>
                        </tr>
                    </tbody>
                </table>

                <!-- Total -->
                <div class="total-section">
                    <p style="margin: 5px 0; font-size: 16px;"><strong>Subtotal:</strong> ₱<?= number_format($payment['amount'], 2) ?></p>
                    <p style="margin: 5px 0; font-size: 16px;"><strong>Tax (0%):</strong> ₱0.00</p>
                    <p style="margin: 15px 0 0 0;">
                        <strong>Total Amount:</strong><br>
                        <span class="total-amount">₱<?= number_format($payment['amount'], 2) ?></span>
                    </p>
                </div>

                <!-- Payment Information -->
                <?php if ($payment['status'] == 'pending'): ?>
                <div class="info-box" style="margin-top: 30px; background: #fef3c7; border-left: 4px solid #f59e0b;">
                    <div class="section-title" style="color: #92400e;">Payment Due</div>
                    <p style="margin: 5px 0; color: #92400e;">
                        <strong>Due Date:</strong> <?= date('F d, Y', strtotime($payment['payment_date'])) ?>
                    </p>
                    <p style="margin: 5px 0; color: #92400e;">
                        Please complete your payment by the due date to continue enjoying our services.
                    </p>
                </div>
                <?php endif; ?>

                <!-- Notes -->
                <div style="margin-top: 30px; padding: 15px; background: #f9fafb; border-radius: 8px;">
                    <p style="margin: 5px 0; font-size: 14px; color: #6b7280;">
                        <strong>Terms & Conditions:</strong><br>
                        • Payment is due by the specified due date<br>
                        • Late payments may result in service interruption<br>
                        • For questions, contact us at billing@gymazing.com
                    </p>
                </div>

                <!-- Footer -->
                <div class="footer">
                    <p>Thank you for being a valued member of Gymazing Fitness Center!</p>
                    <p>This is a computer-generated invoice and does not require a signature.</p>
                    <p>Generated on <?= date('F d, Y h:i A') ?></p>
                </div>

                <!-- Print Button -->
                <div class="no-print" style="text-align: center; margin-top: 20px;">
                    <button onclick="window.print()" style="padding: 12px 24px; background: #2563eb; color: white; border: none; border-radius: 8px; font-size: 16px; cursor: pointer;">
                        Print Invoice
                    </button>
                    <button onclick="window.close()" style="padding: 12px 24px; background: #6b7280; color: white; border: none; border-radius: 8px; font-size: 16px; cursor: pointer; margin-left: 10px;">
                        Close
                    </button>
                </div>
            </div>

            <script>
                // Auto print on load (optional)
                // window.onload = function() { window.print(); }
            </script>
        </body>
        </html>
        <?php
    }

    private function generateHTMLReceipt($payment, $subscription, $user, $plan) {
        // Set headers
        header('Content-Type: text/html; charset=utf-8');
        
        $receipt_number = "RCT-" . str_pad($payment['payment_id'], 6, '0', STR_PAD_LEFT);
        $payment_date = date('F d, Y h:i A', strtotime($payment['payment_date']));
        
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Receipt - <?= $receipt_number ?></title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 20px;
                    background: white;
                }
                .receipt-container {
                    max-width: 600px;
                    margin: 0 auto;
                    background: white;
                    padding: 40px;
                    border: 2px solid #22c55e;
                    border-radius: 12px;
                    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                }
                .receipt-header {
                    text-align: center;
                    margin-bottom: 30px;
                    padding-bottom: 20px;
                    border-bottom: 2px dashed #22c55e;
                }
                .logo {
                    font-size: 36px;
                    font-weight: bold;
                    color: #22c55e;
                    margin-bottom: 10px;
                }
                .receipt-title {
                    font-size: 24px;
                    color: #1f2937;
                    margin: 10px 0;
                }
                .success-badge {
                    display: inline-block;
                    background: #22c55e;
                    color: white;
                    padding: 8px 20px;
                    border-radius: 20px;
                    font-weight: bold;
                    margin: 10px 0;
                }
                .receipt-info {
                    margin: 20px 0;
                    padding: 20px;
                    background: #f0fdf4;
                    border-radius: 8px;
                }
                .info-row {
                    display: flex;
                    justify-content: space-between;
                    padding: 10px 0;
                    border-bottom: 1px solid #d1fae5;
                }
                .info-row:last-child {
                    border-bottom: none;
                }
                .info-label {
                    color: #6b7280;
                    font-weight: 500;
                }
                .info-value {
                    color: #1f2937;
                    font-weight: 600;
                }
                .amount-section {
                    text-align: center;
                    margin: 30px 0;
                    padding: 20px;
                    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
                    border-radius: 12px;
                    color: white;
                }
                .amount-label {
                    font-size: 14px;
                    opacity: 0.9;
                    margin-bottom: 5px;
                }
                .amount-value {
                    font-size: 48px;
                    font-weight: bold;
                }
                .footer {
                    margin-top: 30px;
                    padding-top: 20px;
                    border-top: 2px dashed #22c55e;
                    text-align: center;
                    color: #6b7280;
                    font-size: 12px;
                }
                .checkmark {
                    font-size: 64px;
                    color: #22c55e;
                }
                @media print {
                    body { margin: 0; padding: 10px; }
                    .no-print { display: none; }
                    .receipt-container { box-shadow: none; }
                }
            </style>
        </head>
        <body>
            <div class="receipt-container">
                <!-- Header -->
                <div class="receipt-header">
                    <div class="checkmark">✓</div>
                    <div class="logo">GYMAZING</div>
                    <div class="receipt-title">PAYMENT RECEIPT</div>
                    <div class="success-badge">PAID</div>
                </div>

                <!-- Receipt Number and Date -->
                <div class="receipt-info">
                    <div class="info-row">
                        <span class="info-label">Receipt Number:</span>
                        <span class="info-value"><?= $receipt_number ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Payment Date:</span>
                        <span class="info-value"><?= $payment_date ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Transaction ID:</span>
                        <span class="info-value">TXN-<?= $payment['payment_id'] ?></span>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="receipt-info">
                    <div class="info-row">
                        <span class="info-label">Member Name:</span>
                        <span class="info-value"><?= htmlspecialchars($user['name']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Member ID:</span>
                        <span class="info-value"><?= $user['user_id'] ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value"><?= htmlspecialchars($user['email']) ?></span>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="receipt-info">
                    <div class="info-row">
                        <span class="info-label">Membership Plan:</span>
                        <span class="info-value"><?= htmlspecialchars($plan['plan_name']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Subscription Period:</span>
                        <span class="info-value">
                            <?= date('M d', strtotime($subscription['start_date'])) ?> - 
                            <?= date('M d, Y', strtotime($subscription['end_date'])) ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Payment Method:</span>
                        <span class="info-value">Credit Card</span>
                    </div>
                </div>

                <!-- Amount Paid -->
                <div class="amount-section">
                    <div class="amount-label">AMOUNT PAID</div>
                    <div class="amount-value">₱<?= number_format($payment['amount'], 2) ?></div>
                </div>

                <!-- Footer -->
                <div class="footer">
                    <p><strong>Thank you for your payment!</strong></p>
                    <p>This receipt confirms that your payment has been received and processed successfully.</p>
                    <p>For any inquiries, please contact us at billing@gymazing.com or call (02) 1234-5678</p>
                    <p style="margin-top: 20px;">Gymazing Fitness Center | 123 Fitness Street, Zamboanga City, Zamboanga Del Sur</p>
                    <p>Generated on <?= date('F d, Y h:i A') ?></p>
                </div>

                <!-- Print Button -->
                <div class="no-print" style="text-align: center; margin-top: 20px;">
                    <button onclick="window.print()" style="padding: 12px 24px; background: #22c55e; color: white; border: none; border-radius: 8px; font-size: 16px; cursor: pointer;">
                        Print Receipt
                    </button>
                    <button onclick="window.close()" style="padding: 12px 24px; background: #6b7280; color: white; border: none; border-radius: 8px; font-size: 16px; cursor: pointer; margin-left: 10px;">
                        Close
                    </button>
                </div>
            </div>

            <script>
                // Auto print on load (optional)
                // window.onload = function() { window.print(); }
            </script>
        </body>
        </html>
        <?php
    }
}
?>