// Alert Function
function showAlert(message, type) {
  const alertClass = type === "success" ? "bg-green-500" : "bg-red-500";
  const icon = type === "success" ? "✓" : "✕";

  const alert = `
          <div class="alert ${alertClass} text-white px-6 py-4 rounded-lg shadow-lg flex items-center">
              <span class="mr-3 text-2xl">${icon}</span>
              <span>${message}</span>
          </div>
      `;

  $("#alertContainer").append(alert);

  setTimeout(() => {
    $("#alertContainer .alert")
      .first()
      .fadeOut(300, function () {
        $(this).remove();
      });
  }, 5000);
}
$(document).ready(function () {
  // ===== TAB SWITCHING =====
  $(".tab-button").on("click", function () {
    const tabName = $(this).data("tab");

    // Remove active class from all buttons and contents
    $(".tab-button").removeClass("active").addClass("text-gray-400");
    $(".tab-content").removeClass("active");

    // Add active class to clicked button and corresponding content
    $(this).addClass("active").removeClass("text-gray-400");
    $("#" + tabName).addClass("active");
  });

  // Close plan modal
  $(".modal-close, .modal-cancel").on("click", function () {
    $(this).closest(".modal-backdrop").removeClass("show");
    $("body").css("overflow", "auto");
  });

  // Close modal when clicking backdrop
  $("#planModal, #memberModal").on("click", function (e) {
    if ($(e.target).is(this)) {
      $(this).removeClass("show");
      $("body").css("overflow", "auto");
    }
  });
});

// ===== DELETE PLAN LOGIC =====
$(document).on("click", ".btn-delete-plan", function () {
  const planId = $(this).data("plan-id");
  $("#delete_plan_id").val(planId);
  $("#deletePlanModal").addClass("show");
  $("body").css("overflow", "hidden");
});

$(".delete-plan-cancel, .delete-plan-close").on("click", function () {
  $("#deletePlanModal").removeClass("show");
  $("body").css("overflow", "auto");
});

$("#deletePlanForm").on("submit", function (e) {
  e.preventDefault();
  const submitBtn = $("#deletePlanBtn");
  const originalText = submitBtn.text();
  submitBtn.html('<span class="loading"></span>').prop("disabled", true);

  const formData = new FormData(this);

  $.ajax({
    type: "POST",
    url: "index.php?controller=Plan&action=deletePlan",
    data: formData,
    processData: false,
    contentType: false,
    dataType: "json",
    success: function (response) {
      if (response.success) {
        showAlert("✓ Plan deleted successfully", "success");
        setTimeout(() => {
          $("#deletePlanModal").removeClass("show");
          $("body").css("overflow", "auto");
          location.reload();
        }, 1500);
      } else {
        $("#deletePlanMessage")
          .html(response.message || "Failed to delete plan")
          .removeClass("hidden");
        submitBtn.html(originalText).prop("disabled", false);
      }
    },
    error: function () {
      $("#deletePlanMessage")
        .html("An error occurred")
        .removeClass("hidden");
      submitBtn.html(originalText).prop("disabled", false);
    }
  });
});

// ===== DELETE WALK-IN LOGIC =====
$(document).on("click", ".btn-delete-walkin", function () {
  const row = $(this).closest("tr");
  const walkinId = row.data("walkin-id");
  $("#delete_walkin_id").val(walkinId);
  $("#deleteWalkinModal").addClass("show");
  $("body").css("overflow", "hidden");
});

$(".delete-walkin-cancel, .delete-walkin-close").on("click", function () {
  $("#deleteWalkinModal").removeClass("show");
  $("body").css("overflow", "auto");
});

