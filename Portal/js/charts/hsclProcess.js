function createHsclChart(id,title,means,instances,borders,expected){
    var canvas = document.getElementById(id);


    var ctx = canvas.getContext('2d');
    ctx.canvas.width = 300;
    ctx.canvas.height = 200;
    // Global Options:
    Chart.defaults.global.defaultFontColor = 'black';
    Chart.defaults.global.defaultFontSize = 16;   

    var colors = ["0,159,227", "229,48,18", "60,160,135"]
    var data = {
        labels: instances,
        datasets: [
            {
                label: "Verlauf",
                fill: false,
                lineTension: 0.1,
                backgroundColor: "rgba("+colors[0]+",1)",
                borderColor: "rgba("+colors[0]+",1)", // The main line color
                borderCapStyle: 'square',
                borderDash: [], // try [5, 15] for instance
                borderDashOffset: 0.0,
                borderJoinStyle: 'miter',
                borderWidth: 1,
                pointBorderColor: "rgba("+colors[0]+",1)",
                pointBackgroundColor: "rgba("+colors[0]+",1)",
                pointBorderWidth: 1,
                pointHoverRadius: 8,
                pointHoverBackgroundColor: "rgba("+colors[0]+",1)",
                pointHoverBorderColor: "rgba("+colors[0]+",1)",
                pointHoverBorderWidth: 2,
                pointRadius: 4,
                pointHitRadius: 10,
                // notice the gap in the data and the spanGaps: true
                data: means,
                spanGaps: true,
            }, 
            {
                label: "Grenze",
                fill: false,
                lineTension: 0.1,
                backgroundColor: "rgba("+colors[1]+",1)",
                borderColor: "rgba("+colors[1]+",1)", // The main line color
                borderCapStyle: 'square',
                borderDash: [], // try [5, 15] for instance
                borderDashOffset: 0.0,
                borderJoinStyle: 'miter',
                borderWidth: 1,
                pointBorderColor: "rgba("+colors[1]+",1)",
                pointBackgroundColor: "rgba("+colors[1]+",1)",
                pointBorderWidth: 1,
                pointHoverRadius: 8,
                pointHoverBackgroundColor: "rgba("+colors[1]+",1)",
                pointHoverBorderColor: "rgba("+colors[1]+",1)",
                pointHoverBorderWidth: 2,
                pointRadius: 0,
                pointHitRadius: 10,
                // notice the gap in the data and the spanGaps: false
                data: borders,
                spanGaps: false,
            },
            {
                label: "Erwartet",
                fill: false,
                lineTension: 0.1,
                backgroundColor: "rgba("+colors[2]+",1)",
                borderColor: "rgba("+colors[2]+",1)", // The main line color
                borderCapStyle: 'square',
                borderDash: [], // try [5, 15] for instance
                borderDashOffset: 0.0,
                borderJoinStyle: 'miter',
                borderWidth: 1,
                pointBorderColor: "rgba("+colors[2]+",1)",
                pointBackgroundColor: "rgba("+colors[2]+",1)",
                pointBorderWidth: 1,
                pointHoverRadius: 8,
                pointHoverBackgroundColor: "rgba("+colors[2]+",1)",
                pointHoverBorderColor: "rgba("+colors[2]+",1)",
                pointHoverBorderWidth: 2,
                pointRadius: 0,
                pointHitRadius: 10,
                // notice the gap in the data and the spanGaps: false
                data: expected,
                spanGaps: false,
            },
        ]
    };
    // Notice the scaleLabel at the same level as Ticks
    var options = {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true,
                    min: 1,
                    max: 4,
                    stepSize: 0.5,
                    fontSize: 12
                    
                },
                scaleLabel: {
                    display: true,
                    labelString: 'Mittelwert',
                    fontSize: 20
                }
                }],
            xAxes: [{
                ticks: {
                    fontSize: 12,
                    autoSkip: false,
                    callback: function(value,index,values){
                        if(index % Math.ceil(values.length / 20.0) == 0 || isNaN(value))
                            return value;
                    }
                }
                }]
            },
        title: {
            display: true,
            text: title,
            position: 'top'
        },
        // Begin Tooltips
        /* Zeigt alle Werte von einem Messzeitpunkt in einem Tooltip an.
        */
        responsive: true,
        title:{
            display: true,
        },
        tooltips: {
            mode: 'index',
            intersect: false,
        },
        // End Tooltips
    };
    // Chart declaration:
    var myBarChart = new Chart(ctx, {
        type: 'line',
        data: data,
        options: options
    });

}

function createOldHsclChart(id,title,scales,names,instances){
    var canvas = document.getElementById(id);
    var ctx = canvas.getContext('2d');
    ctx.canvas.width = 300;
    ctx.canvas.height = 200;
    
    // Global Options:
    Chart.defaults.global.defaultFontColor = 'black';
    Chart.defaults.global.defaultFontSize = 16;

    var areaColor = ["0,0,0", "0,255,0", "255,0,0", "0,0,255", "0,255,255", "255,0,255", "0,128,0", "128,0,128", "255,215,0", "0,191,255"]
    var scalesKey = Object.keys(scales);
    var tmpDatasets = new Array();
    for (var i = 0; i < names.length; i++){
        tmpDatasets[i] = {
            label: names[i],
            fill: false,
            lineTension: 0.1,
            backgroundColor: "rgba("+areaColor[i]+",1)",
            borderColor: "rgba("+areaColor[i]+",1)", // The main line color
            borderCapStyle: 'square',
            borderDash: [], // try [5, 15] for instance
            borderDashOffset: 0.0,
            borderJoinStyle: 'miter',
            borderWidth: 1,
            pointBorderColor: "rgba("+areaColor[i]+",1)",
            pointBackgroundColor: "rgba("+areaColor[i]+",1)",
            pointBorderWidth: 1,
            pointHoverRadius: 8,
            pointHoverBackgroundColor: "rgba("+areaColor[i]+",1)",
            pointHoverBorderColor: "rgba("+areaColor[i]+",1)",
            pointHoverBorderWidth: 2,
            pointRadius: 4,
            pointHitRadius: 10,
            // notice the gap in the data and the spanGaps: true
            data: scales[scalesKey[i]],
            spanGaps: true,
        }    
    }

    var data = {
        labels: instances,
        datasets: tmpDatasets
    };
    // Notice the scaleLabel at the same level as Ticks
    var options = {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true,
                    min: -2,
                    max: 4,
                    stepSize: 0.5,
                    fontSize: 12
                    
                },
                scaleLabel: {
                    display: true,
                    labelString: 'Ausprägung',
                    fontSize: 20
                }
                }],
            xAxes: [{
                ticks: {
                    fontSize: 12,
                    autoSkip: true,
                    maxTicksLimit: 20
                }
                }]
            },
        title: {
            display: true,
            text: title,
            position: 'top'
        }
    };
    // Chart declaration:
    var myBarChart = new Chart(ctx, {
        type: 'line',
        data: data,
        options: options
    });

}