// // Wait for the DOM to be fully loaded

// document.addEventListener('DOMContentLoaded', () => {
//   const pages = document.querySelectorAll('.page');
//   let currentPageIndex = 0;
//   let activePage;
//   let inptFields;

//   // Function to show a specific page
//   const showPage = (index) => {
//     pages.forEach((page, i) => {
//       page.classList.toggle('active', i === index);
//     });

//     // Update the active page variable and input fields based on the current active page
//     activePage = document.querySelector('.page.active');
//     inptFields = activePage.querySelectorAll('input[required]');
//   };

//   // Function to go to the next page
//   const goToNextPage = () => {
//     let isEmpty = false;
//     inptFields.forEach((inpt) => {
//       if (inpt.value.trim() === '') {
//         isEmpty = true;
//       }
//     });

//     if (!isEmpty) {
//       if (currentPageIndex < pages.length - 1) {
//         if (currentPageIndex == 1) {
//           let userInpt = document.querySelector('[name="email"]').value;
//           let validEmail = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
//           if (!userInpt.match(validEmail)) {
//             alert("Please Enter Valid Email !");
//             return;
//           }
//         }
//         currentPageIndex++;
//         showPage(currentPageIndex);
//       }
//     } else {
//       alert('Please Fill All Required Fields');
//     }
//   };

//   // Function to go to the previous page
//   const goToPrevPage = () => {
//     if (currentPageIndex > 0) {
//       currentPageIndex--;
//       showPage(currentPageIndex);
//     }
//   };

//   // Attach click event listeners to 'Next' buttons
//   document.querySelectorAll('.next-btn').forEach((button) => {
//     button.addEventListener('click', goToNextPage);
//   });

//   // Attach click event listeners to 'Prev' buttons
//   document.querySelectorAll('.prev-btn').forEach((button) => {
//     button.addEventListener('click', goToPrevPage);
//   });

//   // Attach click event listeners to 'correct' buttons
//   document.querySelector('#previewBtn').addEventListener('click', goToNextPage);

//   // Attach keypress event listener to prevent Enter keypress
//   document.addEventListener('keypress', (e) => {
//     if (e.key === 'Enter' && currentPageIndex < pages.length - 1) {
//       e.preventDefault();
//     }
//   });

//   Show the initial page
//   showPage(currentPageIndex);
// });