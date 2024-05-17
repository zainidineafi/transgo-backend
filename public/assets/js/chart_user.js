fetch("/user-registrations-data")
    .then((response) => response.json())
    .then((data) => {
        var ctx = document
            .getElementById("userRegistrationsChart")
            .getContext("2d");

        var dates = data.map((entry) => entry.date);
        var registrations = data.map((entry) => entry.registrations);

        var myChart = new Chart(ctx, {
            type: "bar",
            data: {
                labels: dates,
                datasets: [
                    {
                        label: "User Registrations",
                        data: registrations,
                        backgroundColor: "rgba(54, 162, 235, 0.2)",
                        borderColor: "rgba(54, 162, 235, 1)",
                        borderWidth: 1,
                    },
                ],
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                    },
                },
            },
        });
    })
    .catch((error) => {
        console.error("Error fetching user registrations data:", error);
    });
