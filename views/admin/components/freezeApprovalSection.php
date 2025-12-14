<?php
// Admin Dashboard Extension: Freeze Approvals Section
// Add this to adminDashboard.php or create a new adminFreezeRequests.php page

require_once __DIR__ . '/../../App/models/Subscription.php';

$subscriptionModel = new Subscription();
$freezeRequests = $subscriptionModel->getPendingFreezeRequests();
?>

<!-- Freeze Requests Section -->
<div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-white flex items-center gap-2">
            <i class="fas fa-snowflake text-amber-400"></i>
            Membership Freeze Requests
        </h3>
        <span class="bg-amber-500/20 text-amber-400 px-3 py-1 rounded-full text-sm font-semibold">
            <?= count($freezeRequests) ?> Pending
        </span>
    </div>
    
    <?php if (count($freezeRequests) > 0): ?>
    <div class="space-y-3">
        <?php foreach ($freezeRequests as $request): ?>
        <div class="bg-slate-900/50 border border-slate-700 rounded-lg p-4 hover:border-slate-600 transition">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <h4 class="font-semibold text-white"><?= htmlspecialchars($request['member_name']) ?></h4>
                        <span class="text-xs bg-blue-500/20 text-blue-400 px-2 py-1 rounded">
                            <?= htmlspecialchars($request['plan_name']) ?>
                        </span>
                    </div>
                    <p class="text-sm text-slate-400 mb-2"><?= htmlspecialchars($request['email']) ?></p>
                    
                    <div class="grid grid-cols-2 gap-3 mb-2">
                        <div class="text-sm">
                            <i class="fas fa-calendar-alt text-slate-500 mr-2"></i>
                            <span class="text-slate-400">Start:</span>
                            <span class="text-white font-medium"><?= date('M d, Y', strtotime($request['freeze_start'])) ?></span>
                        </div>
                        <div class="text-sm">
                            <i class="fas fa-calendar-check text-slate-500 mr-2"></i>
                            <span class="text-slate-400">End:</span>
                            <span class="text-white font-medium"><?= date('M d, Y', strtotime($request['freeze_end'])) ?></span>
                        </div>
                    </div>
                    
                    <?php if (!empty($request['reason'])): ?>
                    <div class="text-sm bg-slate-800 border border-slate-700 rounded p-2 mt-2">
                        <i class="fas fa-comment text-slate-500 mr-2"></i>
                        <span class="text-slate-300"><?= htmlspecialchars($request['reason']) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <p class="text-xs text-slate-500 mt-2">
                        Requested: <?= date('M d, Y g:i A', strtotime($request['requested_at'])) ?>
                    </p>
                </div>
                
                <div class="flex flex-col gap-2 ml-4">
                    <button onclick="approveFreeze(<?= $request['freeze_id'] ?>)"
                            class="px-4 py-2 bg-green-600 hover:bg-green-500 text-white text-sm font-semibold rounded-lg transition">
                        <i class="fas fa-check mr-1"></i> Approve
                    </button>
                    <button onclick="rejectFreeze(<?= $request['freeze_id'] ?>)"
                            class="px-4 py-2 bg-red-600 hover:bg-red-500 text-white text-sm font-semibold rounded-lg transition">
                        <i class="fas fa-times mr-1"></i> Reject
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="text-center py-8 text-slate-400">
        <i class="fas fa-inbox text-4xl mb-3 opacity-20"></i>
        <p>No pending freeze requests</p>
    </div>
    <?php endif; ?>
</div>

<script>
function approveFreeze(freezeId) {
    if (!confirm('Approve this freeze request?')) return;
    
    $.ajax({
        url: 'index.php?controller=Subscribe&action=ApproveFreezeRequest',
        method: 'POST',
        data: { freeze_id: freezeId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Freeze request approved!');
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Failed to process request');
        }
    });
}

function rejectFreeze(freezeId) {
    const notes = prompt('Rejection reason (optional):');
    if (notes === null) return; // User cancelled
    
    $.ajax({
        url: 'index.php?controller=Subscribe&action=RejectFreezeRequest',
        method: 'POST',
        data: { freeze_id: freezeId, admin_notes: notes },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Freeze request rejected');
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Failed to process request');
        }
    });
}
</script>
