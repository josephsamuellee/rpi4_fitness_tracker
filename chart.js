function drawSevenDayChart(rows) {
    const labels = rows.map(r => r.day);
    const protein = rows.map(r => r.ate_protein);

    new Chart(document.getElementById('sevenDay'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Ate Protein',
                data: protein
            }]
        },
        options: { responsive: true }
    });
}

// quick heatmap style: 1 = green, 0 = red
function drawHeatmap(canvasId, rows) {
    const ctx = document.getElementById(canvasId).getContext('2d');

    const data = {
        labels: rows.map(r => r.day),
        datasets: [{
            label: 'Score',
            data: rows.map(r =>
                r.ate_protein +
                r.hit_veg +
                r.no_unplanned_snacks +
                r.workout_12min +
                r.ran +
                r.dinner_by_830
            )
        }]
    };

    new Chart(ctx, {
        type: 'bar',
        data,
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true, max: 6 } }
        }
    });
}

