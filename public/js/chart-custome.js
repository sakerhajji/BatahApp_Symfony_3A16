/* ----- Employee Dashboard Chart Js For Applications Statistics ----- */
function createConfig() {
    return {
        type: 'line',
        data: {
            labels: ['April', 'May', 'June', 'July', 'August'],
            datasets: [{
                label: 'Dataset',
                borderColor: window.chartColors.blue,
                backgroundColor: window.chartColors.blue,
                data: [80, 20, 53, 0, 50],
                fill: false,
            }]
        },
        options: {
            responsive: true,
            title: {
                display: true,
                text: 'Sample tooltip with border'
            },
            tooltips: {
                position: 'nearest',
                mode: 'index',
                intersect: false,
                yPadding: 10,
                xPadding: 10,
                caretSize: 8,
                backgroundColor: 'rgba(72, 241, 12, 1)',
                titleFontColor: window.chartColors.black,
                bodyFontColor: window.chartColors.black,
                borderColor: 'rgba(0,0,0,1)',
                borderWidth: 4
            },
        }
    };
}
window.onload = function() {
    var c_container = document.querySelector('.c_container');
    var div = document.createElement('div');
    div.classList.add('chart-container');

    var canvas = document.createElement('canvas');
    div.appendChild(canvas);
    c_container.appendChild(div);

    var ctx = canvas.getContext('2d');
    var config = createConfig();
    new Chart(ctx, config);
};

// Circle Doughnut Chart
var ctx = document.getElementById('myChart').getContext('2d');
var chart = new Chart(ctx, {
    // The type of chart we want to create
    type: 'doughnut',

    // The data for our dataset
    data: {
        labels: [' Principal and Interest 21,972', 'Property Taxes $1,068', 'HOA Dues $2,036'],
        datasets: [{
            label: 'My First dataset',
            segmentShowStroke : true,
            segmentStrokeColor : "transparent",
            segmentStrokeWidth : 17,
            backgroundColor: ["#0061DF", "#4585ff", "#fb8855"],
            data: [50, 25, 25],
            responsive: true,
            showScale: true
        }]
    },

    // Configuration options go here
    options: {
        cutoutPercentage : 85,
        responsive: true,
    }
});

// BarChart Style
var data = {
  labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul"],
  datasets: [{
    label: "Dataset #1",
    backgroundColor: "rgba(255,99,132,0.2)",
    borderColor: "rgba(255,99,132,1)",
    borderWidth: 2,
    hoverBackgroundColor: "rgba(255,99,132,0.4)",
    hoverBorderColor: "rgba(255,99,132,1)",
    data: [65, 59, 20, 81, 56, 55, 40],
  }]
};

var options = {
  maintainAspectRatio: false,
  scales: {
    yAxes: [{
      stacked: true,
      gridLines: {
        display: true,
        color: "rgba(255,99,132,0.2)"
      }
    }],
    xAxes: [{
      gridLines: {
        display: false
      }
    }]
  }
};

Chart.Bar('chart', {
  options: options,
  data: data
});

// LineChart Style 2
var ctx = document.getElementById('myChartweave').getContext('2d');
var chart = new Chart(ctx, {
    // The type of chart we want to create
    type: 'line', // also try bar or other graph types
    // The data for our dataset
    data: {
        labels: ["Text", "Text", "Text", "Text", "Text"],
        // Information about the dataset
        datasets: [{
            label: "Rainfall",
            backgroundColor: 'lightblue',
            borderColor: 'lightblue',
            data: [35, 48, 25, 8, 68],
        }]
    },

    // Configuration options
    options: {
        layout: {
          padding: 10,
        },
        legend: {
            position: 'bottom',
        },
        title: {
            display: true,
            // text: 'Precipitation in Toronto'
        },
        scales: {
            yAxes: [{
                scaleLabel: {
                    display: true,
                    // labelString: 'Precipitation in mm'
                }
            }],
            xAxes: [{
                scaleLabel: {
                    display: true,
                    // labelString: 'Month of the Year'
                }
            }]
        }
    }
});