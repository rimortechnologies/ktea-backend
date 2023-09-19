// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Roboto,sans-serif';
Chart.defaults.global.defaultFontColor = '#999999';

// dashboard
var ctx = document.getElementById("overall");
var myLineChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["Leads", "Clients", "Open Positions", "Open Candidates", "Submissions", "Interviews", "Offers Made", "Rejected/Dropped"],
    datasets: [{
      label: "Total",
      backgroundColor: "rgba(0,70,128,1)",
      borderColor: "rgba(0,70,128,1)",
      data: [500, 250, 100, 2500, 150, 15, 15, 10],
    }],
  },
  options: {
    scales: {
      xAxes: [{
        time: {
          unit: 'month'
        },
        gridLines: {
          display: false
        },
        ticks: {
          maxTicksLimit: 11
        }
      }],
      yAxes: [{
        ticks: {
          min: 0,
          max: 3000,
          maxTicksLimit: 5
        },
        gridLines: {
          display: true
        }
      }],
    },
    legend: {
      display: false
    }
  }
});