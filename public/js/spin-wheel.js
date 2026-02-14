(function () {
    "use strict";

    var gameState = window.gameState || {
        userPhone: "",
        userPhoneDisplay: "",
        hasSpun: false,
        isSpinning: false,
        wheel: null,
        animationFrameId: null,
        prizes: [],
        lastWinner: null,
        spinsCount: 0,
        winnersCount: 0,
    };

    var defaultPrizes = [
        {
            id: 1,
            label: "استشارة مجانية",
            backgroundColor: "#0d9488",
            labelColor: "#fff",
            probability_weight: 3,
        },
        {
            id: 2,
            label: "فحص مجاني",
            backgroundColor: "#2563eb",
            labelColor: "#fff",
            probability_weight: 3,
        },
        {
            id: 3,
            label: "إجراء مجاني",
            backgroundColor: "#7c3aed",
            labelColor: "#fff",
            probability_weight: 1,
        },
        {
            id: 4,
            label: "خصم 50%",
            backgroundColor: "#c026d3",
            labelColor: "#fff",
            probability_weight: 2,
        },
        {
            id: 5,
            label: "عروض حصرية",
            backgroundColor: "#dc2626",
            labelColor: "#fff",
            probability_weight: 2,
        },
    ];

    var phoneInput = document.getElementById("phoneInput");
    var phoneError = document.getElementById("phoneError");
    var phoneSuccess = document.getElementById("phoneSuccess");
    var startGameBtn = document.getElementById("startGameBtn");
    var phoneSection = document.getElementById("phoneSection");
    var wheelSection = document.getElementById("wheelSection");
    var wheelEl = document.getElementById("wheelEl");
    var logoRef = document.getElementById("logoRef");
    var spinBtn = document.getElementById("spinBtn");
    var spinBtnText = document.getElementById("spinBtnText");
    var resultModal = document.getElementById("resultModal");
    var resultDisplay = document.getElementById("resultDisplay");
    var resultPhoneNumber = document.getElementById("resultPhoneNumber");
    var claimResultBtn = document.getElementById("claimResultBtn");
    var whatsappBtn = document.getElementById("whatsappBtn");
    var saveResultForm = document.getElementById("saveResultForm");
    var userPhoneDisplayEl = document.getElementById("userPhoneDisplay");
    var spinsCountDisplay = document.getElementById("spinsCountDisplay");
    var winnersCountDisplay = document.getElementById("winnersCountDisplay");

    function validatePhone(phone) {
        var digits = phone.replace(/\D/g, "");
        return /^05\d{8}$/.test(digits);
    }

    function updatePhoneValidation() {
        var rawValue = (phoneInput && phoneInput.value) || "";
        var digits = rawValue.replace(/\D/g, "");
        var isValid = validatePhone(digits);

        if (phoneError) phoneError.style.display = "none";

        if (phoneSuccess) phoneSuccess.style.display = "none";

        if (digits.length > 0) {
            if (isValid) {
                if (phoneSuccess) {
                    phoneSuccess.style.display = "block";
                }
                if (startGameBtn) {
                    startGameBtn.disabled = false;
                    startGameBtn.style.opacity = "1";
                    startGameBtn.style.pointerEvents = "auto";
                }
            } else {
                // Only show error if they've typed enough to potentially be a full number
                // or if they are clearly starting wrong (not with 05)
                if (
                    digits.length >= 10 ||
                    (digits.length >= 2 && !digits.startsWith("05"))
                ) {
                    if (phoneError) {
                        phoneError.textContent =
                            "الرقم يجب أن يبدأ بـ 05 ويتكون من 10 أرقام";
                        phoneError.style.display = "block";
                    }
                }
                if (startGameBtn) {
                    startGameBtn.disabled = true;
                    // Don't modify opacity if we want to keep the design consistent,
                    // but sometimes mobile needs a redraw
                }
            }
        } else {
            if (startGameBtn) startGameBtn.disabled = true;
        }
    }

    function updateStats() {
        if (spinsCountDisplay)
            spinsCountDisplay.textContent = gameState.spinsCount;
        var baseWinners =
            typeof window.winnersCount !== "undefined"
                ? window.winnersCount
                : 0;
        if (winnersCountDisplay)
            winnersCountDisplay.textContent =
                baseWinners + gameState.winnersCount;
    }

    function setWhatsAppLink(phone, message) {
        if (!whatsappBtn) return;
        var num = (window.whatsappNumber || "905357176133").replace(/\D/g, "");
        var text = encodeURIComponent(
            message ||
                "مرحباً، أريد استلام جائزتي من دولاب الحظ. رقمي: " +
                    (phone || ""),
        );
        whatsappBtn.href = "https://wa.me/" + num + "?text=" + text;
    }

    function savePhoneToServer(phone) {
        var token = document.querySelector('meta[name="csrf-token"]')
            ? document
                  .querySelector('meta[name="csrf-token"]')
                  .getAttribute("content")
            : "";
        var formData = new FormData();
        formData.append("phone", phone.replace(/\D/g, ""));
        formData.append("_token", token || "");

        return fetch("/spin-wheel/start", {
            method: "POST",
            body: formData,
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
            },
        }).then(function (res) {
            if (!res.ok) {
                return res.json().then(function (data) {
                    var msg =
                        (data && data.message) ||
                        (data &&
                            data.errors &&
                            data.errors.phone &&
                            data.errors.phone[0]) ||
                        "فشل في التسجيل";
                    throw new Error(msg);
                });
            }
            return res.json();
        });
    }

    function startGame() {
        var phone = phoneInput
            ? phoneInput.value.trim().replace(/\D/g, "")
            : "";
        var btnTextEl = startGameBtn
            ? startGameBtn.querySelector(".btn-text")
            : null;

        if (!validatePhone(phoneInput ? phoneInput.value.trim() : "")) {
            if (phoneError) {
                phoneError.textContent =
                    "الرقم يجب أن يبدأ بـ 05 ويتكون من 10 أرقام (05xxxxxxxx)";
                phoneError.style.display = "block";
            }
            return;
        }

        if (startGameBtn && btnTextEl) {
            startGameBtn.disabled = true;
            btnTextEl.textContent = "جاري التسجيل...";
        }

        savePhoneToServer(phone)
            .then(function () {
                gameState.userPhone = phone;
                gameState.userPhoneDisplay = phone;

                if (phoneSection) {
                    phoneSection.classList.add("spin-screen-hidden");
                    phoneSection.classList.remove("spin-screen-visible");
                }
                if (wheelSection) {
                    wheelSection.classList.add("spin-screen-visible");
                    wheelSection.style.display = "";
                }

                if (userPhoneDisplayEl)
                    userPhoneDisplayEl.textContent =
                        gameState.userPhoneDisplay || gameState.userPhone;
                updateStats();

                gameState.prizes =
                    window.wheelItemsFromBlade &&
                    window.wheelItemsFromBlade.length > 0
                        ? window.wheelItemsFromBlade
                        : defaultPrizes;

                setTimeout(initializeWheel, 100);
            })
            .catch(function (err) {
                if (phoneError) {
                    phoneError.textContent = err.message || "فشل في التسجيل";
                    phoneError.style.display = "block";
                }
                if (startGameBtn && btnTextEl) {
                    startGameBtn.disabled = false;
                    btnTextEl.textContent = "ابدأ الآن";
                }
            });
    }

    function pickWinnerByWeight(items) {
        var total = 0;
        for (var i = 0; i < items.length; i++) {
            total += items[i].probability_weight || 1;
        }
        var r = Math.random() * total;
        for (var j = 0; j < items.length; j++) {
            var w = items[j].probability_weight || 1;
            if (r < w) return j;
            r -= w;
        }
        return 0;
    }

    function initializeWheel() {
        if (!wheelEl) return;

        var prizes = gameState.prizes || defaultPrizes;
        var wheelItems = prizes.map(function (p) {
            return {
                label: p.label,
                backgroundColor: p.backgroundColor || "#2ecc71",
                labelColor: p.labelColor || "#fff",
                weight: p.probability_weight || 1,
            };
        });

        var Wheel = window.spinWheel && window.spinWheel.Wheel;
        if (!Wheel) {
            console.error("Wheel not found");
            return;
        }

        gameState.wheel = new Wheel(wheelEl, {
            items: wheelItems,
            itemLabelFont: "900 400px Cairo",
            itemLabelRadius: 0.5,
            itemLabelRadiusMax: 0.9,
            radius: 0.95,
            pointerAngle: 0,
            isInteractive: false,
            rotationResistance: -50,
            onRest: function (item) {
                gameState.isSpinning = false;
                if (gameState.animationFrameId) {
                    cancelAnimationFrame(gameState.animationFrameId);
                    gameState.animationFrameId = null;
                }

                var winnerIndex = item.currentIndex;
                var winnerItem = prizes[winnerIndex] || prizes[0];
                gameState.hasSpun = true;
                gameState.lastWinner = winnerItem;
                gameState.spinsCount += 1;
                var isWinner = winnerItem.is_winner === true;
                if (isWinner) gameState.winnersCount += 1;
                updateStats();

                if (spinBtn) {
                    spinBtn.disabled = true;
                    if (spinBtnText)
                        spinBtnText.textContent = "اكتملت المحاولة";
                }

                saveResultThenShowModal(winnerItem);
            },
        });
    }

    function updateLogoRotation() {
        if (gameState.wheel && logoRef) {
            var rot = gameState.wheel.rotation % 360;
            logoRef.style.transform =
                "translate(-50%, -50%) rotate(" + rot + "deg)";
            if (gameState.isSpinning) {
                gameState.animationFrameId =
                    requestAnimationFrame(updateLogoRotation);
            }
        }
    }

    function spin() {
        if (gameState.isSpinning || gameState.hasSpun || !gameState.wheel)
            return;

        gameState.isSpinning = true;
        if (spinBtn) spinBtn.disabled = true;
        if (spinBtnText) spinBtnText.textContent = "جاري الدوران...";

        updateLogoRotation();

        var prizes = gameState.prizes || defaultPrizes;
        var winnerIndex = pickWinnerByWeight(prizes);
        var count = prizes.length;
        var segment = 360 / count;
        var fullTurns = 6 * 360;
        var centerOfSegment = winnerIndex * segment + segment / 2;
        var jitter = (Math.random() - 0.5) * Math.min(12, segment * 0.5);
        var targetAngle = Math.floor(fullTurns + centerOfSegment + jitter);

        gameState.wheel.spin(targetAngle);
    }

    function saveResultThenShowModal(winnerItem) {
        var token = document.querySelector('meta[name="csrf-token"]')
            ? document
                  .querySelector('meta[name="csrf-token"]')
                  .getAttribute("content")
            : "";
        var formData = new FormData();
        formData.append("prize_id", winnerItem.id || winnerItem.prize_id || 0);
        formData.append("_token", token || "");

        fetch(
            saveResultForm
                ? saveResultForm.getAttribute("action")
                : "/spin-wheel/save-result",
            {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                },
            },
        )
            .then(function (res) {
                if (!res.ok)
                    return res.json().then(function () {
                        throw new Error("فشل حفظ النتيجة");
                    });
                return res.json();
            })
            .then(function () {
                showResultModal(winnerItem);
            })
            .catch(function () {
                showResultModal(winnerItem);
            });
    }

    function showResultModal(winnerItem) {
        if (resultDisplay) resultDisplay.textContent = winnerItem.label;
        if (resultPhoneNumber)
            resultPhoneNumber.textContent =
                gameState.userPhoneDisplay || gameState.userPhone;
        if (resultModal) {
            /* Small delay so user sees wheel land on winning segment before popup */
            setTimeout(function () {
                var Modal = window.bootstrap && window.bootstrap.Modal;
                if (Modal) {
                    var modal = new Modal(resultModal, {
                        backdrop: "static",
                        keyboard: false,
                    });
                    modal.show();
                } else {
                    resultModal.classList.add("show");
                    resultModal.style.display = "block";
                    resultModal.setAttribute("aria-modal", "true");
                    document.body.classList.add("modal-open");
                }
            }, 400);
        }
    }

    function closeResultModal() {
        if (!resultModal) return;
        var Modal = window.bootstrap && window.bootstrap.Modal;
        var instance = Modal.getInstance(resultModal);
        if (instance) instance.hide();
    }

    function onClaimResult() {
        closeResultModal();
    }

    if (phoneInput) {
        phoneInput.addEventListener("input", updatePhoneValidation);
        phoneInput.addEventListener("keyup", function (e) {
            if (e.key === "Enter") {
                e.preventDefault();
                startGame();
            }
        });

        // Prevent any accidental form submission on focus/click
        phoneInput.addEventListener("focus", function (e) {
            e.stopPropagation();
        });

        phoneInput.addEventListener("click", function (e) {
            e.stopPropagation();
        });
    }

    if (startGameBtn) {
        startGameBtn.addEventListener("click", function (e) {
            e.preventDefault();
            startGame();
        });
    }

    if (spinBtn) {
        spinBtn.addEventListener("click", spin);
    }

    if (claimResultBtn) {
        claimResultBtn.addEventListener("click", onClaimResult);
    }

    var closeResultModalBtn = document.getElementById("closeResultModalBtn");
    if (closeResultModalBtn) {
        closeResultModalBtn.addEventListener("click", closeResultModal);
    }

    document.addEventListener("DOMContentLoaded", function () {
        // Ensure phone input is never disabled or readonly
        if (phoneInput) {
            phoneInput.disabled = false;
            phoneInput.readOnly = false;
            phoneInput.removeAttribute("disabled");
            phoneInput.removeAttribute("readonly");
            phoneInput.removeAttribute("autocomplete");
            phoneInput.setAttribute("autocomplete", "off");

            // Force hide validation messages initially
            if (phoneError) phoneError.style.display = "none";
            if (phoneSuccess) phoneSuccess.style.display = "none";

            // Ensure button is disabled initially
            if (startGameBtn) {
                startGameBtn.disabled = true;
            }

            // Detect and handle potential autofill without clearing user-typed content
            phoneInput.addEventListener("animationstart", function (e) {
                if (e.animationName === "onAutoFillStart") {
                    updatePhoneValidation();
                }
            });

            // Initial validation check (in case of browser-restored values)
            setTimeout(updatePhoneValidation, 500);
        }
    });

    window.gameState = gameState;
})();
