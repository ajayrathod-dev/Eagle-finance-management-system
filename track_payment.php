<?php
include("header.php");
?>

<div class="page-wrapper">
    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-4"> Generate UPI QR</h5>

                <form id="upiForm">
                    <div class="mb-3">
                        <label class="form-label">UPI ID</label>
                        <input type="text" id="upiId" class="form-control" placeholder="example@upi" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Amount (₹)</label>
                        <input type="number" id="amount" class="form-control" placeholder="Enter Amount" required>
                    </div>

                    <button type="button" class="btn btn-primary" onclick="generateQR()">Generate QR</button>
                </form>

                <div class="text-center mt-4">
                    <canvas id="qrcode"></canvas>
                    <div id="qrText" class="mt-2 text-muted"></div>
                </div>

                <div class="mt-3 d-flex justify-content-center gap-3">
                    <button id="downloadBtn" class="btn btn-success" style="display:none;">Download QR</button>
                    <a id="whatsappBtn" class="btn btn-success" target="_blank" style="display:none;">Share on WhatsApp</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
<script>
    let qr;

    function generateQR() {
        const upiId = document.getElementById("upiId").value.trim();
        const amount = document.getElementById("amount").value.trim();

        if (!upiId) {
            alert("Please enter a UPI ID");
            return;
        }

        let upiUrl = `upi://pay?pa=${encodeURIComponent(upiId)}&am=${encodeURIComponent(amount)}&cu=INR`;

        if (!qr) {
            qr = new QRious({
                element: document.getElementById("qrcode"),
                size: 250,
                value: upiUrl
            });
        } else {
            qr.value = upiUrl;
        }

        document.getElementById("qrText").innerText = upiUrl;

        document.getElementById("downloadBtn").style.display = "inline-block";
        document.getElementById("whatsappBtn").style.display = "inline-block";

        // Download QR
        document.getElementById("downloadBtn").onclick = function() {
            const link = document.createElement('a');
            link.href = document.getElementById("qrcode").toDataURL("image/png");
            link.download = "upi_qr.png";
            link.click();
        };

        // WhatsApp Share
        document.getElementById("whatsappBtn").href = `https://wa.me/?text=Pay via UPI: ${upiUrl}`;
    }
</script>

<?php
include("footer.php");
?>
