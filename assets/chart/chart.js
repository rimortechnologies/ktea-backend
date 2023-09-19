// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Roboto,sans-serif';
Chart.defaults.global.defaultFontColor = '#999999';

// chart 1
var ctx = document.getElementById("myPieChart1");
var myPieChart = new Chart(ctx, {
  type: 'pie',
  data: {
    labels: ["Leads", "Clients"],
    datasets: [{
      data: [500, 325],
      backgroundColor: ['#5a48b2', '#f6329f'],
    }],
  },
});

// chart 2
var ctx = document.getElementById("myPieChart2");
var myPieChart = new Chart(ctx, {
  type: 'doughnut',
  data: {
    labels: ["Interviews", "Offers Made", "Rejected/Dropped"],
    datasets: [{
      data: [25, 15, 10],
      backgroundColor: ['#006ed6', '#7cd000', '#ff2636'],
    }],
  },
});

// chart 3
var ctx = document.getElementById("myBarChart");
var myLineChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["Open Positions", "Submissions"],
    datasets: [{
      label: "Sales",
      backgroundColor: "rgba(76,79,98,1)",
      borderColor: "rgba(76,79,98,1)",
      data: [400, 100],
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
          maxTicksLimit: 7
        }
      }],
      yAxes: [{
        ticks: {
          min: 0,
          max: 500,
          maxTicksLimit: 10
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
      data: [500, 250, 100, 2500, 450, 15, 15, 10],
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