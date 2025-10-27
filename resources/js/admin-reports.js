/**
 * Admin Reports Dashboard Charts
 * ------------------------------------
 * Handles rendering of charts for:
 * - Job Status Overview
 * - Applications Breakdown
 * - Monthly Jobs Trend
 */

document.addEventListener("DOMContentLoaded", function () {
  // ✅ Check that chart data is available
  if (!window.dashboardData) {
    console.warn("⚠️ dashboardData not found.");
    return;
  }

  const { jobLabels, jobValues, appLabels, appValues, monthLabels, monthValues } = window.dashboardData;

  // ===== Job Status Pie Chart =====
  const jobCtx = document.getElementById("jobStatusChart");
  if (jobCtx) {
    new Chart(jobCtx, {
      type: "pie",
      data: {
        labels: jobLabels,
        datasets: [{
          data: jobValues,
          backgroundColor: ["#00b3ff", "#00c9a7", "#ffc107", "#ef4444"]
        }]
      }
    });
  }

  // ===== Applications Doughnut Chart =====
  const appCtx = document.getElementById("applicationChart");
  if (appCtx) {
    new Chart(appCtx, {
      type: "doughnut",
      data: {
        labels: appLabels,
        datasets: [{
          data: appValues,
          backgroundColor: ["#00c9a7", "#00b3ff", "#ef4444"]
        }]
      }
    });
  }

  // ===== Monthly Jobs Line Chart =====
  const monthlyCtx = document.getElementById("monthlyJobsChart");
  if (monthlyCtx) {
    new Chart(monthlyCtx, {
      type: "line",
      data: {
        labels: monthLabels,
        datasets: [{
          label: "Jobs Posted",
          data: monthValues,
          borderColor: "#00b3ff",
          backgroundColor: "rgba(0,179,255,0.2)",
          tension: 0.3,
          fill: true
        }]
      }
    });
  }
});
