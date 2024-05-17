document.addEventListener("DOMContentLoaded", function () {
    var chartContainer = document.getElementById("chartContainer");
    if (chartContainer) {
        var reservations = JSON.parse(
            chartContainer.getAttribute("data-reservations")
        );
        var ctx = document.getElementById("myChart").getContext("2d");
        var myChart = new Chart(ctx, getChartConfig([]));

        document
            .getElementById("timeRange")
            .addEventListener("change", function () {
                var selectedRange = this.value;
                var filteredData = filterDataByRange(
                    reservations,
                    selectedRange
                );
                updateChart(
                    myChart,
                    filteredData.labels,
                    filteredData.dataValues
                );
            });

        // Initial load
        var initialData = filterDataByRange(reservations, "all");
        updateChart(myChart, initialData.labels, initialData.dataValues);
    }

    function getChartConfig(data) {
        return {
            type: "bar",
            data: data,
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                    },
                },
            },
        };
    }

    function filterDataByRange(data, range) {
        var filteredLabels = [];
        var filteredDataValues = [];
        var currentDate = new Date();
        var pastDate = new Date();

        if (range !== "all") {
            range = parseInt(range);
            pastDate.setDate(currentDate.getDate() - range);
        }

        data.forEach(function (item) {
            var itemDate = new Date(item.date_departure);
            if (range === "all" || itemDate >= pastDate) {
                filteredLabels.push(item.date_departure);
                filteredDataValues.push(item.jumlah); // Adjust to your actual data structure
            }
        });

        return { labels: filteredLabels, dataValues: filteredDataValues };
    }

    function updateChart(chart, labels, dataValues) {
        chart.data.labels = labels;
        chart.data.datasets[0].data = dataValues;
        chart.update();
    }
});
