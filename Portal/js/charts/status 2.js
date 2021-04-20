//cutoff 1-4 und cutcol 1-4 sind jeweils Arrays, die für jede Skala den jeweiligen Wert enthalten, graphlabel beinhaltet die Namen der Skalen, also "Gesamt" etc. ...graphdata enthält den eigentlich einzuzeichnenden Wert, graphcol die Farbe des Wertes (beide derzeit noch nicht im Graphen enthalten)
function circle(context,centerX,centerY,radius) {
    context.beginPath();
    context.arc(centerX, centerY, radius, 0, 2 * Math.PI, false);
    context.fillStyle = 'red';
    context.fill();
    context.lineWidth = 5;
}

function createStatusGraph(id, graphdata, info, instance, index) {
    var canvas = document.getElementById(id);
    var ctx = canvas.getContext('2d');

    var tmp = Object.values(graphdata);
    var mean = [];
    tmp.forEach(function(element,i) {
        for(let [key,value] of Object.entries(element)){
            if(key === instance){
                mean.push(parseFloat(value[0]));
            }
        }
    });
    ctx.canvas.width = 100;
    ctx.canvas.height = 8 * mean.length + 5;

    var barData = [[],[],[],[]];
    var labels = [];
    var colors = [[],[],[],[]];
    Object.values(info).forEach(function(element){
        barData[0].push(parseFloat(element.low));
        barData[1].push(parseFloat(element.mid)-parseFloat(element.low));
        barData[2].push(parseFloat(element.high)-parseFloat(element.mid));
        barData[3].push(100-parseFloat(element.high));

        labels.push(element.name);

        colors[0].push('#cccccc');
        colors[1].push('#999999');
        colors[2].push('#666666');
        colors[3].push('#333333');
    });
    var data = {
        labels: labels,
        datasets: [
            {
                label: "keine/geringe Belastung",
                data: barData[0],
                backgroundColor: colors[0]
            },
            {
                label: "mittlere Belastung",
                data: barData[1],
                backgroundColor: colors[1]
            },
            {
                label: "schwere Belastung",
                data: barData[2],
                backgroundColor: colors[2]
            },
            {
                label: "starke Belastung",
                data: barData[3],
                backgroundColor: colors[3]
            }
        ]
    };

    var options = {
        tooltips: {
            mode: 'single',
            callbacks: {
                label: function (tooltipItem, data) {
                    return mean[tooltipItem.index];  
                }
            }
        },
        hover: {
            mode: null
        },
        scales: {
            xAxes: [{
                stacked: true,

            }],
            yAxes: [{
                stacked: true,
            }]
        },
        legend: {
            display: true,
            //onClick: null sorgt dafür, dass die Daten nicht "weggeklickt" werden können
            onClick: null
        },
        title: {
            display: true,
        }
    };

    var myHorizontalBar = new Chart(ctx, {
        type: 'horizontalBar',
        data: data,
        options: options,
        plugins: [{
            drawPoints: function (chartInstance) {
                //Zugriff auf den Chart
                var context = chartInstance.chart.ctx;
                //x-Achsen zum finden der Höhe der Skalen in Pixeln
                var xaxis = chartInstance.scales['x-axis-0'];
                //Farbe der Punkte auf rot, damit sichtbar
                ctx.fillStyle="#FF0000";
                /*Leeres Array zum Sammeln der y Werte in Pixeln && Iteration über y-Werte zum sammeln
                Index wird in Status.php beim Iterieren über $Questionnaires inkrementiert
                Beim Aufruf von createStatusGraph wird dem Objekt "myHorizontalBar.config.data.datasets[0]._meta"
                der entsprechende Eintrag an der letzten Stelle angefügt; alle anderen bleiben undefined.
                Heißt, dass das Objekt mit jeder Iteration um 1 wächst, aber nur das jeweils letzte Element
                definiert ist.
                */
                var yValues = [];
                for (i=0;i<chartInstance.config.data.datasets[0]._meta[index].data.length;i++) {
                    yValues.push(chartInstance.config.data.datasets[0]._meta[index].data[i]._model.y);
                }
                //Vergleich ob x,y Paare vollständig
                if (mean.length == yValues.length) {
                    //Iteration zum einzeichnen der Punkte mit Pixelwert für x und y
                    for (k=0;k<yValues.length;k++) {
                        circle(context,xaxis.getPixelForValue(mean[k]),yValues[k],5);
                    }
                }
            },
            //Einzeichnen der Punkte durch Aufruf der Funktion
            afterDatasetsDraw: function (chartInstance, easing) {
                this.drawPoints(chartInstance);
            }
        }]
    });
    //console.log(myHorizontalBar.config);
}