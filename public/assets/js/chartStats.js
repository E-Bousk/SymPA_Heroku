window.onload = () => {
    const labelsFromTwig = JSON.parse(document.getElementById('dataCategoriesToJS').dataset.categoryName);
    const colorsFromTwig = JSON.parse(document.getElementById('dataCategoriesToJS').dataset.categoryColor);
    const countCategoryFromTwig = JSON.parse(document.getElementById('dataCategoriesToJS').dataset.categoryCount);

    let categories = document.querySelector('#categoriesChart');
    let categoriesGraph = new Chart(categories, {
        type: 'pie',
        data: {
            labels: labelsFromTwig,
            datasets: [{
                label: 'Répartition des catégories',
                backgroundColor: colorsFromTwig,
                borderColor: 'black',
                data: countCategoryFromTwig
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                  position: 'bottom',
                }
            }
        }
    });

    const datesFromTwig = JSON.parse(document.getElementById('dataOffersToJS').dataset.offersDate);
    const countFromTwig = JSON.parse(document.getElementById('dataOffersToJS').dataset.offersCount);

    let offers = document.querySelector('#offersChart');
    let offersGraph = new Chart(offers, {
        type: 'bar',
        data: {
            labels: datesFromTwig,
            datasets: [{
                label: 'Nombre d\'annonces',
                data: countFromTwig,
                backgroundColor: '#7081ff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            y: {
                ticks: {
                  stepSize: 1,
                }
            },
            plugins: {
                legend: {
                  position: 'bottom',
                }
            }
        }
    });
}
