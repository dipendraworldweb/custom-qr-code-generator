"use strict";
document.addEventListener("DOMContentLoaded", function () {
    const embeds = document.querySelectorAll(".custom-embed a[data-embed-code]");
    const siteUrl = document.querySelector(".custom-embed a[data-site-url]");
    let siteUrlPath = siteUrl.getAttribute("data-site-url");

    if (embeds.length === 0) return;

    setTimeout(() => {
        embeds.forEach((embed) => {
            if (!embed.parentNode) {
                console.error("Parent node missing for embed:", embed);
                return;
            }

            let encodedEmbedCode = embed.getAttribute("data-embed-code");
            let nonce = embed.parentNode.getAttribute("data-nonce");

            if (!encodedEmbedCode) {
                console.error("QR Code URL is missing.");
                return;
            }

            // Loader setup
            const loader = document.createElement("div");
            loader.style.cssText = `
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                height: 400px;
                width: 100%;
                background: rgba(255, 255, 255, 0.7);
                backdrop-filter: blur(8px);
                border-radius: 12px;
            `;

            const spinner = document.createElement("div");
            spinner.style.cssText = `
                width: 60px;
                height: 60px;
                border: 6px solid rgba(0,0,0,0.1);
                border-top: 6px solid #007bff;
                border-radius: 50%;
                animation: spin 1s linear infinite;
            `;

            const loadingText = document.createElement("div");
            loadingText.innerText = "Generating QR Code... Please wait";
            loadingText.style.cssText = `
                margin-top: 20px;
                font-size: 16px;
                color: #555;
            `;

            loader.append(spinner, loadingText);
            embed.parentNode.appendChild(loader);

            const style = document.createElement("style");
            style.textContent = `
                @keyframes spin {
                    from { transform: rotate(0deg); }
                    to { transform: rotate(360deg); }
                }
            `;
            document.head.appendChild(style);

            // Fetch QR Code
            let attempt = 0;
            const maxRetries = 4;

            function fetchQRCode() {
                fetch(`${siteUrlPath}/wp-json/cqrc/v1/get-qr-code/`, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ hash: encodedEmbedCode, _ajax_nonce: nonce })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.qrcode_url) {
                        createImage(embed, data.qrcode_url);
                        loader.remove();
                    } else {
                        retryFetch();
                    }
                })
                .catch(() => retryFetch());
            }

            function createImage(target, qrCodeUrl) {
                const img = document.createElement("img");
                img.src = qrCodeUrl;
                img.style.width = "100%";
                img.style.height = "auto";
                img.style.maxHeight = "400px";
                img.style.objectFit = "contain";

                // Replace the original embed with the image
                if (target.parentNode) {
                    target.parentNode.replaceChild(img, target);
                }
            }

            function retryFetch() {
                attempt++;
                if (attempt <= maxRetries) {
                    loadingText.innerText = `Retrying... Attempt ${attempt}/${maxRetries}`;
                    setTimeout(fetchQRCode, 2000);
                } else {
                    loadingText.innerText = "Failed to load QR Code. Please refresh or try again later.";
                    spinner.style.display = "none";
                    console.error("Max retries reached. QR code could not be loaded.");
                }
            }

            fetchQRCode();
        });
    }, 100);
});