$("#deleteWalkinForm").on("submit", function (e) {
  e.preventDefault();
  const submitBtn = $("#deleteWalkinBtn");
  const originalText = submitBtn.text();
  submitBtn.html('<span class="loading"></span>').prop("disabled", true);

  const formData = new FormData(this);

  $.ajax({
    type: "POST",
    url: "index.php?controller=User&action=deleteWalkin",
    data: formData,
    processData: false,
    contentType: false,
    dataType: "json",
    success: function (response) {
      if (response.success) {
        showAlert("✓ Walk-in record deleted", "success");
        setTimeout(() => {
          $("#deleteWalkinModal").removeClass("show");
          $("body").css("overflow", "auto");
          location.reload();
        }, 1500);
      } else {
        $("#deleteWalkinMessage")
          .html(response.message || "Failed to delete record")
          .removeClass("hidden");
        submitBtn.html(originalText).prop("disabled", false);
      }
    },
    error: function () {
      $("#deleteWalkinMessage")
        .html("An error occurred")
        .removeClass("hidden");
      submitBtn.html(originalText).prop("disabled", false);
    }
  });
});
// Close member modal
$(".member-modal-close").on("click", function () {
  $("#memberModal").removeClass("show");
  $("body").css("overflow", "auto");
});
$(document).ready(function () {
  // ===== ADD MEMBER MODAL =====
  $("#btnAddMember").on("click", function () {
    $("#addMemberForm")[0].reset();
    $("#addMemberMessage").addClass("hidden");
    $("#addMemberModal").addClass("show");
    $("body").css("overflow", "hidden");
  });

  // Close add member modal
  $(".add-member-close, .add-member-cancel").on("click", function () {
    $("#addMemberModal").removeClass("show");
    $("body").css("overflow", "auto");
  });

  // ===== ADD WALK-IN MODAL =====
  $("#btnAddWalkIn").on("click", function () {
    $("#addWalkInForm")[0].reset();
    $("#walkInMessage").addClass("hidden");
    // Set today's date and time as default
    const now = new Date();
    const datetime = now.toISOString().slice(0, 16);
    $("#visitTime").val(datetime);
    $("#addWalkInModal").addClass("show");
    $("body").css("overflow", "hidden");
  });

  // Calculate payment amount based on session type
  $("#sessionType").on("change", function () {
    const selectedOption = $(this).find("option:selected");
    const price = selectedOption.data("price");
    const sessionType = selectedOption.val();

    if (price) {
      $("#paymentAmount").val(price);

      // Calculate end date based on session type
      const visitTime = $("#visitTime").val();
      if (visitTime) {
        calculateEndDate(visitTime, sessionType);
      }
    } else {
      $("#paymentAmount").val("");
      $("#endDate").val("");
    }
  });
  // Update end date when visit time changes
  $("#visitTime").on("change", function () {
    const visitTime = $(this).val();
    const sessionType = $("#sessionType").val();

    if (visitTime && sessionType) {
      calculateEndDate(visitTime, sessionType);
    }
  });

  function calculateEndDate(visitTime, sessionType) {
    const startDate = new Date(visitTime);
    let endDate = new Date(startDate);

    switch (sessionType) {
      case "single":
        // Single session - 3 hours
        endDate.setHours(endDate.getHours() + 3);
        break;
      case "day_pass":
        // Day pass - until end of day (11:59 PM)
        endDate.setHours(23, 59, 0, 0);
        break;
      case "weekend":
        // Weekend pass - 2 days
        endDate.setDate(endDate.getDate() + 2);
        endDate.setHours(23, 59, 0, 0);
        break;
    }

    $("#endDate").val(endDate.toISOString().slice(0, 16));
  }

  // Close walk-in modal
  $(".walkin-close, .walkin-cancel").on("click", function () {
    $("#addWalkInModal").removeClass("show");
    $("body").css("overflow", "auto");
  });

  // ===== ADD WALK-IN FORM SUBMISSION WITH AJAX =====
  $("#addWalkInForm").on("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitBtn = $("#btnSubmitWalkIn");
    const originalText = submitBtn.text();

    // Show loading state
    submitBtn.html('<span class="loading"></span>').prop("disabled", true);

    $.ajax({
      type: "POST",
      url: "index.php?controller=User&action=validateWalkin",
      data: formData,
      processData: false,
      contentType: false,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          showWalkInMessage("✓ " + response.message, "success");
          setTimeout(() => {
            $("#addWalkInModal").removeClass("show");
            $("body").css("overflow", "auto");
            location.reload();
          }, 2000);
        } else {
          showWalkInMessage(
            response.message || "Failed to register walk-in",
            "error"
          );
          submitBtn.html(originalText).prop("disabled", false);
        }
      },
      error: function (xhr, status, error) {
        console.error("Walk-in registration error:", error);
        let errorMessage = "An error occurred. Please try again.";

        if (xhr.responseJSON && xhr.responseJSON.message) {
          errorMessage = xhr.responseJSON.message;
        } else if (xhr.responseJSON && xhr.responseJSON.error) {
          errorMessage = xhr.responseJSON.error;
        }

        showWalkInMessage(errorMessage, "error");
        submitBtn.html(originalText).prop("disabled", false);
      },
    });
  });
  $(document).ready(function () {
    $(document).on("click", ".btn-view-walkin", function () {
      let walkinId = $(this).closest("tr").data("walkin-id");

      if (!walkinId) return;

      $.ajax({
        url: "index.php?controller=User&action=getWalkinData",
        type: "GET",
        data: { walkin_id: walkinId },
        dataType: "json",
        success: function (response) {
          if (response.success) {
            let w = response.data;
            let html = `
                          <div class="space-y-6">
                              <div class="flex items-center justify-between bg-slate-900/50 p-4 rounded-xl border border-slate-700/50">
                                  <div>
                                      <p class="text-slate-400 text-xs uppercase tracking-wider font-semibold">Walk-in ID</p>
                                      <p class="text-white font-mono text-lg tracking-wide">#${w.walkin_id
              }</p>
                                  </div>
                                  <span class="px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wide bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                      ${w.session_type.toUpperCase()}
                                  </span>
                              </div>

                              <div class="bg-slate-700/20 rounded-xl p-5 border border-slate-700/50">
                                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                      <div>
                                          <p class="text-slate-500 text-xs mb-1">Full Name</p>
                                          <p class="text-white font-medium text-lg">${w.first_name
              } ${w.middle_name ? w.middle_name + " " : ""
              }${w.last_name}</p>
                                      </div>
                                      <div>
                                          <p class="text-slate-500 text-xs mb-1">Contact</p>
                                          <p class="text-white font-medium">${w.contact_no
              }</p>
                                          <p class="text-slate-400 text-xs">${w.email || "No email"
              }</p>
                                      </div>
                                  </div>
                                  <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-700/50">
                                      <div>
                                          <p class="text-slate-500 text-xs mb-1">Visit Time</p>
                                          <p class="text-white font-medium">${w.visit_time
              }</p>
                                      </div>
                                      <div>
                                          <p class="text-slate-500 text-xs mb-1">Valid Until</p>
                                          <p class="text-white font-medium">${w.end_date
              }</p>
                                      </div>
                                  </div>
                                  <div class="mt-4 pt-4 border-t border-slate-700/50">
                                      <p class="text-slate-500 text-xs mb-1">Payment</p>
                                      <p class="text-white font-bold">₱${w.payment_amount
              } <span class="text-slate-400 font-normal text-xs">(${w.payment_method
              })</span></p>
                                  </div>
                              </div>
                          </div>
                      `;
            $("#walkinDetails").html(html);
            $("#btnEditWalkinFromView").data("walkin-id", w.walkin_id); // Store ID on edit button
            $("#viewWalkinModal").addClass("show");
          }
        },
      });
    });

    // Close View Modal
    $(".view-walkin-close").click(function () {
      $("#viewWalkinModal").removeClass("show");
    });

    // --- Edit Walk-in Details ---

    // Trigger from Table
    $(document).on("click", ".btn-edit-walkin", function () {
      let walkinId = $(this).closest("tr").data("walkin-id"); // Ensure TR has data-walkin-id
      openEditWalkinModal(walkinId);
    });

    // Trigger from View Modal
    $("#btnEditWalkinFromView").click(function () {
      let walkinId = $(this).data("walkin-id");
      $("#viewWalkinModal").removeClass("show");
      openEditWalkinModal(walkinId);
    });

    function openEditWalkinModal(walkinId) {
      if (!walkinId) return;

      $.ajax({
        url: "index.php?controller=User&action=getWalkinData",
        type: "GET",
        data: { walkin_id: walkinId },
        dataType: "json",
        success: function (response) {
          if (response.success) {
            let w = response.data;
            $("#edit_walkin_id").val(w.walkin_id);
            $("#edit_walkin_first_name").val(w.first_name);
            $("#edit_walkin_middle_name").val(w.middle_name);
            $("#edit_walkin_last_name").val(w.last_name);
            $("#edit_walkin_email").val(w.email);
            $("#edit_walkin_contact_no").val(w.contact_no);
            $("#edit_walkin_session_type").val(w.session_type);
            $("#edit_walkin_payment_amount").val(w.payment_amount);
            $("#edit_walkin_payment_method").val(w.payment_method);

            // Format datetime for datetime-local input (YYYY-MM-DDTHH:MM)
            let visitTime = w.visit_time ? w.visit_time.replace(" ", "T") : "";
            let endDate = w.end_date ? w.end_date.replace(" ", "T") : "";

            $("#edit_walkin_visit_time").val(visitTime.substring(0, 16));
            $("#edit_walkin_end_date").val(endDate.substring(0, 16));

            $("#editWalkinModal").addClass("show");
          }
        },
      });
    }

    // Close Edit Modal
    $(".edit-walkin-close, .edit-walkin-cancel").click(function () {
      $("#editWalkinModal").removeClass("show");
    });

    // Submit Edit Form
    $("#editWalkinForm").submit(function (e) {
      e.preventDefault();

      // Disable button to prevent double clicks
      const $btn = $("#btnUpdateWalkin");
      const originalText = $btn.html();
      $btn
        .prop("disabled", true)
        .html('<i class="fas fa-spinner fa-spin"></i> Updating...');

      // Select the message container
      const $msgBox = $("#editWalkinMessage");

      $.ajax({
        url: $(this).attr("action"),
        type: "POST",
        data: $(this).serialize(),
        dataType: "json",
        success: function (response) {
          if (response.success) {
            // 1. Show Success Message inside the modal (Green styling)
            $msgBox
              .removeClass("hidden text-red-400 bg-red-500/10") // Remove error styles
              .addClass("text-emerald-400 bg-emerald-500/10") // Add success styles
              .html(
                `<i class="fas fa-check-circle mr-2"></i> ${response.message}`
              )
              .fadeIn();

            // 2. Wait 1.5 seconds, then close modal and reload
            setTimeout(function () {
              $("#editWalkinModal").removeClass("show"); // Close modal
              location.reload(); // Reload page to reflect changes
            }, 1500);
          } else {
            // Show Error Message inside the modal (Red styling)
            $msgBox
              .removeClass("hidden text-emerald-400 bg-emerald-500/10") // Remove success styles
              .addClass("text-red-400 bg-red-500/10") // Add error styles
              .html(
                `<i class="fas fa-exclamation-triangle mr-2"></i> ${response.message}`
              )
              .fadeIn();

            // Re-enable button so they can try again
            $btn.prop("disabled", false).html(originalText);
          }
        },
        error: function () {
          // Generic Error
          $msgBox
            .removeClass("hidden text-emerald-400 bg-emerald-500/10")
            .addClass("text-red-400 bg-red-500/10")
            .html(
              `<i class="fas fa-times-circle mr-2"></i> An unexpected error occurred.`
            )
            .fadeIn();

          $btn.prop("disabled", false).html(originalText);
        },
      });
    });
  });

  // Function to show Toast Notifications
  function showNotification(type, message) {
    const container = $("#alertContainer");

    // Define colors and icons based on type
    let styles = "";
    let icon = "";

    if (type === "success") {
      styles = "bg-emerald-500/10 border-emerald-500/20 text-emerald-400";
      icon = '<i class="fa-solid fa-check-circle text-xl"></i>';
    } else if (type === "error") {
      styles = "bg-red-500/10 border-red-500/20 text-red-400";
      icon = '<i class="fa-solid fa-circle-exclamation text-xl"></i>';
    } else {
      styles = "bg-blue-500/10 border-blue-500/20 text-blue-400";
      icon = '<i class="fa-solid fa-info-circle text-xl"></i>';
    }

    // Create the Toast HTML
    const toastHtml = `
          <div class="glass-panel p-4 rounded-lg border flex items-center shadow-lg transform transition-all duration-300 translate-x-full opacity-0 ${styles}">
              <div class="mr-3">${icon}</div>
              <div class="font-medium text-sm">${message}</div>
          </div>
      `;

    // Append to container
    const $toast = $(toastHtml).appendTo(container);

    // Animate In (Slide from right)
    setTimeout(() => {
      $toast.removeClass("translate-x-full opacity-0");
    }, 100);

    // Remove after 3 seconds
    setTimeout(() => {
      $toast.addClass("translate-x-full opacity-0"); // Slide out
      setTimeout(() => {
        $toast.remove(); // Remove from DOM
      }, 300);
    }, 3000);
  }
  // ===== ADD MEMBER FORM VALIDATION =====
  $("#addMemberForm").on("submit", function (e) {
    e.preventDefault();

    const password = $('input[name="password"]').val();
    const confirmPassword = $('input[name="cPassword"]').val();

    if (password.length < 8) {
      showAddMemberMessage("Password must be at least 8 characters", "error");
      return false;
    }

    if (password !== confirmPassword) {
      showAddMemberMessage("Passwords do not match", "error");
      return false;
    }

    // If validation passes, submit via AJAX
    const formData = new FormData(this);
    const submitBtn = $("#btnSubmitMember");
    const originalText = submitBtn.text();

    // Show loading state
    submitBtn.html('<span class="loading"></span>').prop("disabled", true);

    $.ajax({
      type: "POST",
      url: "index.php?controller=Admin&action=registerMember",
      data: formData,
      processData: false,
      contentType: false,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          showAddMemberMessage("✓ " + response.message, "success");
          setTimeout(() => {
            $("#addMemberModal").removeClass("show");
            $("body").css("overflow", "auto");
            location.reload();
          }, 2000);
        } else {
          // Handle validation errors
          if (response.errors) {
            let errorMessages = "";
            for (let field in response.errors) {
              errorMessages += response.errors[field] + "<br>";
            }
            showAddMemberMessage(errorMessages, "error");
          } else {
            showAddMemberMessage(
              response.message || "Failed to add member",
              "error"
            );
          }
          submitBtn.html(originalText).prop("disabled", false);
        }
      },
      error: function (xhr, status, error) {
        console.error("Add member error:", error);
        let errorMessage = "An error occurred. Please try again.";

        if (xhr.responseJSON && xhr.responseJSON.message) {
          errorMessage = xhr.responseJSON.message;
        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
          let errorMessages = "";
          for (let field in xhr.responseJSON.errors) {
            errorMessages += xhr.responseJSON.errors[field] + "<br>";
          }
          errorMessage = errorMessages;
        }

        showAddMemberMessage(errorMessage, "error");
        submitBtn.html(originalText).prop("disabled", false);
      },
    });
  });

  // ===== HELPER FUNCTIONS =====
  function showAddMemberMessage(message, type) {
    const messageDiv = $("#addMemberMessage");
    const bgColor = type === "error" ? "bg-red-500" : "bg-green-500";

    messageDiv
      .html(
        `
                    <div class="p-3 rounded-lg ${bgColor} text-white text-sm">
                        ${message}
                    </div>
                `
      )
      .removeClass("hidden");

    if (type !== "error") {
      setTimeout(() => {
        messageDiv.addClass("hidden");
      }, 3000);
    }
  }

  function showWalkInMessage(message, type) {
    const messageDiv = $("#walkInMessage");
    const bgColor = type === "error" ? "bg-red-500" : "bg-green-500";

    messageDiv
      .html(
        `
                    <div class="p-3 rounded-lg ${bgColor} text-white text-sm">
                        ${message}
                    </div>
                `
      )
      .removeClass("hidden");

    if (type !== "error") {
      setTimeout(() => {
        messageDiv.addClass("hidden");
      }, 3000);
    }
  }
});
$(document).ready(function () {
  // ===== VIEW MEMBER DETAILS =====
  $(document).on("click", ".btn-view-member", function () {
    const row = $(this).closest(".table-row");
    const userId = row.data("user-id");

    if (!userId) {
      alert(
        "User ID not found. Make sure table rows have data-user-id attribute."
      );
      return;
    }
    // Fetch member data
    $.ajax({
      type: "GET",
      url: "index.php?controller=User&action=getMemberData&user_id=" + userId,
      dataType: "json",
      success: function (response) {
        if (response.success && response.data) {
          const member = response.data;

          // Build HTML content
          let detailsHTML = `
              <div class="space-y-6">
                  
                  <div class="flex items-center justify-between bg-slate-900/50 p-4 rounded-xl border border-slate-700/50">
                      <div>
                          <p class="text-slate-400 text-xs uppercase tracking-wider font-semibold">User ID</p>
                          <p class="text-white font-mono text-lg tracking-wide">#${member.user_id
            }</p>
                      </div>
                      <div>
                          <span class="px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wide border border-opacity-20 shadow-sm ${member.role === "admin"
              ? "bg-red-500/10 text-red-400 border-red-500"
              : member.role === "trainer"
                ? "bg-purple-500/10 text-purple-400 border-purple-500"
                : "bg-blue-500/10 text-blue-400 border-blue-500"
            }">
                              ${member.role || "user"}
                          </span>
                      </div>
                  </div>

                  <div>
                      <h4 class="text-slate-200 font-semibold mb-3 flex items-center gap-2">
                          <i class="fa-regular fa-address-card text-blue-500"></i> Personal Information
                      </h4>
                      
                      <div class="bg-slate-700/20 rounded-xl p-5 border border-slate-700/50">
                          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5 pb-5 border-b border-slate-700/50">
                              <div>
                                  <p class="text-slate-500 text-xs mb-1">First Name</p>
                                  <p class="text-white font-medium truncate">${member.first_name
            }</p>
                              </div>
                              <div>
                                  <p class="text-slate-500 text-xs mb-1">Middle Name</p>
                                  <p class="text-white font-medium truncate">${member.middle_name ||
            '<span class="text-slate-600">-</span>'
            }</p>
                              </div>
                              <div>
                                  <p class="text-slate-500 text-xs mb-1">Last Name</p>
                                  <p class="text-white font-medium truncate">${member.last_name
            }</p>
                              </div>
                          </div>

                          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                              <div>
                                  <p class="text-slate-500 text-xs mb-1">Email Address</p>
                                  <div class="flex items-center gap-2 text-white font-medium">
                                      <i class="fa-regular fa-envelope text-slate-500 text-xs"></i>
                                      <span class="truncate">${member.email
            }</span>
                                  </div>
                              </div>
                              <div>
                                  <p class="text-slate-500 text-xs mb-1">Member Since</p>
                                  <div class="flex items-center gap-2 text-white font-medium">
                                      <i class="fa-regular fa-calendar text-slate-500 text-xs"></i>
                                      <span>${member.created_at}</span>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>

                  <div>
                      <h4 class="text-slate-200 font-semibold mb-3 flex items-center gap-2">
                          <i class="fa-solid fa-medal text-yellow-500"></i> Membership Details
                      </h4>
                      <div class="grid grid-cols-2 gap-4">
                          <div class="bg-slate-700/20 p-4 rounded-xl border border-slate-700/50">
                              <p class="text-slate-500 text-xs mb-1">Current Plan</p>
                              <p class="text-white font-bold text-lg tracking-tight">${member.plan_name || "No Active Plan"
            }</p>
                          </div>
                          <div class="bg-slate-700/20 p-4 rounded-xl border border-slate-700/50">
                              <p class="text-slate-500 text-xs mb-1">Account Status</p>
                              <div class="flex items-center gap-2 mt-1">
                                  <span class="w-2.5 h-2.5 rounded-full ${member.status === "active"
              ? "bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"
              : "bg-slate-500"
            }"></span>
                                  <span class="text-white font-medium capitalize">${member.status || "inactive"
            }</span>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          `;

          $("#memberDetails").html(detailsHTML);
          $("#memberModal").data("user-id", userId);
          $("#memberModal").addClass("show");
          $("body").css("overflow", "hidden");
        } else {
          alert("Failed to load member data");
        }
      },
      error: function () {
        alert("An error occurred while loading member data");
      },
    });
  });

  // ===== OPEN EDIT FROM VIEW MODAL =====
  $("#btnEditMemberFromView").on("click", function () {
    const userId = $("#memberModal").data("user-id");
    $("#memberModal").removeClass("show");
    fetchMemberForEdit(userId);
  });

  // ===== DIRECT EDIT FROM TABLE =====
  $(document).on("click", ".btn-edit-member", function () {
    const row = $(this).closest(".table-row");
    const userId = row.data("user-id");
    fetchMemberForEdit(userId);
  });

  // ===== FETCH MEMBER DATA FOR EDITING =====
  function fetchMemberForEdit(userId) {
    $.ajax({
      type: "GET",
      url: "index.php?controller=User&action=getMemberData&user_id=" + userId,
      dataType: "json",
      success: function (response) {
        if (response.success && response.data) {
          const member = response.data;

          $("#edit_user_id").val(member.user_id);
          $("#edit_first_name").val(member.first_name);
          $("#edit_last_name").val(member.last_name);
          $("#edit_middle_name").val(member.middle_name || "");
          $("#edit_email").val(member.email);
          $("#edit_role").val(member.role || "user");
          $("#edit_password").val("");
          $("#edit_confirm_password").val("");

          $("#editMemberModal").addClass("show");
          $("body").css("overflow", "hidden");
        } else {
          alert("Failed to load member data for editing");
        }
      },
      error: function () {
        alert("An error occurred while loading member data");
      },
    });
  }

  // ===== UPDATE MEMBER FORM SUBMISSION =====
  $("#editMemberForm").on("submit", function (e) {
    e.preventDefault();

    const password = $("#edit_password").val();
    const confirmPassword = $("#edit_confirm_password").val();
    const userId = $("#edit_user_id").val();
    if (password || confirmPassword) {
      if (password.length < 8) {
        showMessage("Password must be at least 8 characters", "error");
        return false;
      }
      if (password !== confirmPassword) {
        showMessage("Passwords do not match", "error");
        return false;
      }
    }

    const formData = new FormData(this);
    const submitBtn = $("#btnUpdateMember");
    const originalText = submitBtn.text();

    submitBtn.html('<span class="loading"></span>').prop("disabled", true);

    $.ajax({
      type: "POST",
      url: "index.php?controller=User&action=updateMember&user_id=" + userId,
      data: formData,
      processData: false,
      contentType: false,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          showMessage("✓ " + response.message, "success");
          setTimeout(() => {
            $("#editMemberModal").removeClass("show");
            $("body").css("overflow", "auto");
            location.reload();
          }, 2000);
        } else {
          showMessage(response.message || "Failed to update member", "error");
          submitBtn.html(originalText).prop("disabled", false);
        }
      },
      error: function (xhr) {
        let errorMessage = "An error occurred. Please try again.";
        if (xhr.responseJSON && xhr.responseJSON.message) {
          errorMessage = xhr.responseJSON.message;
        }
        showMessage(errorMessage, "error");
        submitBtn.html(originalText).prop("disabled", false);
      },
    });
  });

  // ===== CLOSE MODALS =====
  $(".member-modal-close").on("click", function () {
    $("#memberModal").removeClass("show");
    $("body").css("overflow", "auto");
  });

  $(".edit-member-close, .edit-member-cancel").on("click", function () {
    $("#editMemberModal").removeClass("show");
    $("body").css("overflow", "auto");
  });

  // ===== HELPER FUNCTION =====
  function showMessage(message, type) {
    const messageDiv = $("#editMemberMessage");
    const bgColor = type === "error" ? "bg-red-500" : "bg-green-500";

    messageDiv
      .html(
        `
            <div class="p-3 rounded-lg ${bgColor} text-white text-sm">
                ${message}
            </div>
        `
      )
      .removeClass("hidden");

    if (type !== "error") {
      setTimeout(() => messageDiv.addClass("hidden"), 3000);
    }
  }

  // ===== DIRECT DELETE FROM TABLE =====
  $(document).on("click", ".btn-delete-member", function () {
    const row = $(this).closest(".table-row");
    const userId = row.data("user-id");
    $("#deleteMemberModal").addClass("show");
    $("#delete_user_id").val(userId);

    const deleteBtn = $("#deleteBtn");
    const originalText = deleteBtn.text();
    deleteBtn.html('<span class="loading"></span>').prop("disabled", true);
    $("body").css("overflow", "hidden");

    setTimeout(() => {
      deleteBtn.html(originalText).prop("disabled", false);
    }, 2000);
  });
  // ===== CLOSE DELETE MODAL =====
  $(".delete-modal-close").on("click", function () {
    $("#deleteMemberModal").removeClass("show");
    $("body").css("overflow", "auto");
  });

  $(".delete-member-close, .delete-member-cancel").on("click", function () {
    $("#deleteMemberModal").removeClass("show");
    $("body").css("overflow", "auto");
  });

  $("#deleteForm").on("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitBtn = $("#deleteBtn");
    const originalText = submitBtn.text();
    const userId = $("#delete_user_id").val();
    console.log(userId);
    submitBtn.html('<span class="loading"></span>').prop("disabled", true);

    $.ajax({
      type: "POST",
      url: "index.php?controller=User&action=deleteMember",
      data: formData,
      processData: false,
      contentType: false,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          showDeleteMessage("✓ " + response.message, "success");
          setTimeout(() => {
            $("#deleteMemberModal").removeClass("show");
            $("body").css("overflow", "auto");
            location.reload();
          }, 2000);
        } else {
          showDeleteMessage(
            response.message || "Failed to update member",
            "error"
          );
          submitBtn.html(originalText).prop("disabled", false);
        }
      },
      error: function (xhr) {
        let errorMessage = "An error occurred. Please try again.";
        if (xhr.responseJSON && xhr.responseJSON.message) {
          errorMessage = xhr.responseJSON.message;
        }
        showDeleteMessage(errorMessage, "error");
        submitBtn.html(originalText).prop("disabled", false);
      },
    });
  });
  // ===== HELPER FUNCTION =====
  function showDeleteMessage(message, type) {
    const messageDiv = $("#deleteMemberMessage");
    const bgColor = type === "error" ? "bg-red-500" : "bg-green-500";

    messageDiv
      .html(
        `
            <div class="p-3 rounded-lg ${bgColor} text-white text-sm">
                ${message}
            </div>
        `
      )
      .removeClass("hidden");

    if (type !== "error") {
      setTimeout(() => messageDiv.addClass("hidden"), 3000);
    }
  }
});

