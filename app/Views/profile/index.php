<?php
// app/Views/profile/index.php
?>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-2">
    <div>
        <h3 class="fw-bold text-primary mb-0"><i class="fa-solid fa-user-circle me-2"></i>My Profile</h3>
        <p class="text-muted small mb-0">Manage your account settings and digital signature</p>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-check-circle me-2"></i><?php echo htmlspecialchars($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card-custom">
            <h5 class="fw-bold mb-3"><i class="fa-solid fa-address-card text-primary me-2"></i>User Details</h5>
            <div class="mb-3">
                <label class="text-muted small fw-semibold d-block">Username</label>
                <div class="fw-bold"><?php echo htmlspecialchars($user['username'] ?? ''); ?></div>
            </div>
            <div class="mb-3">
                <label class="text-muted small fw-semibold d-block">Email Address</label>
                <div><?php echo htmlspecialchars($user['email'] ?? ''); ?></div>
            </div>
            <div class="mb-3">
                <label class="text-muted small fw-semibold d-block">Assigned Role</label>
                <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                    <?php echo htmlspecialchars($user['role_name'] ?? 'User'); ?>
                </span>
            </div>
        </div>
    </div>
    
    <div class="col-md-7 mb-4">
        <div class="card-custom">
            <h5 class="fw-bold mb-3"><i class="fa-solid fa-signature text-primary me-2"></i>Digital Signature</h5>
            <p class="text-muted small">Draw and save your digital signature below. This will be automatically applied when you approve jobs or submit inspections.</p>
            
            <form id="signatureForm" action="<?php echo BASE_URL; ?>/profile/save-signature" method="POST">
                <div class="mb-3">
                    <div class="border rounded-4 bg-white" style="position: relative; width: 100%; height: 200px; cursor: crosshair; overflow: hidden;">
                        <?php if (!empty($user['signature'])): ?>
                            <img src="<?php echo htmlspecialchars($user['signature']); ?>" id="existingSignature" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.2; pointer-events: none;" alt="Saved Signature">
                        <?php endif; ?>
                        <canvas id="signaturePad" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10;"></canvas>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <button type="button" id="btnClearSignature" class="btn btn-sm btn-outline-secondary rounded-3"><i class="fa-solid fa-eraser me-2"></i>Clear</button>
                        <div class="d-flex align-items-center">
                            <label for="signatureUpload" class="btn btn-sm btn-outline-primary rounded-3 mb-0 me-2" style="cursor: pointer;">
                                <i class="fa-solid fa-upload me-2"></i>Upload Image
                            </label>
                            <input type="file" id="signatureUpload" accept="image/png, image/webp" class="d-none">
                            <span class="text-muted small italic">Draw or Upload</span>
                        </div>
                    </div>
                    <input type="hidden" name="signature_data" id="signatureData">
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary py-2.5 rounded-4"><i class="fa-solid fa-save me-2"></i>Save Signature</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    const canvas = document.getElementById('signaturePad');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    let drawing = false;
    let hasDrawn = false;

    // Handle resizing gracefully
    function resizeCanvas() {
        // Save current content
        const currentData = canvas.toDataURL();
        
        canvas.width = canvas.parentElement.clientWidth;
        canvas.height = canvas.parentElement.clientHeight;
        
        ctx.strokeStyle = '#000000';
        ctx.lineWidth = 3;
        ctx.lineCap = 'round';
        
        // Restore content if something was drawn
        if (hasDrawn) {
            const img = new Image();
            img.onload = function() {
                ctx.drawImage(img, 0, 0);
            };
            img.src = currentData;
        }
    }
    
    // Initial size
    resizeCanvas();
    
    // Update on window resize
    $(window).on('resize', function() {
        resizeCanvas();
    });

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
        hasDrawn = true;
        $('#existingSignature').hide();
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

    // Touch Events (Vanilla JS for {passive: false} to prevent scrolling)
    canvas.addEventListener('touchstart', function(e) {
        drawing = true;
        hasDrawn = true;
        $('#existingSignature').hide();
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

    $('#btnClearSignature').on('click', function() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        $('#signatureData').val('');
        $('#existingSignature').hide();
        $('#signatureUpload').val('');
        hasDrawn = true;
    });

    $('#signatureUpload').on('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        if (!file.type.match('image/png') && !file.type.match('image/webp')) {
            Swal.fire('Invalid File', 'Please upload a PNG or WEBP image.', 'error');
            $(this).val('');
            return;
        }

        const reader = new FileReader();
        reader.onload = function(event) {
            const img = new Image();
            img.onload = function() {
                // Clear existing canvas
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                
                // Draw image scaled to fit canvas
                // We'll maintain aspect ratio and center it
                const scale = Math.min(canvas.width / img.width, canvas.height / img.height);
                const x = (canvas.width / 2) - (img.width / 2) * scale;
                const y = (canvas.height / 2) - (img.height / 2) * scale;
                
                ctx.drawImage(img, x, y, img.width * scale, img.height * scale);
                
                $('#existingSignature').hide();
                hasDrawn = true;
            };
            img.src = event.target.result;
        };
        reader.readAsDataURL(file);
    });

    $('#signatureForm').on('submit', function(e) {
        const blank = document.createElement('canvas');
        blank.width = canvas.width;
        blank.height = canvas.height;
        
        // If the user hasn't drawn anything new
        if (canvas.toDataURL() === blank.toDataURL()) {
            // But they already have a saved signature, maybe they just hit save without drawing
            // Do not submit empty unless they cleared
            if (hasDrawn) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Empty Signature',
                    text: 'Please draw your signature before saving.'
                });
            } else {
                e.preventDefault();
                Swal.fire({
                    icon: 'info',
                    title: 'No Changes',
                    text: 'You have not made any changes to your signature.'
                });
            }
        } else {
            $('#signatureData').val(canvas.toDataURL());
        }
    });
});
</script>
