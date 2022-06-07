function callEarningChart(earnings, expenses) {
    /*-------------------------------------
                  Line Chart
              -------------------------------------*/
    if ($("#earning-line-chart").length) {

        var lineChartData = {
            labels: ["Jan", "Feb", "March", "Apr", "May", "Jun", "July", "August", "Sept", "Oct", "Nov", "Dec"],
            datasets: [{
                data: earnings,
                backgroundColor: 'rgba(255,255,255,0)',
                borderColor: '#ff0000',
                borderWidth: 2,
                pointRadius: 0,
                pointBackgroundColor: '#ff0000',
                pointBorderColor: '#ffffff',
                pointHoverRadius: 6,
                pointHoverBorderWidth: 3,
                fill: 'origin',
                label: "Total Collection"
            },
                {
                    data: expenses,
                    backgroundColor: 'rgba(255,255,255,0)',
                    borderColor: '#417dfc',
                    borderWidth: 2,
                    pointRadius: 0,
                    pointBackgroundColor: '#304ffe',
                    pointBorderColor: '#ffffff',
                    pointHoverRadius: 6,
                    pointHoverBorderWidth: 3,
                    fill: 'origin',
                    label: "Total expenses"
                }
            ]
        };
        var lineChartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 2000
            },
            scales: {

                xAxes: [{
                    display: true,
                    ticks: {
                        display: true,
                        fontColor: "#222222",
                        fontSize: 16,
                        padding: 20
                    },
                    gridLines: {
                        display: true,
                        drawBorder: true,
                        color: '#cccccc',
                        borderDash: [5, 5]
                    }
                }],
                yAxes: [{
                    display: true,
                    ticks: {
                        display: true,
                        autoSkip: true,
                        maxRotation: 0,
                        fontColor: "#646464",
                        fontSize: 16,
                        stepSize: 10000,
                        padding: 20,
                        callback: function (value) {
                            var ranges = [{
                                divider: 1e6,
                                suffix: 'M'
                            },
                                {
                                    divider: 1e3,
                                    suffix: 'k'
                                }
                            ];

                            function formatNumber(n) {
                                for (var i = 0; i < ranges.length; i++) {
                                    if (n >= ranges[i].divider) {
                                        return (n / ranges[i].divider).toString() + ranges[i].suffix;
                                    }
                                }
                                return n;
                            }

                            return formatNumber(value);
                        }
                    },
                    gridLines: {
                        display: true,
                        drawBorder: false,
                        color: '#cccccc',
                        borderDash: [5, 5],
                        zeroLineBorderDash: [5, 5],
                    }
                }]
            },
            legend: {
                display: false
            },
            tooltips: {
                mode: 'index',
                intersect: false,
                enabled: true
            },
            elements: {
                line: {
                    tension: .35
                },
                point: {
                    pointStyle: 'circle'
                }
            }
        };
        var earningCanvas = $("#earning-line-chart").get(0).getContext("2d");
        var earningChart = new Chart(earningCanvas, {
            type: 'line',
            data: lineChartData,
            options: lineChartOptions
        });
    }
}