$(document).ready(function () {
  // ===== OPEN ADD TRAINER MODAL =====
  $(document).on("click", "#btnAddNewTrainer", function () {
    $("#addTrainerForm")[0].reset();
    $("#addTrainerMessage").addClass("hidden");
    $("#addTrainerModal").addClass("show");
    $("body").css("overflow", "hidden");
  });

  // ===== ADD TRAINER FORM SUBMISSION =====
  $("#addTrainerForm").on("submit", function (e) {
    e.preventDefault();

    const password = $('input[name="password"]').val();
    const confirmPassword = $('input[name="confirm_password"]').val();

    if (password.length < 8) {
      showMessage(
        "addTrainerMessage",
        "Password must be at least 8 characters",
        "error"
      );
      return false;
    }

    if (password !== confirmPassword) {
      showMessage("addTrainerMessage", "Passwords do not match", "error");
      return false;
    }

    const formData = new FormData(this);
    const submitBtn = $("#btnAddTrainer");
    const originalText = submitBtn.text();

    submitBtn.html('<span class="loading"></span>').prop("disabled", true);

    $.ajax({
      type: "POST",
      url: "index.php?controller=Admin&action=addTrainer",
      data: formData,
      processData: false,
      contentType: false,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          showMessage("addTrainerMessage", "✓ " + response.message, "success");
          setTimeout(() => {
            $("#addTrainerModal").removeClass("show");
            $("body").css("overflow", "auto");
            location.reload();
          }, 2000);
        } else {
          showMessage(
            "addTrainerMessage",
            response.message || "Failed to add trainer",
            "error"
          );
          submitBtn.html(originalText).prop("disabled", false);
        }
      },
      error: function (xhr) {
        let errorMessage = "An error occurred. Please try again.";
        if (xhr.responseJSON && xhr.responseJSON.message) {
          errorMessage = xhr.responseJSON.message;
        }
        showMessage("addTrainerMessage", errorMessage, "error");
        submitBtn.html(originalText).prop("disabled", false);
      },
    });
  });

  // ===== VIEW TRAINER DETAILS =====
  $(document).on("click", ".btn-view-trainer", function () {
    const row = $(this).closest(".table-row");
    const trainerId = row.data("trainer-id");

    if (!trainerId) {
      alert("Trainer ID not found");
      return;
    }

    $.ajax({
      type: "GET",
      url:
        "index.php?controller=Admin&action=getTrainerData&trainer_id=" +
        trainerId,
      dataType: "json",
      success: function (response) {
        if (response.success && response.data) {
          const trainer = response.data;

          let detailsHTML = `
              <div class="space-y-6">
                  
                  <div class="flex items-center justify-between bg-slate-900/50 p-4 rounded-xl border border-slate-700/50">
                      <div>
                          <p class="text-slate-400 text-xs uppercase tracking-wider font-semibold">Trainer ID</p>
                          <p class="text-white font-mono text-lg tracking-wide">#${trainer.trainer_id
            }</p>
                      </div>
                      <div>
                          <span class="px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wide border border-opacity-20 shadow-sm ${trainer.status === "active"
              ? "bg-emerald-500/10 text-emerald-400 border-emerald-500"
              : "bg-slate-500/10 text-slate-400 border-slate-500"
            }">
                              ${trainer.status}
                          </span>
                      </div>
                  </div>

                  <div>
                      <h4 class="text-slate-200 font-semibold mb-3 flex items-center gap-2">
                          <i class="fa-solid fa-user-tie text-blue-500"></i> Personal Information
                      </h4>
                      
                      <div class="bg-slate-700/20 rounded-xl p-5 border border-slate-700/50">
                          <div class="mb-5 pb-5 border-b border-slate-700/50">
                              <p class="text-slate-500 text-xs mb-1">Full Name</p>
                              <p class="text-white font-medium text-lg">${trainer.name
            }</p>
                          </div>

                          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                              <div>
                                  <p class="text-slate-500 text-xs mb-1">Email Address</p>
                                  <div class="flex items-center gap-2 text-white font-medium">
                                      <i class="fa-regular fa-envelope text-slate-500 text-xs"></i>
                                      <span class="truncate">${trainer.email
            }</span>
                                  </div>
                              </div>
                              <div>
                                  <p class="text-slate-500 text-xs mb-1">Contact Number</p>
                                  <div class="flex items-center gap-2 text-white font-medium">
                                      <i class="fa-solid fa-phone text-slate-500 text-xs"></i>
                                      <span>${trainer.contact_no ||
            '<span class="text-slate-600 italic">N/A</span>'
            }</span>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>

                  <div>
                      <h4 class="text-slate-200 font-semibold mb-3 flex items-center gap-2">
                          <i class="fa-solid fa-briefcase text-purple-500"></i> Professional Profile
                      </h4>
                      <div class="bg-slate-700/20 rounded-xl p-5 border border-slate-700/50">
                          
                          <div class="mb-4">
                              <p class="text-slate-500 text-xs mb-1">Specialization</p>
                              <div class="flex flex-wrap gap-2">
                                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-blue-500/10 text-blue-400 text-sm font-medium border border-blue-500/20">
                                      ${trainer.specialization}
                                  </span>
                              </div>
                          </div>

                          <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-700/50">
                              <div>
                                  <p class="text-slate-500 text-xs mb-1">Experience</p>
                                  <p class="text-white font-bold text-lg">${trainer.experience_years
            } <span class="text-sm font-normal text-slate-400">Years</span></p>
                              </div>
                              <div>
                                  <p class="text-slate-500 text-xs mb-1">Joined Team</p>
                                  <div class="flex items-center gap-2 text-white font-medium">
                                      <i class="fa-regular fa-calendar-check text-slate-500 text-xs"></i>
                                      <span>${trainer.join_date}</span>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
           `;

          $("#trainerDetails").html(detailsHTML);
          $("#viewTrainerModal").data("trainer-id", trainerId);
          $("#viewTrainerModal").addClass("show");
          $("body").css("overflow", "hidden");
        } else {
          alert("Failed to load trainer data");
        }
      },
      error: function () {
        alert("An error occurred while loading trainer data");
      },
    });
  });

  // ===== OPEN EDIT FROM VIEW MODAL =====
  $("#btnEditTrainer").on("click", function () {
    const row = $(this).closest(".table-row");
    const trainerId = row.data("trainer-id");
    $("#viewTrainerModal").removeClass("show");
    loadTrainerForEdit(trainerId);
  });

  // ===== DIRECT EDIT FROM TABLE =====
  $(document).on("click", ".btn-edit-trainer", function () {
    const row = $(this).closest(".table-row");
    const trainerId = row.data("trainer-id");
    loadTrainerForEdit(trainerId);
  });

  // ===== LOAD TRAINER DATA FOR EDITING =====
  function loadTrainerForEdit(trainerId) {
    $.ajax({
      type: "GET",
      url:
        "index.php?controller=Admin&action=getTrainerData&trainer_id=" +
        trainerId,
      dataType: "json",
      success: function (response) {
        if (response.success && response.data) {
          const trainer = response.data;

          $("#edit_trainer_id").val(trainer.trainer_id);
          $("#edit_user_trainer_id").val(trainer.user_id);
          $("#edit_trainer_first_name").val(trainer.first_name);
          $("#edit_trainer_last_name").val(trainer.last_name);
          $("#edit_trainer_middle_name").val(trainer.middle_name || "");
          $("#edit_trainer_email").val(trainer.email);
          $("#edit_trainer_contact_no").val(trainer.contact_no || "");
          $("#edit_specialization").val(trainer.specialization);
          $("#edit_experience_years").val(trainer.experience_years);
          $("#edit_trainer_status").val(trainer.status);
          $("#edit_bio").val(trainer.bio || "");
          $("#edit_trainer_password").val("");
          $("#edit_confirm_trainer_password").val("");

          $("#editTrainerModal").addClass("show");
          $("body").css("overflow", "hidden");
        } else {
          alert("Failed to load trainer data for editing");
        }
      },
      error: function () {
        alert("An error occurred while loading trainer data");
      },
    });
  }

  // ===== UPDATE TRAINER FORM SUBMISSION =====
  $("#editTrainerForm").on("submit", function (e) {
    e.preventDefault();

    const password = $("#edit_password").val();
    const confirmPassword = $("#edit_confirm_password").val();

    if (password || confirmPassword) {
      if (password.length < 8) {
        showMessage(
          "editTrainerMessage",
          "Password must be at least 8 characters",
          "error"
        );
        return false;
      }
      if (password !== confirmPassword) {
        showMessage("editTrainerMessage", "Passwords do not match", "error");
        return false;
      }
    }

    const formData = new FormData(this);
    const submitBtn = $("#btnUpdateTrainer");
    const originalText = submitBtn.text();

    submitBtn.html('<span class="loading"></span>').prop("disabled", true);

    $.ajax({
      type: "POST",
      url: "index.php?controller=Trainer&action=updateTrainer",
      data: formData,
      processData: false,
      contentType: false,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          showMessage("editTrainerMessage", "✓ " + response.message, "success");
          setTimeout(() => {
            $("#editTrainerModal").removeClass("show");
            $("body").css("overflow", "auto");
            location.reload();
          }, 2000);
        } else {
          showMessage(
            "editTrainerMessage",
            response.message || "Failed to update trainer",
            "error"
          );
          submitBtn.html(originalText).prop("disabled", false);
        }
      },
      error: function (xhr) {
        let errorMessage = "An error occurred. Please try again.";
        if (xhr.responseJSON && xhr.responseJSON.message) {
          errorMessage = xhr.responseJSON.message;
        }
        showMessage("editTrainerMessage", errorMessage, "error");
        submitBtn.html(originalText).prop("disabled", false);
      },
    });
  });
  // ===== DIRECT DELETE FROM TABLE =====
  $(document).on("click", ".btn-delete-trainer", function () {
    const row = $(this).closest(".table-row");
    const trainerId = row.data("trainer-id");
    $("#deleteTrainerModal").addClass("show");
    $("#delete_trainer_id").val(trainerId);

    const deleteBtn = $("#deleteBtn");
    const originalText = deleteBtn.text();
    deleteBtn.html('<span class="loading"></span>').prop("disabled", true);
    $("body").css("overflow", "hidden");

    setTimeout(() => {
      deleteBtn.html(originalText).prop("disabled", false);
    }, 2000);
  });
  // ===== CLOSE DELETE MODAL =====
  $(".delete-modal-close").on("click", function () {
    $("#deleteMemberModal").removeClass("show");
    $("body").css("overflow", "auto");
  });

  $(".delete-member-close, .delete-member-cancel").on("click", function () {
    $("#deleteMemberModal").removeClass("show");
    $("body").css("overflow", "auto");
  });
  $("#deleteTrainerForm").on("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitBtn = $("#deleteTrainerBtn");
    const originalText = submitBtn.text();
    const trainerId = $("#delete_trainer_id").val();
    submitBtn.html('<span class="loading"></span>').prop("disabled", true);

    $.ajax({
      type: "POST",
      url: "index.php?controller=Trainer&action=deleteTrainer",
      data: formData,
      processData: false,
      contentType: false,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          showDeleteMessage("✓ " + response.message, "success");
          setTimeout(() => {
            $("#deleteTrainerModal").removeClass("show");
            $("body").css("overflow", "auto");
            location.reload();
          }, 2000);
        } else {
          showDeleteMessage(
            response.message || "Failed to update member",
            "error"
          );
          submitBtn.html(originalText).prop("disabled", false);
        }
      },
      error: function (xhr) {
        let errorMessage = "An error occurred. Please try again.";
        if (xhr.responseJSON && xhr.responseJSON.message) {
          errorMessage = xhr.responseJSON.message;
        }
        showDeleteMessage(errorMessage, "error");
        submitBtn.html(originalText).prop("disabled", false);
      },
    });
  });
  //add trainer modal close
  $(".add-trainer-close").on("click", function () {
    $("#addTrainerModal").removeClass("show");
    $("body").css("overflow", "auto");
  });

  $(".add-trainer-close, .add-trainer-cancel").on("click", function () {
    $("#addTrainerModal").removeClass("show");
    $("body").css("overflow", "auto");
  });
  //view trainer modal
  $(".view-trainer-close, .edit-trainer-cancel").on("click", function () {
    $("#viewTrainerModal").removeClass("show");
    $("body").css("overflow", "auto");
  });
  //close edit trainer modal
  $(".edit-trainer-close").on("click", function () {
    $("#editTrainerModal").removeClass("show");
    $("body").css("overflow", "auto");
  });

  $(".edit-trainer-close, .edit-trainer-cancel").on("click", function () {
    $("#editTrainerModal").removeClass("show");
    $("body").css("overflow", "auto");
  });

  $(".delete-trainer-modal-close").on("click", function () {
    $("#deleteTrainerModal").removeClass("show");
    $("body").css("overflow", "auto");
  });

  $(".delete-trainer-modal-close, .delete-trainer-cancel").on(
    "click",
    function () {
      $("#deleteTrainerModal").removeClass("show");
      $("body").css("overflow", "auto");
    }
  );
  // ===== HELPER FUNCTION =====
  function showMessage(message, type) {
    const messageDiv = $("#editMemberMessage");
    const bgColor = type === "error" ? "bg-red-500" : "bg-green-500";

    messageDiv
      .html(
        `
            <div class="p-3 rounded-lg ${bgColor} text-white text-sm">
                ${message}
            </div>
        `
      )
      .removeClass("hidden");

    if (type !== "error") {
      setTimeout(() => messageDiv.addClass("hidden"), 3000);
    }
  }
  function showDeleteMessage(message, type) {
    const messageDiv = $("#deleteMemberMessage");
    const bgColor = type === "error" ? "bg-red-500" : "bg-green-500";

    messageDiv
      .html(
        `
            <div class="p-3 rounded-lg ${bgColor} text-white text-sm">
                ${message}
            </div>
        `
      )
      .removeClass("hidden");

    if (type !== "error") {
      setTimeout(() => messageDiv.addClass("hidden"), 3000);
    }
  }
  // Add Member as Trainer Button
  $("#btnAddMemberNewTrainer").click(function () {
    // First, load available members
    $.ajax({
      url: "index.php?controller=Admin&action=getNonTrainerMembers",
      method: "GET",
      dataType: "json",
      success: function (response) {
        if (response.success) {
          showAddMemberAsTrainerModal(response.data);
        }
      },
      error: function () {
        showAlert("Failed to load members", "error");
      },
    });
  });

  function showAddMemberAsTrainerModal(members) {
    // Create modal dynamically
    const modal = `
          <div id="addMemberAsTrainerModal" class="modal-backdrop fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
              <div class="modal-content bg-gray-900 rounded-2xl p-8 max-w-lg w-full border border-gray-700">
                  <button class="close-member-trainer-modal float-right text-gray-400 hover:text-white text-2xl mb-4">&times;</button>
                  
                  <h3 class="text-2xl font-bold text-white mb-6">Promote Member to Trainer</h3>
                  
                  <form id="addMemberAsTrainerForm">
                      <div class="mb-4">
                          <label class="block text-white font-semibold mb-2">Select Member</label>
                          <select name="user_id" required class="w-full px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg">
                              <option value="">Choose a member...</option>
                              ${members
        .map(
          (m) =>
            `<option value="${m.user_id}">${m.name} (${m.email})</option>`
        )
        .join("")}
                          </select>
                      </div>
                      
                      <div class="mb-4">
                          <label class="block text-white font-semibold mb-2">Specialization</label>
                          <select name="specialization" required class="w-full px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg">
                              <option value="">Select specialization...</option>
                              <option value="Weight Training">Weight Training</option>
                              <option value="Cardio">Cardio</option>
                              <option value="CrossFit">CrossFit</option>
                              <option value="Yoga">Yoga</option>
                              <option value="Pilates">Pilates</option>
                              <option value="Boxing">Boxing</option>
                              <option value="Personal Training">Personal Training</option>
                              <option value="Nutrition">Nutrition</option>
                          </select>
                      </div>
                      
                      <div class="mb-4">
                          <label class="block text-white font-semibold mb-2">Experience (Years)</label>
                          <input type="number" name="experience_years" value="0" min="0" max="50" class="w-full px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg">
                      </div>
                      
                      <div class="mb-4">
                          <label class="block text-white font-semibold mb-2">Contact Number</label>
                          <input type="tel" name="contact_no" placeholder="+63 9XX XXX XXXX" class="w-full px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg">
                      </div>
                      
                      <div id="memberTrainerMessage" class="hidden mb-4"></div>
                      
                      <div class="flex space-x-4">
                          <button type="button" class="close-member-trainer-modal flex-1 px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg">
                              Cancel
                          </button>
                          <button type="submit" class="flex-1 px-4 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg">
                              Promote to Trainer
                          </button>
                      </div>
                  </form>
              </div>
          </div>
      `;

    // Append modal to body
    $("body").append(modal);
    $("#addMemberAsTrainerModal").addClass("show");

    // Close modal handlers
    $(".close-member-trainer-modal").click(function () {
      $("#addMemberAsTrainerModal").removeClass("show");
      setTimeout(() => $("#addMemberAsTrainerModal").remove(), 300);
    });
  }

  // Handle Member to Trainer Form Submission
  $(document).on("submit", "#addMemberAsTrainerForm", function (e) {
    e.preventDefault();

    const formData = $(this).serialize();
    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.text();

    submitBtn
      .prop("disabled", true)
      .html('<span class="loading"></span> Processing...');

    $.ajax({
      url: "index.php?controller=Admin&action=addMemberAsTrainer",
      method: "POST",
      data: formData,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          showAlert(response.message, "success");
          $("#addMemberAsTrainerModal").removeClass("show");
          setTimeout(() => {
            $("#addMemberAsTrainerModal").remove();
            location.reload();
          }, 1500);
        } else {
          showAlert(response.message, "error");
          submitBtn.prop("disabled", false).text(originalText);
        }
      },
      error: function (xhr) {
        const response = xhr.responseJSON || {};
        showAlert(
          response.message || "Failed to promote member to trainer",
          "error"
        );
        submitBtn.prop("disabled", false).text(originalText);
      },
    });
  });

  // Add New Trainer Button
  $("#btnAddNewTrainer").click(function () {
    $("#addTrainerModal").addClass("show");
  });

  // Close Add Trainer Modal
  $(".add-trainer-close, .add-trainer-cancel").click(function () {
    $("#addTrainerModal").removeClass("show");
    $("#addTrainerForm")[0].reset();
    $("#addTrainerMessage").addClass("hidden");
  });

  // Handle Add Trainer Form Submission
  $("#addTrainerForm").submit(function (e) {
    e.preventDefault();

    const formData = $(this).serialize();
    const submitBtn = $("#btnAddTrainer");
    const originalText = submitBtn.text();

    // Validate passwords match
    const password = $('input[name="password"]').val();
    const confirmPassword = $('input[name="confirm_password"]').val();

    if (password !== confirmPassword) {
      showAlert("Passwords do not match", "error");
      return;
    }

    submitBtn
      .prop("disabled", true)
      .html('<span class="loading"></span> Adding Trainer...');

    $.ajax({
      url: "index.php?controller=Trainer&action=addTrainer",
      method: "POST",
      data: formData,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          showAlert(response.message, "success");
          $("#addTrainerModal").removeClass("show");
          setTimeout(() => {
            location.reload();
          }, 1500);
        } else {
          showAlert(response.message, "error");
          submitBtn.prop("disabled", false).text(originalText);
        }
      },
      error: function (xhr) {
        const response = xhr.responseJSON || {};
        showAlert(response.message || "Failed to add trainer", "error");
        submitBtn.prop("disabled", false).text(originalText);
      },
    });
  });

  // View Trainer Details
  $(document).on("click", ".btn-view-trainer", function () {
    const trainerId = $(this).closest("tr").data("trainer-id");

    $.ajax({
      url: "index.php?controller=Admin&action=getTrainerData",
      method: "GET",
      data: { trainer_id: trainerId },
      dataType: "json",
      success: function (response) {
        if (response.success) {
          displayTrainerDetails(response.data);
          $("#viewTrainerModal").addClass("show");
        } else {
          showAlert("Failed to load trainer details", "error");
        }
      },
      error: function () {
        showAlert("Error loading trainer details", "error");
      },
    });
  });

  function displayTrainerDetails(trainer) {
    const html = `
          <div class="bg-gray-800 rounded-lg p-4 mb-3">
              <h4 class="text-lg font-semibold text-white mb-3">Personal Information</h4>
              <div class="space-y-2">
                  <p class="text-gray-300"><strong>Name:</strong> ${trainer.name
      }</p>
                  <p class="text-gray-300"><strong>Email:</strong> ${trainer.email
      }</p>
                  <p class="text-gray-300"><strong>Contact:</strong> ${trainer.contact_no || "N/A"
      }</p>
              </div>
          </div>
          
          <div class="bg-gray-800 rounded-lg p-4 mb-3">
              <h4 class="text-lg font-semibold text-white mb-3">Trainer Details</h4>
              <div class="space-y-2">
                  <p class="text-gray-300"><strong>Specialization:</strong> ${trainer.specialization
      }</p>
                  <p class="text-gray-300"><strong>Experience:</strong> ${trainer.experience_years
      } years</p>
                  <p class="text-gray-300"><strong>Join Date:</strong> ${trainer.join_date
      }</p>
                  <p class="text-gray-300">
                      <strong>Status:</strong> 
                      <span class="status-badge ${trainer.status === "active"
        ? "status-active"
        : "status-inactive"
      }">
                          ${trainer.status}
                      </span>
                  </p>
              </div>
          </div>
      `;

    $("#trainerDetails").html(html);

    // Store trainer data for edit
    $("#btnEditTrainer").data("trainer-data", trainer);
  }

  // Close View Trainer Modal
  $(".view-trainer-close").click(function () {
    $("#viewTrainerModal").removeClass("show");
  });

  // Edit Trainer Button (from view modal)
  $("#btnEditTrainer").click(function () {
    const trainerData = $(this).data("trainer-data");

    // Populate edit form
    $("#edit_trainer_id").val(trainerData.trainer_id);
    $("#edit_user_trainer_id").val(trainerData.user_id);
    $("#edit_trainer_first_name").val(trainerData.first_name);
    $("#edit_trainer_last_name").val(trainerData.last_name);
    $("#edit_trainer_middle_name").val(trainerData.middle_name);
    $("#edit_trainer_email").val(trainerData.email);
    $("#edit_trainer_contact_no").val(trainerData.contact_no);
    $("#edit_specialization").val(trainerData.specialization);
    $("#edit_experience_years").val(trainerData.experience_years);
    $("#edit_trainer_status").val(trainerData.status);

    // Close view modal and open edit modal
    $("#viewTrainerModal").removeClass("show");
    $("#editTrainerModal").addClass("show");
  });

  // Edit Trainer Direct Button
  $(document).on("click", ".btn-edit-trainer", function () {
    const trainerId = $(this).closest("tr").data("trainer-id");

    $.ajax({
      url: "index.php?controller=Admin&action=getTrainerData",
      method: "GET",
      data: { trainer_id: trainerId },
      dataType: "json",
      success: function (response) {
        if (response.success) {
          const trainer = response.data;

          // Populate edit form
          $("#edit_trainer_id").val(trainer.trainer_id);
          $("#edit_user_trainer_id").val(trainer.user_id);
          $("#edit_trainer_first_name").val(trainer.first_name);
          $("#edit_trainer_last_name").val(trainer.last_name);
          $("#edit_trainer_middle_name").val(trainer.middle_name);
          $("#edit_trainer_email").val(trainer.email);
          $("#edit_trainer_contact_no").val(trainer.contact_no);
          $("#edit_specialization").val(trainer.specialization);
          $("#edit_experience_years").val(trainer.experience_years);
          $("#edit_trainer_status").val(trainer.status);

          $("#editTrainerModal").addClass("show");
        } else {
          showAlert("Failed to load trainer details", "error");
        }
      },
      error: function () {
        showAlert("Error loading trainer details", "error");
      },
    });
  });

  // Close Edit Trainer Modal
  $(".edit-trainer-close, .edit-trainer-cancel").click(function () {
    $("#editTrainerModal").removeClass("show");
    $("#editTrainerForm")[0].reset();
    $("#editTrainerMessage").addClass("hidden");
  });

  // Handle Update Trainer Form Submission
  $("#editTrainerForm").submit(function (e) {
    e.preventDefault();

    const formData = $(this).serialize();
    const submitBtn = $("#btnUpdateTrainer");
    const originalText = submitBtn.text();

    // Validate passwords match if provided
    const password = $("#edit_trainer_password").val();
    const confirmPassword = $("#edit_confirm_trainer_password").val();

    if (password && password !== confirmPassword) {
      showAlert("Passwords do not match", "error");
      return;
    }

    submitBtn
      .prop("disabled", true)
      .html('<span class="loading"></span> Updating...');

    $.ajax({
      url: "index.php?controller=Trainer&action=updateTrainer",
      method: "POST",
      data: formData,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          showAlert(response.message, "success");
          $("#editTrainerModal").removeClass("show");
          setTimeout(() => {
            location.reload();
          }, 1500);
        } else {
          showAlert(response.message, "error");
          submitBtn.prop("disabled", false).text(originalText);
        }
      },
      error: function (xhr) {
        const response = xhr.responseJSON || {};
        showAlert(response.message || "Failed to update trainer", "error");
        submitBtn.prop("disabled", false).text(originalText);
      },
    });
  });

  // Delete/Deactivate Trainer
  $(document).on("click", ".btn-delete-trainer", function () {
    const trainerId = $(this).closest("tr").data("trainer-id");

    $("#delete_trainer_id").val(trainerId);
    $("#deleteTrainerModal").addClass("show");
  });

  // Close Delete Trainer Modal
  $(".delete-trainer-modal-close, .delete-trainer-cancel").click(function () {
    $("#deleteTrainerModal").removeClass("show");
  });

  // Handle Delete Trainer Form Submission
  $("#deleteTrainerForm").submit(function (e) {
    e.preventDefault();

    const trainerId = $("#delete_trainer_id").val();
    const submitBtn = $("#deleteTrainerBtn");
    const originalText = submitBtn.text();

    submitBtn
      .prop("disabled", true)
      .html('<span class="loading"></span> Processing...');

    $.ajax({
      url: "index.php?controller=Trainer&action=deleteTrainer",
      method: "POST",
      data: { trainer_id: trainerId },
      dataType: "json",
      success: function (response) {
        if (response.success) {
          showAlert(response.message, "success");
          $("#deleteTrainerModal").removeClass("show");
          setTimeout(() => {
            location.reload();
          }, 1500);
        } else {
          showAlert(response.message, "error");
          submitBtn.prop("disabled", false).text(originalText);
        }
      },
      error: function (xhr) {
        const response = xhr.responseJSON || {};
        showAlert(response.message || "Failed to deactivate trainer", "error");
        submitBtn.prop("disabled", false).text(originalText);
      },
    });
  });
  // 1. Open Modal
  $("#btnAddPlan").click(function () {
    $("#addPlanModal").addClass("show");
  });

  // 2. Close Modal
  $(".add-plan-close, .add-plan-cancel").click(function () {
    $("#addPlanModal").removeClass("show");
  });

  // 3. Submit Form
  $("#addPlanForm").submit(function (e) {
    e.preventDefault();

    const $btn = $("#btnAddPlanSubmit");
    const originalText = $btn.html();
    $btn
      .prop("disabled", true)
      .html('<i class="fas fa-spinner fa-spin"></i> Creating...');

    const $msgBox = $("#addPlanMessage");

    $.ajax({
      url: $(this).attr("action"),
      type: "POST",
      data: $(this).serialize(),
      dataType: "json",
      success: function (response) {
        if (response.success) {
          // Success Message (Green)
          $msgBox
            .removeClass("hidden text-red-400 bg-red-500/10")
            .addClass("text-emerald-400 bg-emerald-500/10")
            .html(
              `<i class="fas fa-check-circle mr-2"></i> ${response.message}`
            )
            .fadeIn();

          // Reload after delay
          setTimeout(function () {
            $("#addPlanModal").removeClass("show");
            location.reload();
          }, 1500);
        } else {
          // Error Message (Red)
          $msgBox
            .removeClass("hidden text-emerald-400 bg-emerald-500/10")
            .addClass("text-red-400 bg-red-500/10")
            .html(
              `<i class="fas fa-exclamation-triangle mr-2"></i> ${response.message}`
            )
            .fadeIn();

          $btn.prop("disabled", false).html(originalText);
        }
      },
      error: function () {
        $msgBox
          .removeClass("hidden text-emerald-400 bg-emerald-500/10")
          .addClass("text-red-400 bg-red-500/10")
          .html(
            `<i class="fas fa-times-circle mr-2"></i> An unexpected error occurred.`
          )
          .fadeIn();

        $btn.prop("disabled", false).html(originalText);
      },
    });
  });
  // 1. Open Modal & Fetch Data
  $(document).on("click", ".btn-edit-plan", function () {
    let planId = $(this).data("plan-id");

    if (!planId) return;

    // Fetch data
    $.ajax({
      url: "index.php?controller=Plan&action=getPlanData",
      type: "GET",
      data: { plan_id: planId },
      dataType: "json",
      success: function (response) {
        if (response.success) {
          let p = response.data;

          // Populate fields
          $("#edit_plan_id").val(p.plan_id);
          $("#edit_plan_name").val(p.plan_name);
          $("#edit_plan_price").val(p.price);
          $("#edit_plan_duration").val(p.duration_months);
          $("#edit_plan_description").val(p.description);
          $("#edit_plan_status").val(p.status);

          // Show Modal
          $("#editPlanModal").addClass("show");
        } else {
          showNotification("error", response.message);
        }
      },
      error: function () {
        showNotification("error", "Failed to load plan details.");
      },
    });
  });
  // ===== PAYMENT ACTIONS =====

  // --- View Payment ---
  $(document).on("click", ".btn-view-payment", function () {
    const paymentId = $(this).data("payment-id");

    // Show modal with loading state
    $("#viewPaymentModal").addClass("show");

    $.ajax({
      url: "index.php?controller=Payment&action=getPaymentData",
      type: "GET",
      data: { payment_id: paymentId },
      dataType: "json",
      success: function (response) {
        if (response.success && response.data) {
          const p = response.data;
          const date = new Date(p.payment_date).toLocaleDateString('en-US', {
            year: 'numeric', month: 'long', day: 'numeric',
            hour: '2-digit', minute: '2-digit'
          });

          const html = `
                    <div class="space-y-4">
                        <div class="flex justify-between items-center bg-slate-800 p-4 rounded-lg">
                            <div>
                                <p class="text-xs text-slate-400 uppercase">Amount Paid</p>
                                <p class="text-2xl font-bold text-white">₱${new Intl.NumberFormat().format(p.amount)}</p>
                            </div>
                            <span class="status-badge ${p.status === 'paid' ? 'status-paid' : 'status-pending'}">
                                ${p.status.charAt(0).toUpperCase() + p.status.slice(1)}
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-slate-800/50 p-3 rounded-lg">
                                <p class="text-xs text-slate-500 mb-1">Transaction ID</p>
                                <p class="text-white font-mono text-sm">${p.payment_id}</p>
                            </div>
                            <div class="bg-slate-800/50 p-3 rounded-lg">
                                <p class="text-xs text-slate-500 mb-1">Date</p>
                                <p class="text-white text-sm">${date}</p>
                            </div>
                            <div class="bg-slate-800/50 p-3 rounded-lg">
                                <p class="text-xs text-slate-500 mb-1">Member</p>
                                <p class="text-white text-sm">${p.member_name || 'N/A'}</p>
                            </div>
                            <div class="bg-slate-800/50 p-3 rounded-lg">
                                <p class="text-xs text-slate-500 mb-1">Plan</p>
                                <p class="text-white text-sm">${p.plan_name || 'N/A'}</p>
                            </div>
                        </div>
                    </div>
                `;
          $("#viewPaymentContent").html(html);
        } else {
          $("#viewPaymentContent").html('<p class="text-red-400 text-center">Failed to load details</p>');
        }
      },
      error: function () {
        $("#viewPaymentContent").html('<p class="text-red-400 text-center">Error loading details</p>');
      }
    });
  });

  $(".view-payment-close").click(function () {
    $("#viewPaymentModal").removeClass("show");
    setTimeout(() => {
      $("#viewPaymentContent").html(`
            <div class="animate-pulse space-y-4">
                <div class="h-4 bg-slate-800 rounded w-3/4"></div>
                <div class="h-4 bg-slate-800 rounded w-1/2"></div>
            </div>
        `);
    }, 300);
  });

  // --- Refund Payment ---
  $(document).on("click", ".btn-refund-payment", function () {
    const paymentId = $(this).data("payment-id");
    $("#refund_payment_id").val(paymentId);
    $("#refundPaymentModal").addClass("show");
  });

  $(".refund-payment-cancel").click(function () {
    $("#refundPaymentModal").removeClass("show");
  });

  $("#refundPaymentForm").submit(function (e) {
    e.preventDefault();
    const btn = $("#refundPaymentBtn");
    const originalText = btn.text();
    btn.html('<span class="loading"></span>').prop("disabled", true);

    $.ajax({
      url: "index.php?controller=Payment&action=refundPayment",
      type: "POST",
      data: $(this).serialize(),
      dataType: "json",
      success: function (response) {
        if (response.success) {
          showAlert("✓ Payment refunded successfully", "success");
          setTimeout(() => {
            location.reload();
          }, 1500);
        } else {
          $("#refundPaymentMessage").html(response.message).removeClass("hidden").addClass("text-red-400");
          btn.html(originalText).prop("disabled", false);
        }
      },
      error: function () {
        $("#refundPaymentMessage").html("An error occurred").removeClass("hidden").addClass("text-red-400");
        btn.html(originalText).prop("disabled", false);
      }
    });
  });

  // --- Remind Payment ---
  $(document).on("click", ".btn-remind-payment", function () {
    const paymentId = $(this).data("payment-id");
    const btn = $(this);
    const originalContent = btn.html();

    if (!confirm("Send payment reminder to member?")) return;

    btn.html('<i class="fas fa-spinner fa-spin"></i>').prop("disabled", true);

    $.ajax({
      url: "index.php?controller=Payment&action=sendReminder",
      type: "POST",
      data: { payment_id: paymentId },
      dataType: "json",
      success: function (response) {
        if (response.success) {
          showAlert("✓ Reminder sent successfully", "success");
        } else {
          showAlert("Failed to send reminder", "error");
        }
        btn.html(originalContent).prop("disabled", false);
      },
      error: function () {
        showAlert("Error sending reminder", "error");
        btn.html(originalContent).prop("disabled", false);
      }
    });
  });

  // 2. Close Modal
  $(".edit-plan-close, .edit-plan-cancel").click(function () {
    $("#editPlanModal").removeClass("show");
  });

  // 3. Submit Form
  $("#editPlanForm").submit(function (e) {
    e.preventDefault();

    const $btn = $("#btnUpdatePlan");
    const originalText = $btn.html();
    $btn
      .prop("disabled", true)
      .html('<i class="fas fa-spinner fa-spin"></i> Updating...');

    const $msgBox = $("#editPlanMessage");

    $.ajax({
      url: $(this).attr("action"),
      type: "POST",
      data: $(this).serialize(),
      dataType: "json",
      success: function (response) {
        if (response.success) {
          // Success Message
          $msgBox
            .removeClass("hidden text-red-400 bg-red-500/10")
            .addClass("text-emerald-400 bg-emerald-500/10")
            .html(
              `<i class="fas fa-check-circle mr-2"></i> ${response.message}`
            )
            .fadeIn();

          // Reload after delay
          setTimeout(function () {
            $("#editPlanModal").removeClass("show");
            location.reload();
          }, 1500);
        } else {
          // Error Message
          $msgBox
            .removeClass("hidden text-emerald-400 bg-emerald-500/10")
            .addClass("text-red-400 bg-red-500/10")
            .html(
              `<i class="fas fa-exclamation-triangle mr-2"></i> ${response.message}`
            )
            .fadeIn();

          $btn.prop("disabled", false).html(originalText);
        }
      },
      error: function () {
        $msgBox
          .removeClass("hidden text-emerald-400 bg-emerald-500/10")
          .addClass("text-red-400 bg-red-500/10")
          .html(
            `<i class="fas fa-times-circle mr-2"></i> An unexpected error occurred.`
          )
          .fadeIn();

        $btn.prop("disabled", false).html(originalText);
      },
    });
  });
  // Alert Function
  function showAlert(message, type) {
    const alertClass = type === "success" ? "bg-green-500" : "bg-red-500";
    const icon = type === "success" ? "✓" : "✕";

    const alert = `
          <div class="alert ${alertClass} text-white px-6 py-4 rounded-lg shadow-lg flex items-center">
              <span class="mr-3 text-2xl">${icon}</span>
              <span>${message}</span>
          </div>
      `;

    $("#alertContainer").append(alert);

    setTimeout(() => {
      $("#alertContainer .alert")
        .first()
        .fadeOut(300, function () {
          $(this).remove();
        });
    }, 5000);
  }
});

