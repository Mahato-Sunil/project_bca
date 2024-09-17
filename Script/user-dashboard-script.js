document.addEventListener('DOMContentLoaded', () => {
    // side button navigation 
    const sidemenu = document.querySelector("aside");
    const menuBtn = document.querySelector("#menu-btn");
    const closeBtn = document.querySelector("#close-btn");

    menuBtn.addEventListener('click', () => {
        sidemenu.style.display = "block";
    });

    closeBtn.addEventListener('click', () => {
        sidemenu.style.display = "none";
    });

    document.getElementById('logout').addEventListener('click', () => {
        console.log("logout");
        window.location.replace("../PHP/logout-script.php");
        window.history.replaceState(null, null, "../");
        window.history.pushState(null, null, '../');
        window.addEventListener('popstate', function () {
            window.history.replaceState(null, null, '../');
        });
    });

    // javascript code for showing the location details of the users 
    // let modal = document.getElementById("myModal");
    // let btn = document.getElementById("location-btn");
    // let span = document.getElementsByClassName("close-btn")[0];

    // btn.addEventListener('click', () => {
    //     modal.style.display = "block";
    // });

    // span.addEventListener('click', () => {
    //     modal.style.display = "none";
    // });

    // window.addEventListener('click', (event) => {
    //     if (event.target == modal) {
    //         modal.style.display = "none";
    //     }
    // });


});
