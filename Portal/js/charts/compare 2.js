
function createLineChart(id,title, means1, means2, info){
    var canvas = document.getElementById(id);

    var instances1 = Object.keys(means1);
    var meanValues1 = Object.values(means1);
    var instances = [];
    var values1 = [];
    var values2 = [];
    if(means2 != null){
        var instances2 = Object.keys(means2);
        var meanValues2 = Object.values(means2);
        var i = 0;
        var j = 0;
        while(i < instances1.length  && j < instances2.length){
            if(instances1[i] == instances2[j]){
                instances.push(instances1[i]);
                values1.push(Object.values(meanValues1[i++])[0]);
                values2.push(Object.values(meanValues2[j++])[0]);             
            } else {
                var timeI = Object.keys(meanValues1[i])[0];
                var timeJ = Object.keys(meanValues2[j])[0];
                if(timeI <= timeJ){
                    instances.push(instances1[i]);
                    values1.push(meanValues1[i++][timeI]);
                    values2.push(null);
                } else {
                    instances.push(instances2[j]);
                    values1.push(null);
                    values2.push(meanValues2[j++][timeI]);
                }
            }
        }
    } else {
        instances = instances1;
        for(var i = 0; i < meanValues1.length; i++){
            values1.push(Object.values(meanValues1[i])[0]);
        }
    }
    var ctx = canvas.getContext('2d');
    ctx.canvas.width = 300;
    ctx.canvas.height = 200;
    // Global Options:
    Chart.defaults.global.defaultFontColor = 'black';
    Chart.defaults.global.defaultFontSize = 16;

    var colors = ["0,159,227", "229,48,18", "60,160,135"];

    var data = {
        labels: instances,
        datasets: [
            {
                label: info[0].title,
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
                data: values1,
                spanGaps: false
            }
        ]
    }

    if(means2 != null) {
        data.datasets.push({
            label: info[1].title,
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
            pointRadius: 4,
            pointHitRadius: 10,
            // notice the gap in the data and the spanGaps: true
            data: values2,
            spanGaps: false
        });
    }

    var min = parseFloat(info[0].min);
    var max = parseFloat(info[0].max)
    var stepSize = (max - min) / 10;
    // Notice the scaleLabel at the same level as Ticks
    var options = {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:false,
                    min: min,
                    max: max,
                    stepSize: stepSize,
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
        tooltips: {
            mode: 'index',
            intersect: false,
        },
        // End Tooltips
        // annotation: {
        //     annotations: anno,
        // }
    };
    // Chart declaration:
    new Chart(ctx, {
        type: 'line',
        data: data,
        options: options
    });
}