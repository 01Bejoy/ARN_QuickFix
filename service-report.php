<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Create Service Report | Sonic Elevator Ltd.</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

  <style>
    :root{ --sonic:#00C2CB; }
    body{ background:#f7fbfc; }
    .card-soft{ border:1px solid rgba(15,23,42,.08); box-shadow:0 10px 30px rgba(2,8,23,.06); border-radius:16px; }
    .btn-sonic{ background:var(--sonic); border:none; font-weight:700; }
    .btn-sonic:hover{ background:#06aeb6; }
    .hint{ color:#64748b; }
  </style>
</head>

<body>
  <div class="container py-5">

    <div class="d-flex align-items-center justify-content-between mb-4">
      <div>
        <a href="technician-dashboard.html" class="text-decoration-none">
          <i class="fa-solid fa-arrow-left me-2"></i>Back to Dashboard
        </a>
        <h2 class="fw-bold mt-2 mb-0">Create Service Report</h2>
        <small class="text-secondary">For Request: SR-1024 • Elevator: ELV-DHK-21</small>
      </div>

      <a href="request-details.html" class="btn btn-outline-dark rounded-pill">
        <i class="fa-solid fa-folder-open me-2"></i>View Request
      </a>
    </div>

    <div class="row g-4">
      <div class="col-lg-8">
        <div class="card card-soft p-4">
          <form id="reportForm">

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Service Type</label>
                <select class="form-select" required>
                  <option value="">Select</option>
                  <option>Preventive Maintenance</option>
                  <option>Repair & Breakdown</option>
                  <option>Emergency Rescue</option>
                  <option>Safety Inspection</option>
                  <option>Modernization Support</option>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Status After Service</label>
                <select class="form-select" required>
                  <option value="">Select</option>
                  <option>Resolved</option>
                  <option>Temporarily Fixed</option>
                  <option>Needs Parts / Pending</option>
                  <option>Escalated to Senior Engineer</option>
                </select>
              </div>

              <div class="col-12">
                <label class="form-label fw-semibold">Root Cause</label>
                <textarea class="form-control" rows="3" placeholder="Example: Door sensor misalignment caused repeated open/close cycle." required></textarea>
              </div>

              <div class="col-12">
                <label class="form-label fw-semibold">Corrective Actions Taken</label>
                <textarea class="form-control" rows="4" placeholder="Example: Cleaned sensor, aligned bracket, tested 20 cycles, verified leveling & door timing." required></textarea>
              </div>

              <div class="col-12">
                <label class="form-label fw-semibold">Parts Used (if any)</label>
                <div class="row g-2">
                  <div class="col-md-5">
                    <input id="partName" type="text" class="form-control" placeholder="Part name (e.g., Door Sensor)">
                  </div>
                  <div class="col-md-3">
                    <input id="partQty" type="number" min="1" class="form-control" placeholder="Qty">
                  </div>
                  <div class="col-md-4 d-grid">
                    <button type="button" class="btn btn-outline-secondary" onclick="addPart()">
                      <i class="fa-solid fa-plus me-2"></i>Add Part
                    </button>
                  </div>
                </div>

                <div class="mt-3">
                  <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                      <thead>
                        <tr>
                          <th>Part</th>
                          <th style="width:120px;">Qty</th>
                          <th style="width:80px;"></th>
                        </tr>
                      </thead>
                      <tbody id="partsTable">
                        <tr class="text-secondary">
                          <td colspan="3">No parts added.</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <small class="hint d-block mt-2">Backend can later deduct parts from inventory automatically.</small>
              </div>

              <div class="col-12">
                <label class="form-label fw-semibold">Technician Notes</label>
                <textarea class="form-control" rows="3" placeholder="Optional notes for admin/management"></textarea>
              </div>

              <div class="col-12 d-grid">
                <button class="btn btn-sonic py-3" type="submit">
                  <i class="fa-solid fa-paper-plane me-2"></i>Submit Service Report
                </button>
              </div>
            </div>

          </form>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card card-soft p-4">
          <h5 class="fw-bold mb-2"><i class="fa-solid fa-circle-info me-2" style="color:#00C2CB;"></i>Quick Guide</h5>
          <ul class="mb-0 text-secondary">
            <li class="mb-2">Write the root cause clearly.</li>
            <li class="mb-2">Actions must be measurable (tested cycles, checked floors, etc.).</li>
            <li class="mb-2">Add parts used to track inventory.</li>
            <li>Submit report after job completion.</li>
          </ul>
        </div>

        <div class="card card-soft p-4 mt-4">
          <h6 class="fw-bold mb-2">Next Steps</h6>
          <a href="request-details.html" class="btn btn-outline-dark rounded-pill w-100 mb-2">
            <i class="fa-solid fa-clock-rotate-left me-2"></i>Update Timeline
          </a>
          <a href="maintenance-checklist.html" class="btn btn-outline-secondary rounded-pill w-100">
            <i class="fa-solid fa-list-check me-2"></i>Open Checklist
          </a>
        </div>
      </div>
    </div>

  </div>

<script>
  const parts = [];

  function renderParts(){
    const tbody = document.getElementById("partsTable");
    tbody.innerHTML = "";

    if(parts.length === 0){
      tbody.innerHTML = `<tr class="text-secondary"><td colspan="3">No parts added.</td></tr>`;
      return;
    }

    parts.forEach((p, i) => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td class="fw-semibold">${p.name}</td>
        <td>${p.qty}</td>
        <td class="text-end">
          <button type="button" class="btn btn-sm btn-outline-danger" onclick="removePart(${i})">
            <i class="fa-solid fa-trash"></i>
          </button>
        </td>
      `;
      tbody.appendChild(tr);
    });
  }

  function addPart(){
    const name = document.getElementById("partName").value.trim();
    const qty = parseInt(document.getElementById("partQty").value, 10);

    if(!name) return alert("Enter part name.");
    if(!qty || qty < 1) return alert("Enter valid quantity.");

    parts.push({name, qty});
    document.getElementById("partName").value = "";
    document.getElementById("partQty").value = "";
    renderParts();
  }

  function removePart(i){
    parts.splice(i,1);
    renderParts();
  }

  document.getElementById("reportForm").addEventListener("submit", function(e){
    e.preventDefault();
    alert("✅ Service report submitted (demo). Connect backend to save into database.");
    window.location.href = "technician-dashboard.html";
  });

  renderParts();
</script>

</body>
</html>