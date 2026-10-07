<?php
// app/Views/jobs/inspect.php
$readonly = ($job['status'] === 'pending_review' || $job['status'] === 'completed') ? 'disabled' : '';
?>
<div class="row align-items-center mb-4">
    <div class="col">
        <h2 class="fw-bold mb-0">Quality Inspection Form</h2>
        <p class="text-muted">Job Reference: <strong><?php echo htmlspecialchars($job['job_id_code']); ?></strong> | Report: <?php echo htmlspecialchars($job['report_name']); ?></p>
    </div>
</div>

<div class="card-custom" style="max-width: 800px;">
    <form id="inspectionForm" action="<?php echo BASE_URL; ?>/jobs/save-inspection/<?php echo $job['id']; ?>" method="POST" enctype="multipart/form-data">
        <h5 class="fw-bold text-primary mb-4"><i class="fa-solid fa-list-check me-2"></i>Inspection Parameters</h5>
        
        <?php if (empty($fields)): ?>
            <!-- Default mock attributes fallback for demonstration if no fields defined -->
            <div class="mb-3">
                <label class="form-label text-secondary fw-semibold">Measured Current Phase A (Amps)</label>
                <input type="number" step="0.01" name="attributes[phase_a]" class="form-control form-control-custom" required value="<?php echo htmlspecialchars($savedAttributes['phase_a'] ?? '4.85'); ?>" <?php echo $readonly; ?>>
            </div>
            <div class="mb-3">
                <label class="form-label text-secondary fw-semibold">Measured Current Phase B (Amps)</label>
                <input type="number" step="0.01" name="attributes[phase_b]" class="form-control form-control-custom" required value="<?php echo htmlspecialchars($savedAttributes['phase_b'] ?? '4.90'); ?>" <?php echo $readonly; ?>>
            </div>
            <div class="mb-3">
                <label class="form-label text-secondary fw-semibold">Measured Current Phase C (Amps)</label>
                <input type="number" step="0.01" name="attributes[phase_c]" class="form-control form-control-custom" required value="<?php echo htmlspecialchars($savedAttributes['phase_c'] ?? '4.88'); ?>" <?php echo $readonly; ?>>
            </div>
            <div class="mb-3">
                <label class="form-label text-secondary fw-semibold">Dynamic Calculation Formula (Average)</label>
                <input type="text" id="calculatedAvg" class="form-control form-control-custom" readonly value="<?php echo htmlspecialchars($savedAttributes['calculated_average'] ?? '4.88 Amps'); ?>">
            </div>
        <?php else: ?>
            <?php foreach ($fields as $field): ?>
                <?php 
                    $fieldName = $field['name'];
                    $fieldVal = $savedAttributes[$fieldName] ?? '';
                ?>
                <div class="mb-3">
                    <label class="form-label text-secondary fw-semibold"><?php echo htmlspecialchars($field['label']); ?></label>
                    <?php if ($field['type'] === 'number'): ?>
                        <input type="number" step="any" name="attributes[<?php echo htmlspecialchars($fieldName); ?>]" class="form-control form-control-custom" value="<?php echo htmlspecialchars($fieldVal); ?>" <?php echo $field['required'] ? 'required' : ''; ?> <?php echo $readonly; ?>>
                    <?php elseif ($field['type'] === 'textarea'): ?>
                        <textarea name="attributes[<?php echo htmlspecialchars($fieldName); ?>]" class="form-control form-control-custom" rows="3" <?php echo $field['required'] ? 'required' : ''; ?> <?php echo $readonly; ?>><?php echo htmlspecialchars($fieldVal); ?></textarea>
                    <?php else: ?>
                        <input type="text" name="attributes[<?php echo htmlspecialchars($fieldName); ?>]" class="form-control form-control-custom" value="<?php echo htmlspecialchars($fieldVal); ?>" <?php echo $field['required'] ? 'required' : ''; ?> <?php echo $readonly; ?>>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Digital Engineer Signature Canvas/Display -->
        <div class="border-top pt-4 mt-4 mb-4">
            <?php if ($readonly && $signature): ?>
                <label class="form-label text-secondary fw-semibold d-block">Digital Engineer Signature</label>
                <div class="border rounded-4 bg-white p-2 text-center" style="max-width: 400px;">
                    <img src="<?php echo htmlspecialchars($signature); ?>" alt="Engineer Signature" style="max-height: 120px; width: auto; max-width: 100%;">
                </div>
            <?php elseif ($readonly): ?>
                <div class="text-muted small italic">No signature recorded.</div>
            <?php else: ?>
                <div class="mb-4">
                    <label class="form-label text-secondary fw-semibold d-block">Digital Signature</label>
                    <div class="border rounded-4 bg-white" style="position: relative; width: 100%; height: 150px; cursor: crosshair; overflow: hidden;">
                        <?php if (!empty($userSignature)): ?>
                            <img src="<?php echo htmlspecialchars($userSignature); ?>" id="existingSignatureImg" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.5; pointer-events: none;" alt="Saved Signature">
                        <?php endif; ?>
                        <canvas id="signaturePad" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10;"></canvas>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <button type="button" id="btnClearSignature" class="btn btn-sm btn-outline-secondary rounded-3"><i class="fa-solid fa-eraser me-2"></i>Clear & Draw New</button>
                        <?php if (!empty($userSignature)): ?>
                            <span class="text-success small italic"><i class="fa-solid fa-check-circle me-1"></i>Saved signature loaded</span>
                        <?php endif; ?>
                    </div>
                    <input type="hidden" name="attributes[digital_signature]" id="signatureData" value="<?php echo htmlspecialchars($userSignature ?? ''); ?>">
                </div>
            <?php endif; ?>
        </div>

        <?php if (!$readonly): ?>
            <div class="border-top pt-4 mt-4">
                <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-4"><i class="fa-solid fa-circle-check me-2"></i>Submit Approved Inspection Results</button>
            </div>
        <?php endif; ?>
    </form>
