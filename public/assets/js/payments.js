$(document).ready(function () {
  // State to hold current payment details
  let currentPayment = {};

  // Helper function for currency formatting (Philippines Peso)
  function formatCurrency(amount) {
    // Assuming amount is an integer (e.g., 2999) and needs to be formatted to ₱2,999
    return "₱" + new Intl.NumberFormat("en-PH").format(amount);
  }

  // Function to display an alert message
  function showAlert(message, type = "info") {
    const alertClass =
      {
        success: "bg-green-500",
        error: "bg-red-500",
        warning: "bg-yellow-500",
        info: "bg-blue-500",
      }[type] || "bg-blue-500";

    const alert = $(`
        <div class="alert ${alertClass} text-white px-6 py-4 rounded-lg shadow-lg flex items-center justify-between fadeIn">
          <span>${message}</span>
            <button class="text-white hover:text-gray-200 ml-4 opacity-75 hover:opacity-100 transition-opacity">&times;</button>
        </div>
    `);

    alert.find("button").on("click", function () {
      alert.slideUp(300, function () {
        $(this).remove();
      });
    });

    $("#alertContainer").append(alert);

    if (type !== "error") {
      setTimeout(() => {
        alert.slideUp(300, function () {
          $(this).remove();
        });
      }, 4000);
    }
  }

  // Function to close a specific modal
  function closeModal(modalId) {
    $(modalId).removeClass("show");
    if (
      $("#paymentModal").hasClass("show") === false &&
      $("#paymentDetailsModal").hasClass("show") === false
    ) {
      $("body").css("overflow", "auto");
    }
    $("#paymentMessage").addClass("hidden").empty();
    $("#detailsMessage").addClass("hidden").empty();
  }

  // ===== MODAL 1: OPEN PAYMENT MODAL (Pay Now) =====
  $("#btnPayNow").on("click", function () {
    // Store data in the state
    currentPayment.subscription_id = $(this).data("subscription-id");
    currentPayment.amount = $(this).data("amount");
    currentPayment.plan_name = $(this).data("plan");

    $("#modalSubscriptionId").text(currentPayment.subscription_id);
    $("#modalPlanName").text(currentPayment.plan_name);
    $("#modalAmount").text(formatCurrency(currentPayment.amount));

    $("#paymentModal").addClass("show");
    $("body").css("overflow", "hidden");
  });

  // ===== MODAL 1: CLOSE MODAL HANDLERS =====
  $(".modal-close, .modal-cancel").on("click", function () {
    closeModal("#paymentModal");
  });

  $("#paymentModal").on("click", function (e) {
    if ($(e.target).is(this)) {
      closeModal("#paymentModal");
    }
  });

  // ===== MODAL 1: PROCEED TO PAYMENT (Moves to Modal 2) =====
  $("#btnProceedPayment").on("click", function () {
    const selectedMethod = $('input[name="payment_method"]:checked').val();

    if (!selectedMethod) {
      showPaymentMessage("Please select a payment method.", "error");
      return;
    }

    // Store selected method
    currentPayment.payment_method = selectedMethod;

    // Close Modal 1
    closeModal("#paymentModal");

    // Open Modal 2 and populate fields
    $('#detailsAmount').text(formatCurrency(currentPayment.amount));
    $("#detailsMethod").text(
      selectedMethod.charAt(0).toUpperCase() +
        selectedMethod
          .slice(1)
          .replace("maya", "Maya")
          .replace("bank", "Bank Transfer")
    );

    $("#form_subscription_id").val(currentPayment.subscription_id);
    $("#form_amount").val(currentPayment.amount);
    $("#form_payment_method").val(selectedMethod);

    // Show the relevant form section and clear previous inputs
    $(".payment-form-section").addClass("hidden").find("input").val("");
    $(`#${selectedMethod}Form`).removeClass("hidden");

    // Open Modal 2
    $("#paymentDetailsModal").addClass("show");
    $("body").css("overflow", "hidden");
  });

  // ===== MODAL 2: CLOSE MODAL HANDLERS (Payment Details Modal) =====
  $(".payment-details-close").on("click", function () {
    closeModal("#paymentDetailsModal");
  });

  $("#paymentDetailsModal").on("click", function (e) {
    if ($(e.target).is(this)) {
      closeModal("#paymentDetailsModal");
    }
  });

  // ===== MODAL 2: BACK BUTTON (Goes back to Modal 1) =====
  $(".payment-details-back").on("click", function () {
    closeModal("#paymentDetailsModal");
    $("#paymentModal").addClass("show");
    $("body").css("overflow", "hidden");
  });

  // Highlight selected payment method
  $('input[name="payment_method"]')
    .on("change", function () {
      $(".payment-method-option")
        .removeClass("border-blue-500 border-2 bg-blue-900/20")
        .addClass("border-gray-600");
      $(this)
        .closest(".payment-method-option")
        .addClass("border-blue-500 border-2 bg-blue-900/20")
        .removeClass("border-gray-600");
      $("#paymentMessage").addClass("hidden").empty();
    })
    .filter(":checked")
    .trigger("change"); // Apply initial styling
  // Function to show a message within the Payment Details Modal (kept as is)
  function showDetailsMessage(message, type) {
    const messageDiv = $("#detailsMessage");
    const bgColor =
      type === "error"
        ? "bg-red-900/30 border-red-600"
        : "bg-green-900/30 border-green-600";

    messageDiv
      .html(
        `
                    <div class="p-3 rounded-lg ${bgColor} text-white text-sm border">
                        ${message}
                    </div>
                `
      )
      .removeClass("hidden");

    if (type !== "error") {
      setTimeout(() => {
        messageDiv.addClass("hidden").empty();
      }, 3000);
    }
  }
  // ===== MODAL 2: CONFIRM PAYMENT SUBMISSION (AJAX Simulation) =====
  $("#paymentDetailsForm").on("submit", function (e) {
    e.preventDefault();

    // 1. Client-side Validation
    const formInputs = $(this)
      .find(`#${currentPayment.payment_method}Form input:not([type="hidden"])`)
      .filter(":visible")
      .not('[type="file"]');
    let isValid = true;
    formInputs.each(function () {
      // Simple check for empty value
      if (!$(this).val()) {
        isValid = false;
        $(this).addClass("border-red-500");
      } else {
        $(this).removeClass("border-red-500");
      }
    });

    if (!isValid) {
      showDetailsMessage("Please fill in all required fields.", "error");
      return;
    }

    const form = $(this);

    const serializedData = form.serialize();

    const submitBtn = $("#btnConfirmPayment");
    const originalText = submitBtn.text();

    // Show loading spinner and disable button
    submitBtn
      .html(
        '<span class="loading inline-block w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></span> Processing...'
      )
      .prop("disabled", true);

    // 2. Perform the AJAX Request to the MVC Controller
    $.ajax({
      type: "POST",
      url: "index.php?controller=Payment&action=processPayment",
      data: serializedData, // Data from all hidden and visible inputs
      dataType: "json",

      // 3. Success Handler (HTTP 200)
      success: function (response) {
        if (response.success) {
          submitBtn.html(originalText).prop("disabled", false);
          closeModal("#paymentDetailsModal");
          showDetailsMessage(
            `✅ ${response.message} (Transaction ID: ${response.transaction_id})`,
            "success"
          );
          showAlert(
            `Payment for ${currentPayment.subscription_id} successful!`,
            "success"
          );

          // Reload the page to show updated status/history
          setTimeout(() => {
            window.location.reload();
          }, 2000);
        } else {
          // Handle payment failure returned by the PHP script
          showDetailsMessage(`Payment Failed: ${response.message}`, "error");
          submitBtn.html(originalText).prop("disabled", false); // Restore button
        }
      },

      // 4. Error Handler (HTTP 4xx, 5xx, or network error)
      error: function (xhr, status, error) {
        // Attempt to read the custom error message from the PHP response
        let errorMessage = "An unknown error occurred.";
        try {
          // This is essential for catching custom PHP error responses (like 400 Bad Request)
          const responseJson = JSON.parse(xhr.responseText);
          errorMessage = responseJson.message || errorMessage;
        } catch (e) {
          // Use status text if JSON parsing fails (e.g., network error or PHP fatal error)
          errorMessage = `Server Error (${xhr.status} ${xhr.statusText}).`;
        }

        showDetailsMessage(`Payment Error: ${errorMessage}`, "error");
        submitBtn.html(originalText).prop("disabled", false); // Restore button
      },
    });
  });
});
