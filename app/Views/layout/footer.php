<!-- Global QR & Share Modal -->
<div class="modal fade" id="qrShareModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold" id="qrModalTitle">Share Job</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center pt-2 pb-4">
        <p class="text-muted small mb-3">Scan this code to view the public certification portal.</p>
        <div id="qrcode-container" class="d-flex justify-content-center mb-4 p-3 bg-white rounded-3 shadow-sm border" style="min-height: 200px;"></div>
        
        <div class="d-grid gap-2">
            <button class="btn btn-outline-primary rounded-pill fw-bold" id="btnCopyShareLink">
                <i class="fa-solid fa-link me-2"></i>Copy Link
            </button>
            <a href="#" id="btnOpenShareLink" target="_blank" class="btn btn-light rounded-pill fw-bold text-dark">
                <i class="fa-solid fa-arrow-up-right-from-square me-2"></i>Open in New Tab
            </a>
        </div>
      </div>
    </div>
  </div>
</div>

</div> <!-- End col-md-9 workspace -->
</div> <!-- End row g-0 -->
</div> <!-- End container-fluid -->

<!-- Bootstrap 5 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- QRCode.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    let currentShareUrl = '';

    function showQrModal(jobIdCode, qrToken) {
        currentShareUrl = '<?php echo BASE_URL; ?>/public/view/' + qrToken;
        
        document.getElementById('qrModalTitle').innerText = 'Share: ' + jobIdCode;
        document.getElementById('btnOpenShareLink').href = currentShareUrl;
        
        // Clear previous QR code
        const qrContainer = document.getElementById('qrcode-container');
        qrContainer.innerHTML = '';
        
        // Generate new QR Code
        new QRCode(qrContainer, {
            text: currentShareUrl,
            width: 180,
            height: 180,
            colorDark : "#0f172a",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });

        // Show Modal
        const modal = new bootstrap.Modal(document.getElementById('qrShareModal'));
        modal.show();
    }

    document.getElementById('btnCopyShareLink').addEventListener('click', function() {
        navigator.clipboard.writeText(currentShareUrl).then(() => {
            const btn = this;
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-check me-2"></i>Copied!';
            btn.classList.replace('btn-outline-primary', 'btn-success');
            btn.classList.replace('text-primary', 'text-white');
            
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.classList.replace('btn-success', 'btn-outline-primary');
            }, 2000);
        });
    });
</script>
</body>
</html>
