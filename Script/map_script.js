// wait for the content to be fully loaded 
document.addEventListener("DOMContentLoaded", () => {

    const dataRow = document.querySelectorAll('table tbody tr');
    dataRow.forEach(Row => {
        Row.addEventListener('click', (event) => {
            getUserData(event);
        });
    })
});

// function to retireve the contents of the table 
const getUserData = (event) => {
    let data = event.currentTarget;
    let lat = data.querySelector('.lat').textContent.trim();
    let lon = data.querySelector('.lon').textContent.trim();
    let time = data.querySelector('.time').textContent.trim();

    // redirecting to another page 
    let redirectPage = `../Map/user-map.php?lat=${lat}&lon=${lon}&time=${time}`;

    window.open(redirectPage, '_blank', 'noopener,noreferrer'); //sending the data to next page 
}