$(document).ready(function () {
  // ===== PENDING REGISTRATIONS LOGIC =====

  // 1. Tab Click Event
  $(".tab-button[data-tab='pending']").on("click", function () {
    loadPendingUsers();
  });

  // 2. Refresh Button
  $("#btnRefreshPending").on("click", function () {
    loadPendingUsers();
  });

  // 3. Load Data Function
  function loadPendingUsers() {
    $("#pendingUsersTableBody").html('<tr><td colspan="5" class="p-6 text-center text-slate-500">Loading...</td></tr>');

    $.ajax({
      url: "index.php?controller=Admin&action=getPendingRegistrations",
      type: "GET",
      dataType: "json",
      success: function (response) {
        if (response.success) {
          renderPendingUsers(response.data);
        } else {
          $("#pendingUsersTableBody").html('<tr><td colspan="5" class="p-6 text-center text-red-400">Failed to load data</td></tr>');
        }
      },
      error: function () {
        $("#pendingUsersTableBody").html('<tr><td colspan="5" class="p-6 text-center text-red-400">Error fetching data</td></tr>');
      }
    });
  }

  // 4. Render Table
  function renderPendingUsers(users) {
    if (!users || users.length === 0) {
      $("#pendingUsersTableBody").html('<tr><td colspan="5" class="p-6 text-center text-slate-500">No pending registrations found.</td></tr>');
      return;
    }

    let html = "";
    users.forEach((user) => {
      // Fix path issue: DB stores 'public/uploads/...', but app runs in 'public/', 
      // so relative link becomes 'public/public/...'. We must strip 'public/' prefix.

      let dbPath = user.valid_id_picture;
      if (dbPath && dbPath.startsWith('public/')) {
        dbPath = dbPath.substring(7); // Remove 'public/'
      }

      // Default path should also be relative to public/ (so just 'assets/...')
      let idProof = dbPath || 'assets/images/default-id.png';
      // Assuming response gives 'public/uploads/...'
      // If we are in 'views/admin/', we might need to adjust or use absolute path
      // Ideally backend returns 'public/uploads/...', so we can prepend root or '../' if needed.
      // Let's assume root-relative path for safety if using <base> or just '../'
      // Since we are in /views/admin/ (via URL routing), assets are usually relative to root index.php
      // So 'public/uploads/...' should work fine if <img src="public/...">

      html += `
        <tr class="table-row">
            <td class="p-4">
                <div class="flex items-center">
                    <div class="w-9 h-9 bg-orange-500/20 text-orange-400 rounded-full flex items-center justify-center font-bold text-sm mr-3">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div>
                        <p class="font-medium text-white">${user.name}</p>
                        <p class="text-xs text-slate-500">ID: ${user.user_id}</p>
                    </div>
                </div>
            </td>
            <td class="p-4 text-sm text-slate-400">${user.email}</td>
            <td class="p-4 text-sm text-slate-400">${user.created_at}</td>
            <td class="p-4">
                <div class="w-12 h-8 rounded overflow-hidden cursor-pointer border border-slate-600 hover:border-blue-500 transition-colors btn-view-id-preview" 
                     data-img="${idProof}">
                    <img src="${idProof}" alt="ID" class="w-full h-full object-cover">
                </div>
            </td>
            <td class="p-4 text-center">
                <button class="btn-view-pending px-3 py-1.5 bg-slate-700 hover:bg-slate-600 text-slate-300 text-xs font-medium rounded-lg transition-colors border border-slate-600"
                    data-user-id="${user.user_id}"
                    data-name="${user.name}"
                    data-email="${user.email}"
                    data-img="${idProof}"
                    data-joined="${user.created_at}"
                >
                    Review
                </button>
            </td>
        </tr>
      `;
    });
    $("#pendingUsersTableBody").html(html);
  }

  // 5. Open View Modal
  $(document).on("click", ".btn-view-pending, .btn-view-id-preview", function (e) {
    e.stopPropagation();
    let btn = $(this);

    if ($(this).hasClass('btn-view-id-preview')) {
      btn = $(this).closest('tr').find('.btn-view-pending');
    }

    const userId = btn.data("user-id");
    const name = btn.data("name");
    const email = btn.data("email");
    const img = btn.data("img");
    const joined = btn.data("joined");

    $("#viewIdImage").attr("src", img);
    $("#viewIdUserDetails").html(`
          <div class="grid grid-cols-2 gap-4">
              <div>
                  <p class="text-slate-500 text-xs text-center md:text-left">Full Name</p>
                  <p class="text-white font-medium text-center md:text-left">${name}</p>
              </div>
               <div>
                  <p class="text-slate-500 text-xs text-center md:text-left">Email</p>
                  <p class="text-white font-medium text-center md:text-left">${email}</p>
              </div>
               <div>
                  <p class="text-slate-500 text-xs text-center md:text-left">Registered On</p>
                  <p class="text-white font-medium text-center md:text-left">${joined}</p>
              </div>
          </div>
      `);

    $("#btnApproveUser").data("user-id", userId);
    $("#btnRejectUser").data("user-id", userId);

    $("#viewIdModal").addClass("show");
  });

  $(".view-id-close").click(function () {
    $("#viewIdModal").removeClass("show");
  });

  // 6. Action Buttons
  $("#btnApproveUser").click(function () {
    const userId = $(this).data("user-id");
    processUserApproval(userId, 'approveUserId');
  });

  $("#btnRejectUser").click(function () {
    const userId = $(this).data("user-id");
    if (confirm('Are you sure you want to REJECT this registration?')) {
      processUserApproval(userId, 'rejectUserId');
    }
  });

  function processUserApproval(userId, action) {
    const btn = action === 'approveUserId' ? $("#btnApproveUser") : $("#btnRejectUser");
    const originalText = btn.text();
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

    $.ajax({
      url: "index.php?controller=Admin&action=" + action,
      type: "POST",
      data: { user_id: userId },
      dataType: "json",
      success: function (response) {
        if (response.success) {
          // Re-use external showAlert if available, otherwise fallback
          if (typeof showAlert === 'function') {
            showAlert("✓ " + response.message, "success");
          } else {
            alert(response.message);
          }

          $("#viewIdModal").removeClass("show");
          loadPendingUsers();
        } else {
          if (typeof showAlert === 'function') {
            showAlert("Error: " + response.message, "error");
          } else {
            alert("Error: " + response.message);
          }
        }
        btn.prop('disabled', false).text(originalText);
      },
      error: function () {
        alert("An error occurred");
        btn.prop('disabled', false).text(originalText);
      }
    });
  }
});