</div>

<!-- Manager Approval Card -->
<?php if ($job['status'] === 'pending_review'): ?>
    <div class="card-custom mt-4" style="max-width: 800px;">
        <h5 class="fw-bold mb-3 text-purple"><i class="fa-solid fa-stamp me-2" style="color: #8b5cf6;"></i>Manager QC Approval Sign-off</h5>
        <form id="managerApprovalForm" action="<?php echo BASE_URL; ?>/jobs/approve/<?php echo $job['id']; ?>" method="POST">
            <div class="mb-3">
                <label for="remarks" class="form-label text-secondary fw-semibold">Review Remarks / Corrective Actions</label>
                <textarea name="remarks" id="remarks" class="form-control form-control-custom" rows="3" placeholder="Enter feedback or approval remarks..." required></textarea>
            </div>
            
            <div class="mb-4">
                <label class="form-label text-secondary fw-semibold d-block">Manager Digital Signature</label>
                <div class="border rounded-4 bg-white" style="position: relative; width: 100%; height: 150px; cursor: crosshair; overflow: hidden;">
                    <?php if (!empty($userSignature)): ?>
                        <img src="<?php echo htmlspecialchars($userSignature); ?>" id="existingManagerSignatureImg" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.5; pointer-events: none;" alt="Saved Signature">
                    <?php endif; ?>
                    <canvas id="managerSignaturePad" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10;"></canvas>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <button type="button" id="btnClearManagerSignature" class="btn btn-sm btn-outline-secondary rounded-3"><i class="fa-solid fa-eraser me-2"></i>Clear & Draw New</button>
                    <?php if (!empty($userSignature)): ?>
                        <span class="text-success small italic"><i class="fa-solid fa-check-circle me-1"></i>Saved signature loaded</span>
                    <?php endif; ?>
                </div>
                <input type="hidden" name="manager_signature" id="managerSignatureData" value="<?php echo htmlspecialchars($userSignature ?? ''); ?>">
            </div>

            <div class="d-flex gap-3">
                <button type="submit" id="btnApprove" class="btn btn-success flex-grow-1 py-2.5 rounded-4"><i class="fa-solid fa-circle-check me-2"></i>Approve Conformance (QC Pass)</button>
                <button type="button" id="btnReject" class="btn btn-danger flex-grow-1 py-2.5 rounded-4"><i class="fa-solid fa-triangle-exclamation me-2"></i>Reject & Request Rework</button>
            </div>
        </form>
    </div>
<?php endif; ?>

<!-- Inspection Summary (for completed jobs) -->
<?php if ($job['status'] === 'completed'): ?>
    <div class="card-custom mt-4" style="max-width: 800px; border-left: 5px solid #22c55e;">
        <h5 class="fw-bold text-success mb-2"><i class="fa-solid fa-circle-check me-2"></i>QC Job Finalized</h5>
        <p class="mb-0 text-muted small"><strong>Remarks:</strong> <?php echo htmlspecialchars($job['remarks'] ?? 'QC approved.'); ?></p>
        <div class="mt-3">
            <a href="<?php echo BASE_URL; ?>/jobs/certificate/<?php echo $job['id']; ?>" class="btn btn-outline-success btn-sm rounded-3" target="_blank">
                <i class="fa-solid fa-file-invoice me-2"></i>View Conformance Certificate
            </a>
        </div>
    </div>
<?php endif; ?>

