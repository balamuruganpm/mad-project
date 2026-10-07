<?php
// app/Views/dashboard/guide.php
?>
<div class="row align-items-center mb-4">
    <div class="col">
        <h2 class="fw-bold mb-0 text-gradient" style="font-size: 2.2rem; font-weight: 800; letter-spacing: -1px;">System Documentation & Manual</h2>
        <p class="text-muted" style="font-weight: 500;">Learn how to navigate and manage the Megawin Quality Management System.</p>
    </div>
</div>

<div class="row g-4">
    <!-- Navigation Cards (Left Column) -->
    <div class="col-lg-4">
        <div class="card-custom mb-4">
            <h5 class="fw-bold mb-3"><i class="fa-solid fa-compass me-2 text-primary"></i>Guide Directory</h5>
            <div class="list-group list-group-flush gap-1">
                <a href="#quickstart" class="list-group-item list-group-item-action border-0 py-2.5 rounded-3 fw-semibold small text-secondary bg-transparent"><i class="fa-solid fa-star me-2 text-warning"></i>Quick Start Guide</a>
                <a href="#formbuilder" class="list-group-item list-group-item-action border-0 py-2.5 rounded-3 fw-semibold small text-secondary bg-transparent"><i class="fa-solid fa-square-plus me-2 text-primary"></i>Visual Template Builder</a>
                <a href="#workflow" class="list-group-item list-group-item-action border-0 py-2.5 rounded-3 fw-semibold small text-secondary bg-transparent"><i class="fa-solid fa-rotate me-2 text-purple" style="color: #8b5cf6;"></i>Workflow Approvals</a>
                <a href="#certificates" class="list-group-item list-group-item-action border-0 py-2.5 rounded-3 fw-semibold small text-secondary bg-transparent"><i class="fa-solid fa-award me-2 text-success"></i>Quality Certificates</a>
            </div>
        </div>

        <div class="card-custom" style="border-left: 5px solid #3b82f6;">
            <h6 class="fw-bold text-primary mb-2"><i class="fa-solid fa-terminal me-2"></i>System Architecture</h6>
            <p class="text-muted small mb-0">Megawin QMS is built using a custom PHP 8.3 MVC framework with pure CSS/JS for optimal rendering speeds, completely decoupled from dependencies on heavy external framework runtimes.</p>
        </div>
    </div>

    <!-- Manual Contents (Right Column) -->
    <div class="col-lg-8">
        <!-- Quick Start -->
        <div id="quickstart" class="card-custom mb-4">
            <h4 class="fw-bold mb-3 text-gradient" style="letter-spacing: -0.5px;"><i class="fa-solid fa-rocket me-2 text-primary"></i>Quick Start Guide</h4>
            <p class="text-secondary small">Follow this workflow to execute an inspection job from creation to clearance:</p>
            
            <div class="d-flex flex-column gap-3 mt-3">
                <div class="d-flex gap-3 align-items-start">
                    <div class="p-2 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.85rem;">1</div>
                    <div>
                        <h6 class="fw-bold mb-1">Create an Inspection Job</h6>
                        <p class="text-muted small mb-0">Navigate to <strong>Job Management</strong> and click <strong>Create New Job</strong>. Choose the customer, template form, and assign a QC engineer.</p>
                    </div>
                </div>
                <div class="d-flex gap-3 align-items-start">
                    <div class="p-2 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.85rem;">2</div>
                    <div>
                        <h6 class="fw-bold mb-1">Perform QC checks & Sign</h6>
                        <p class="text-muted small mb-0">The assigned QC engineer clicks <strong>Inspect</strong>, fills out all parameters on a tablet or computer, signs the signature board, and submits the results.</p>
                    </div>
                </div>
                <div class="d-flex gap-3 align-items-start">
                    <div class="p-2 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.85rem;">3</div>
                    <div>
                        <h6 class="fw-bold mb-1">Manager Review & Conformance Approval</h6>
                        <p class="text-muted small mb-0">The Manager reviews the locked inspection results. If satisfied, they click <strong>Approve Conformance</strong> to lock the record and generate a Conformance Pass Certificate.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Builder -->
        <div id="formbuilder" class="card-custom mb-4">
            <h4 class="fw-bold mb-3 text-gradient" style="letter-spacing: -0.5px;"><i class="fa-solid fa-square-plus me-2 text-primary"></i>Visual Template Builder</h4>
            <p class="text-secondary small">Admins can configure dynamic forms without writing SQL schemas or PHP code. Custom attributes added inside the template builder automatically serialize into the QC template checklist.</p>
            
            <div class="border rounded-4 bg-light p-3 mt-3">
                <h6 class="fw-bold mb-2 text-secondary"><i class="fa-solid fa-circle-info me-2"></i>Field Properties Guide:</h6>
                <ul class="text-muted small mb-0 ps-3">
                    <li class="mb-1"><strong>Field Label:</strong> The user-friendly label shown to the engineer (e.g. <code>Insulation Resistance (MΩ)</code>).</li>
                    <li class="mb-1"><strong>Variable Key:</strong> A unique database key identifier (automatically generated in lowercase and snake_case based on label).</li>
                    <li class="mb-1"><strong>Input Type:</strong> Short Text (for serials/ratings), Numeric Value (for measurements), or Paragraph/Remarks.</li>
                </ul>
            </div>
        </div>

        <!-- Workflow Approvals -->
        <div id="workflow" class="card-custom mb-4">
            <h4 class="fw-bold mb-3 text-gradient" style="letter-spacing: -0.5px;"><i class="fa-solid fa-rotate me-2 text-purple" style="color: #8b5cf6;"></i>Workflow Approvals</h4>
            <p class="text-secondary small">Every inspection transitions through specific states to guarantee audit conformance:</p>
            
            <div class="table-responsive mt-3">
                <table class="table table-bordered table-sm align-middle small text-muted">
                    <thead>
                        <tr class="table-light">
                            <th>State</th>
                            <th>Description</th>
                            <th>Permitted Actor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="badge bg-secondary">Assigned</span></td>
                            <td>Job is created and allocated. Engineer needs to perform the inspection.</td>
                            <td>Super Admin / Manager</td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-warning text-dark">Pending review</span></td>
                            <td>Engineer has completed metrics, signed off, and pushed the job to review.</td>
                            <td>QC Engineer</td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-success">Completed</span></td>
                            <td>Manager approved the report. Data is permanently locked, and certificate is available.</td>
                            <td>QC Manager</td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-danger">Rework Requested</span></td>
                            <td>Manager rejected the report. Sent back to engineer with feedback comments.</td>
                            <td>QC Manager</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Certificates -->
        <div id="certificates" class="card-custom">
            <h4 class="fw-bold mb-3 text-gradient" style="letter-spacing: -0.5px;"><i class="fa-solid fa-award me-2 text-success"></i>QC Conformance Certificates</h4>
            <p class="text-secondary small">For finalized jobs, the system generates an official A4-size conforming document:</p>
            
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <div class="border rounded-4 p-3 bg-light h-100">
                        <h6 class="fw-bold small mb-2"><i class="fa-solid fa-qrcode me-2 text-primary"></i>QR Verification Code</h6>
                        <p class="text-muted small mb-0">Certificates are stamped with a verification QR code. Customers or dispatch coordinators scan it to load verified calibration values on the public verification portal.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded-4 p-3 bg-light h-100">
                        <h6 class="fw-bold small mb-2"><i class="fa-solid fa-print me-2 text-success"></i>A4 PDF Printing</h6>
                        <p class="text-muted small mb-0">Use the integrated print button to export conforming certificates directly into clean PDF documents using system print layouts (margins and headers are optimized for A4 portrait).</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .list-group-item-action:hover {
        background-color: rgba(59, 130, 246, 0.05) !important;
        color: #3b82f6 !important;
    }
</style>
