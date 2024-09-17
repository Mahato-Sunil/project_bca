let title = document.getElementById('notice-title');
let msg = document.getElementById('notice-body');

let oldArray = new Array(); // creating a array to store the already arrived notification id 

//checking for the event listener object 
if (typeof (EventSource) !== "undefined") {
    // populate the notification section 
    let source = new EventSource('../PHP/admin-notification.php');
    source.onmessage = (event) => {
        /*server send the data on the following array : 
            noticeMsg  : actual message of the notification 
            noticeTime     : time stamp of the notification 
        */
        let msgFormat = JSON.parse(event.data);
        let noticeMsg = msgFormat['noticeMsg'];
        let noticeTime = msgFormat['noticeTime'];

        if (!oldArray.includes(noticeTime)) {
            const noticeBlock = `
            <!-- notification section  -->
            <div class="notification">
                <div id="notice-title" class="notify-title"> ${noticeMsg} </div>
                <div id="notice-body" class="notice">Time : ${noticeTime} </div>
            </div>`;

            document.getElementById('noticeContainer').innerHTML += noticeBlock;

            oldArray.push(noticeTime);
        }
    }

    // EventSource onerror event handler
    source.onerror = (event) => {
        console.error("EventSource failed:", event);
        source.close(); // Close the EventSource connection on error
    }
} else {
    // Throw the error message 
    console.log("Sorry, SSE is not working");
}


// 
// for notification 

const noticeContainer = document.querySelector("#noticeContainer");
const profile = document.querySelector('.profile');
const right = document.querySelector('.right');
const noticeBtn = document.querySelector(".mobile-notice").addEventListener('click', () => {
    if (noticeContainer.style.display === "block") {
        noticeContainer.style.display = "none";
        right.style.display = "none";
    } else {
        right.style.display = "block";
        profile.style.display = "none";  // Assuming you want to show 'right' when noticeContainer is shown
        noticeContainer.style.display = "block";
    }
}
);