<script>
$(document).ready(function() {
    // Dynamic formula calculation
    function calculateAverage() {
        let total = 0;
        let count = 0;
        $('input[type="number"][name^="attributes"]').each(function() {
            let val = parseFloat($(this).val());
            if (!isNaN(val)) {
                total += val;
                count++;
            }
        });
        if (count > 0) {
            let avg = (total / count).toFixed(2);
            $('#calculatedAvg').val(avg + " Amps");
        }
    }
    $('input[type="number"][name^="attributes"]').on('input change', calculateAverage);

    // Reject redirection handler
    $('#btnReject').on('click', function() {
        let remarks = $('#remarks').val().trim();
        if (!remarks) {
            Swal.fire({
                icon: 'warning',
                title: 'Remarks Required',
                text: 'Please input rework reasons or rejection remarks.'
            });
            return;
        }
        let form = $('#managerApprovalForm');
        form.attr('action', '<?php echo BASE_URL; ?>/jobs/reject/<?php echo $job['id']; ?>');
        form.submit();
    });

    function setupSignaturePad(canvasId, clearBtnId, dataInputId, formId = null) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        let drawing = false;
        
        function resizeCanvas() {
            const currentData = canvas.toDataURL();
            canvas.width = canvas.parentElement.clientWidth;
            canvas.height = canvas.parentElement.clientHeight;
            ctx.strokeStyle = '#000000';
            ctx.lineWidth = 3;
            ctx.lineCap = 'round';
            const blank = document.createElement('canvas');
            blank.width = canvas.width;
            blank.height = canvas.height;
            if (currentData !== blank.toDataURL()) {
                const img = new Image();
                img.onload = function() { ctx.drawImage(img, 0, 0); };
                img.src = currentData;
            }
        }
        
        resizeCanvas();
        $(window).on('resize', function() { resizeCanvas(); });

        function getMousePos(canvasDom, touchOrMouseEvent) {
            const rect = canvasDom.getBoundingClientRect();
            const clientX = touchOrMouseEvent.touches ? touchOrMouseEvent.touches[0].clientX : touchOrMouseEvent.clientX;
            const clientY = touchOrMouseEvent.touches ? touchOrMouseEvent.touches[0].clientY : touchOrMouseEvent.clientY;
            return {
                x: clientX - rect.left,
                y: clientY - rect.top
            };
        }

        // Mouse Events
        $(canvas).on('mousedown', function(e) {
            drawing = true;
            if (canvasId === 'signaturePad') $('#existingSignatureImg').hide();
            if (canvasId === 'managerSignaturePad') $('#existingManagerSignatureImg').hide();
            const pos = getMousePos(canvas, e.originalEvent);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
        });

        $(canvas).on('mousemove', function(e) {
            if (!drawing) return;
            const pos = getMousePos(canvas, e.originalEvent);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
        });

        $(canvas).on('mouseup mouseleave', function() {
            drawing = false;
        });

        // Touch Events (Vanilla JS for {passive: false})
        canvas.addEventListener('touchstart', function(e) {
            drawing = true;
            if (canvasId === 'signaturePad') $('#existingSignatureImg').hide();
            if (canvasId === 'managerSignaturePad') $('#existingManagerSignatureImg').hide();
            const pos = getMousePos(canvas, e);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
            e.preventDefault();
        }, { passive: false });

        canvas.addEventListener('touchmove', function(e) {
            if (!drawing) return;
            const pos = getMousePos(canvas, e);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            e.preventDefault();
        }, { passive: false });

        canvas.addEventListener('touchend', function(e) {
            drawing = false;
        }, { passive: false });

        canvas.addEventListener('touchcancel', function(e) {
            drawing = false;
        }, { passive: false });

        $('#' + clearBtnId).on('click', function() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            $('#' + dataInputId).val('');
            
            if (canvasId === 'signaturePad') $('#existingSignatureImg').hide();
            if (canvasId === 'managerSignaturePad') $('#existingManagerSignatureImg').hide();
        });

        if(formId) {
            $('#' + formId).on('submit', function(e) {
                // If this is the manager form and we are rejecting, we might not require signature.
                // But for approval we require signature.
                // We'll check if action is reject, skip signature validation if you want, but here we just require it for both or skip if not drawn.
                const blank = document.createElement('canvas');
                blank.width = canvas.width;
                blank.height = canvas.height;
                
                let isRejecting = $(this).attr('action').includes('/reject/');
                
                // If user didn't draw anything new
                if (canvas.toDataURL() === blank.toDataURL()) {
                    // Check if there is already a pre-loaded signature in the hidden input
                    const existingSig = $('#' + dataInputId).val();
                    
                    if (!existingSig && !isRejecting) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Signature Required',
                            text: 'Please draw your digital signature before submitting.'
                        });
                    }
                } else {
                    $('#' + dataInputId).val(canvas.toDataURL());
                }
            });
        }
    }

    setupSignaturePad('signaturePad', 'btnClearSignature', 'signatureData', 'inspectionForm');
    setupSignaturePad('managerSignaturePad', 'btnClearManagerSignature', 'managerSignatureData', 'managerApprovalForm');
});
</script>
