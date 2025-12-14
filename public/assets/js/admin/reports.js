// Admin Reports & Analytics Logic

$(document).ready(function () {
  // Store chart instances globally
  window.chartInstances = {};

  // 1. Filter Logic
  // Show/hide custom date range
  $("#dateRangeFilter").on("change", function () {
    if ($(this).val() === "custom") {
      $("#customDateRange").removeClass("hidden");
      // Set default dates
      $("#endDate").val(new Date().toISOString().split("T")[0]);
      $("#startDate").val(
        new Date(Date.now() - 30 * 24 * 60 * 60 * 1000)
          .toISOString()
          .split("T")[0]
      );
    } else {
      $("#customDateRange").addClass("hidden");
    }
  });

  // Apply Filter
  $("#btnApplyFilter").on("click", function () {
    applyChartFilters();
  });

  // Reset Filter
  $("#btnResetFilter").on("click", function () {
    $("#dateRangeFilter").val("30");
    $("#chartTypeFilter").val("all");
    $("#customDateRange").addClass("hidden");
    $("#filterSummary").addClass("hidden");
    location.reload(); // Reload page to show original data
  });

  // 2. Export Logic
  $("#btnExportCSV").on("click", function () {
    exportToCSV();
  });

  // 3. Core Functions
  function applyChartFilters() {
    const dateRange = $("#dateRangeFilter").val();
    const chartType = $("#chartTypeFilter").val();
    let startDate = "";
    let endDate = "";

    // Show loading
    $("#loadingIndicator").removeClass("hidden");

    // Get date parameters
    if (dateRange === "custom") {
      startDate = $("#startDate").val();
      endDate = $("#endDate").val();

      if (!startDate || !endDate) {
        showAlert("Please select both start and end dates", "error");
        $("#loadingIndicator").addClass("hidden");
        return;
      }
    }

    // Make AJAX request
    $.ajax({
      url: "index.php?controller=Admin&action=getFilteredReportData",
      method: "GET",
      data: {
        date_range: dateRange,
        start_date: startDate,
        end_date: endDate,
        chart_type: chartType,
      },
      dataType: "json",
      success: function (response) {
        if (response.success) {
          // Update charts with new data
          updateAllCharts(response.data, chartType);

          // Update KPIs
          updateKPIs(response.data);

          // Show filter summary
          const filterText =
            dateRange === "custom"
              ? `${response.data.filter_info.formatted_start} to ${response.data.filter_info.formatted_end}`
              : `Last ${dateRange} Days`;
          $("#filterSummaryText").text(filterText);
          $("#filterSummary").removeClass("hidden");

          showAlert("Charts updated successfully", "success");
        } else {
          showAlert("Error loading filtered data", "error");
        }
        $("#loadingIndicator").addClass("hidden");
      },
      error: function (xhr, status, error) {
        showAlert("Error: " + error, "error");
        $("#loadingIndicator").addClass("hidden");
      },
    });
  }

  function updateKPIs(data) {
    // Safe check for data existence
    if (!data) return;

    // Money formatting helper
    const formatMoney = (amount) => '₱' + parseFloat(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const formatNum = (num) => parseInt(num).toLocaleString('en-US');

    // Revenue
    if (data.payment_stats) {
      $('#kpiTotalRevenue').text(formatMoney(data.payment_stats.total_paid));
      $('#kpiTransactionCount').text(formatNum(data.payment_stats.paid_count) + ' successful transactions');

      // Avg Transaction
      const avg = data.payment_stats.paid_count > 0 ? (data.payment_stats.total_paid / data.payment_stats.paid_count) : 0;
      $('#kpiAvgTransaction').text(formatMoney(avg));

      // Success Rate
      const totalTrans = data.payment_stats.total_transactions;
      const successRate = totalTrans > 0 ? ((data.payment_stats.paid_count / totalTrans) * 100).toFixed(1) : 0;
      $('#kpiSuccessRate').text(successRate + '%');
      $('#kpiFailedCount').text(data.payment_stats.failed_count || 0);
    }

    // Pending
    if (data.pending_payments) {
      $('#kpiPendingCount').text(formatNum(data.pending_payments.pending_count));
      $('#kpiPendingAmount').text(formatMoney(data.pending_payments.pending_amount));
    }

    // Members
    if (data.active_inactive_count) {
      $('#kpiActiveMembers').text(formatNum(data.active_inactive_count.active_count));
      $('#kpiInactiveMembers').text(formatNum(data.active_inactive_count.inactive_count));
    }

    // Retention
    if (data.retention_rate) {
      $('#kpiRetentionRate').text(data.retention_rate.rate + '%');
    }

    // Expiring
    if (data.expiring_subscriptions) {
      $('#kpiExpiringCount').text(formatNum(data.expiring_subscriptions.expiring_count));
    }
  }

  function updateAllCharts(data, chartType) {
    console.log("Updating charts with:", data);

    // Update Revenue Trend Chart
    if (chartType === "all" || chartType === "revenue") {
      updateChart("revenueTrendChart", {
        labels: data.revenue_trend.map((d) => d.period_label),
        datasets: [
          {
            label: "Revenue (₱)",
            data: data.revenue_trend.map((d) => d.revenue),
            borderColor: "#3b82f6",
            backgroundColor: "rgba(59, 130, 246, 0.1)",
            fill: true,
            tension: 0.4,
            borderWidth: 2,
          },
        ],
      });
    }

    // Update Daily Revenue Chart
    if (chartType === "all" || chartType === "revenue") {
      updateChart("dailyRevenueChart", {
        labels: data.daily_revenue.map((d) => d.date_label),
        datasets: [
          {
            label: "Daily Revenue (₱)",
            data: data.daily_revenue.map((d) => d.revenue),
            backgroundColor: "rgba(34, 197, 94, 0.6)",
            borderColor: "#22c55e",
            borderWidth: 1,
            borderRadius: 4,
          },
        ],
      });
    }

    // Update Revenue by Plan Chart
    if (chartType === "all" || chartType === "revenue") {
      updateChart("revenueByPlanChart", {
        labels: data.revenue_by_plan.map((d) => d.plan_name),
        datasets: [
          {
            data: data.revenue_by_plan.map((d) => d.total_revenue),
            backgroundColor: [
              "rgba(59, 130, 246, 0.8)",
              "rgba(34, 197, 94, 0.8)",
              "rgba(251, 146, 60, 0.8)",
              "rgba(168, 85, 247, 0.8)",
              "rgba(236, 72, 153, 0.8)",
            ],
            borderWidth: 2,
            borderColor: "#1f2937",
          },
        ],
      });
    }

    // Update Member Growth Chart
    if (chartType === "all" || chartType === "members") {
      updateChart("memberGrowthChart", {
        labels: data.member_growth.map((d) => d.period_label),
        datasets: [
          {
            label: "New Members",
            data: data.member_growth.map((d) => d.new_members),
            borderColor: "#22c55e",
            backgroundColor: "rgba(34, 197, 94, 0.1)",
            fill: true,
            tension: 0.4,
            borderWidth: 2,
          },
        ],
      });
    }

    // Update Members by Plan Chart
    if (chartType === "all" || chartType === "members") {
      updateChart("membersByPlanChart", {
        labels: data.members_by_plan.map((d) => d.plan_name),
        datasets: [
          {
            label: "Members",
            data: data.members_by_plan.map((d) => d.member_count),
            backgroundColor: "rgba(168, 85, 247, 0.6)",
            borderColor: "#a855f7",
            borderWidth: 1,
            borderRadius: 4,
          },
        ],
      });
    }

    // Update Payment Method Chart
    if (chartType === "all" || chartType === "payments") {
      updateChart("paymentMethodChart", {
        labels: data.payment_method_stats.map(
          (d) => d.payment_method || "Unknown"
        ),
        datasets: [
          {
            data: data.payment_method_stats.map((d) => d.total_amount),
            backgroundColor: [
              "rgba(59, 130, 246, 0.8)",
              "rgba(34, 197, 94, 0.8)",
              "rgba(251, 146, 60, 0.8)",
              "rgba(236, 72, 153, 0.8)",
            ],
            borderWidth: 2,
            borderColor: "#1f2937",
          },
        ],
      });
    }

    // Members Status and Subscription Status charts can also be updated here if data exists
    if (data.active_inactive_count && (chartType === "all" || chartType === "members")) {
      updateChart("memberStatusChart", {
        labels: ['Active Members', 'Inactive Members'],
        datasets: [{
          data: [data.active_inactive_count.active_count, data.active_inactive_count.inactive_count],
          backgroundColor: [
            'rgba(34, 197, 94, 0.8)',
            'rgba(239, 68, 68, 0.8)'
          ],
          borderWidth: 2,
          borderColor: '#1e293b'
        }]
      });
    }
  }

  function updateChart(chartId, newData) {
    const canvas = document.getElementById(chartId);
    if (!canvas) return;

    // Get existing chart instance
    const existingChart = Chart.getChart(chartId);

    if (existingChart) {
      // Update existing chart
      existingChart.data = newData;
      existingChart.update("active");
    }
  }

  function exportToCSV() {
    // Collect data from the DOM or last requested data
    // For simplicity, we'll export the summary table data from the view
    let csv = [];
    const rows = document.querySelectorAll("table tr");

    for (let i = 0; i < rows.length; i++) {
      let row = [], cols = rows[i].querySelectorAll("td, th");
      for (let j = 0; j < cols.length; j++)
        row.push(cols[j].innerText.replace(/,/g, "")); // Clean commas
      csv.push(row.join(","));
    }

    // Also add header info
    const revenue = $('#kpiTotalRevenue').text().replace(/,/g, "");
    csv.unshift(["Total Revenue", revenue]);
    csv.unshift(["Report Generated", new Date().toLocaleString()]);

    downloadCSV(csv.join("\n"), "gymazing_report.csv");
  }

  function downloadCSV(csv, filename) {
    let csvFile;
    let downloadLink;

    csvFile = new Blob([csv], { type: "text/csv" });
    downloadLink = document.createElement("a");
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
  }

  function showAlert(message, type) {
    const alertClass =
      type === "success"
        ? "bg-green-600"
        : type === "error"
          ? "bg-red-600"
          : "bg-blue-600";

    const alert = $(`
            <div class="alert ${alertClass} text-white px-6 py-4 rounded-lg shadow-lg mb-4 animate-fade-in">
                ${message}
            </div>
        `);

    $("#alertContainer").append(alert);

    setTimeout(() => {
      alert.fadeOut(300, function () {
        $(this).remove();
      });
    }, 3000);
  }
});
