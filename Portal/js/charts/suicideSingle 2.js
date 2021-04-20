function circle(context,centerX,centerY,radius,color) {
    context.beginPath();
    context.arc(centerX, centerY, radius, 0, 2 * Math.PI, false);
    context.fillStyle = color;
    context.fill();
    context.lineWidth = 5;
}

//id = canvasID, value = einzuzeichnender Wert, desc = Label links, index = Graphindex, data = Array mit drei gleichen Einträgen, welche maxVal entsprechen, maxVal = höchster möglicher Wert, labels = Labels x-Achse, scaleHasZero = boolean; fängt die Fragebogenskala bei 0 oder bei 1 an, title = Fragebogenfrage
function createSingleSuicide (id,value,desc,index,data,maxVal,labels,scaleHasZero,title) {
    var canvas = document.getElementById(id);
    var ctx = canvas.getContext('2d');
    ctx.canvas.width = 30;
    ctx.canvas.height = 5;
    var gradient = ctx.createLinearGradient(0, 0, 0, 0);
    var xLabels = labels;
    if (scaleHasZero == false) {
        value = value-1;
    }
    
    var data = {
        labels: [desc],
        datasets: [
            {
                data: data,
                backgroundColor: gradient
            }
        ]
    };

    var options = {
        title: {
            display: true,
            text: title
        },
        tooltips: {
            enabled: false
        },
        hover: {
            mode: null
        },
        legend: {
            display: false
        },
        scales: {
            xAxes: [{
                ticks: {
                    maxRotation: 0,
                    beginAtZero: true,
                    max: maxVal,
                    callback: function (value) {
                        return xLabels[value];
                    }
                }
            }]
        }  
    };

    var myHorizontalBar = new Chart(ctx, {
        type: 'horizontalBar',
        data: data,
        options: options,
        plugins: [{
            //einzeichnen der Punkte
            drawPoints: function (chartInstance) {
                var context = chartInstance.chart.ctx;
                var xaxis = chartInstance.scales['x-axis-0'];
                ctx.fillStyle = '#000000';
                circle(context,xaxis.getPixelForValue(value),chartInstance.config.data.datasets[0]._meta[index].data[0]._model.y,5,'black');
            },
            afterDraw: function (chartInstance) {
                this.drawPoints(chartInstance);
            }
        }]
    });
    //einzeichnen des Gradienten
    var xAxis = myHorizontalBar.scales['x-axis-0'];
    var start = xAxis.getPixelForValue(maxVal);
    var ende = xAxis.getPixelForValue(0);
    var gradient = ctx.createLinearGradient(ende, 0, start, 0);
    gradient.addColorStop(0, '#3CA087');
    gradient.addColorStop(1, '#7B173B');
    myHorizontalBar.config.data.datasets[0].backgroundColor = gradient;
    myHorizontalBar.update();
    console.log(myHorizontalBar);
}