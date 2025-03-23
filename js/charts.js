

document.addEventListener("DOMContentLoaded", function () {


    const euro = new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 0
    });

    // Register the plugin to all charts:
    Chart.register(ChartDataLabels);
    var donneesAides = $DataAides$;
    var donneesAidesMontant = $DataAidesMontant$;
    var etiquettesAides = Object.keys(donneesAides);
    var etiquettesAidesMontant = Object.keys(donneesAidesMontant);
    var valeursAides = Object.values(donneesAides);
    var valeursAidesMontant = Object.values(donneesAidesMontant);
    const ctxAides = document.getElementById('aides');
    const ctxAidesMontant = document.getElementById('aidesMontant');
    var donneesToutes = $DataToutes$;
    var donneesToutesMontant = $DataToutesMontant$;
    var etiquettesToutes = Object.keys(donneesToutes);
    var etiquettesToutesMontant = Object.keys(donneesToutesMontant);
    var valeursToutes = Object.values(donneesToutes);
    var valeursToutesMontant = Object.values(donneesToutesMontant);
    const ctxToutes = document.getElementById('toutes');
    const ctxToutesMontant = document.getElementById('toutesMontant');

    console.log(donneesAides);
    console.log(donneesAidesMontant);
    console.log(donneesToutes);
    console.log(donneesToutesMontant);
    new Chart(ctxAides, {
        type: 'doughnut',
        data: {
            labels: etiquettesAides,
            datasets: [{
                label: ' Nombre ',
                data: valeursAides,
                borderWidth: 2,
                backgroundColor: ['#334A70', '#f28990', '#B1B1B1', '#99CCFF','#2E76FE','#910a13']
            }]
        },
        options: {
            plugins: {
                tooltip: {
                    enabled: false
                },
                datalabels: {
                    align: 'center',
                    color: 'white',
                    formatter: (value, context) => {
                        const datapoints = context.chart.data.datasets[0].data;
                        function totalSum(total, datapoint) {
                            return total + datapoint
                        }
                        const totalValue = datapoints.reduce(totalSum, 0);
                        const percentageValue = (value / totalValue * 100).toFixed(1);
                        const display = [` ${value}`, `${percentageValue} %`];
                        return display;
                    }
                }
            },
            pluggins: [ChartDataLabels]
        }
    });

    new Chart(ctxAidesMontant, {
        type: 'doughnut',
        data: {
            labels: etiquettesAidesMontant,
            datasets: [{
                label: ' Nombre ',
                data: valeursAidesMontant,
                borderWidth: 2,
                backgroundColor:  ['#334A70', '#f28990', '#B1B1B1', '#99CCFF','#2E76FE','#910a13']
            }]
        },
        options: {
            plugins: {
                tooltip: {
                    enabled: false
                },
                datalabels: {
                    align: 'center',
                    color: 'white',
                    formatter: (value, context) => {
                        const datapoints = context.chart.data.datasets[0].data;
                        function totalSum(total, datapoint) {
                            return total + datapoint
                        }
                        const totalValue = datapoints.reduce(totalSum, 0);
                        const percentageValue = (value / totalValue * 100).toFixed(1);
                        const display = [` ${euro.format(value)}`, `${percentageValue} %`];
                        return display;
                    }
                }
            },
            pluggins: [ChartDataLabels]
        }
    });


    new Chart(ctxToutes, {
        type: 'doughnut',
        data: {
            labels: etiquettesToutes,
            datasets: [{
                label: ' Nombre ',
                data: valeursToutes,
                borderWidth: 2,
                backgroundColor: ['#334A70', '#f28990', '#B1B1B1', '#99CCFF','#2E76FE','#910a13']
            }]
        },
        options: {
            plugins: {
                tooltip: {
                    enabled: false
                },
                datalabels: {
                    align: 'center',
                    color: 'white',
                    formatter: (value, context) => {
                        const datapoints = context.chart.data.datasets[0].data;
                        function totalSum(total, datapoint) {
                            return total + datapoint
                        }
                        const totalValue = datapoints.reduce(totalSum, 0);
                        const percentageValue = (value / totalValue * 100).toFixed(1);
                        const display = [` ${value}`, `${percentageValue} %`];
                        return display;
                    }
                }
            },
            pluggins: [ChartDataLabels]
        }
    });

    new Chart(ctxToutesMontant, {
        type: 'doughnut',
        data: {
            labels: etiquettesToutesMontant,
            datasets: [{
                label: ' Nombre ',
                data: valeursToutesMontant,
                borderWidth: 2,
                backgroundColor:  ['#334A70', '#f28990', '#B1B1B1', '#99CCFF','#2E76FE','#910a13']
            }]
        },
        options: {
            plugins: {
                tooltip: {
                    enabled: false
                },
                datalabels: {
                    align: 'center',
                    color: 'white',
                    formatter: (value, context) => {
                        const datapoints = context.chart.data.datasets[0].data;
                        function totalSum(total, datapoint) {
                            return total + datapoint
                        }
                        const totalValue = datapoints.reduce(totalSum, 0);
                        const percentageValue = (value / totalValue * 100).toFixed(1);
                        const display = [` ${euro.format(value)}`, `${percentageValue} %`];
                        return display;
                    }
                }
            },
            pluggins: [ChartDataLabels]
        }
    });
});