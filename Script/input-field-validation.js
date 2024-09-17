
document.addEventListener('DOMContentLoaded', (event) => {

    event.preventDefault();         //prevent the form from submitting

    let nameField = document.querySelectorAll('.nameField');        //class="nameField"
    let dateField = document.querySelector('input[type="date"]');   // selects date input fields 
    let ctznField = document.querySelector('#ctznField');           // id="ctznField" 
    let numField = document.querySelectorAll('input[type="tel"]');  // selects the phone number input fields 
    let emailField = document.querySelector('input[type="email"]');     // selects all the email fields
    let provinceField = document.querySelectorAll('.p_checkbox');       //selecting the checkbox 
    let districtField = document.querySelectorAll('.d_checkbox');       // selecting the districts 

    let submitBtn = document.getElementById('submitBtn');

    let isValidData = false;
    let isEmpty = true;

    //check the validity of the text fields or name fields  
    const isValidName = (event) => {
        let validNamePattern = /^[a-zA-Z(?:\s?)]+(?:\s[a-zA-Z]+)?$/;      //pattern for valid name 
        let msg = document.getElementById('nameMsg');
        if (event.target.value != "") {
            if (event.target.value.match(validNamePattern)) {
                isValidData = true;
                msg.innerHTML = "";
            } else {
                isValidData = false;
                msg.innerHTML = "Name can't contain any number or symbols.";
            }
            isEmpty = false;
        }
        else
            msg.innerHTML = "";
    }

    //check the validity of the number field 
    const isValidNumber = (event) => {
        let validPhonePattern = /^(\+\d{1,3})?[-.\s]?\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4}$/;      //valid pattern for the phone number 
        let msg = document.getElementById('pNumMsg');
        if (event.target.value != "") {
            isEmpty = false;
            if (event.target.value.match(validPhonePattern)) {
                isValidData = true;
                msg.innerHTML = "";
            }
            else {
                isValidData = false;
                msg.innerHTML = "Incorrect Phone Number !"
            }
        }
        else
            msg.innerHTML = "";
    }

    //check the validation of the date 
    const isValidDate = (event) => {
        let datePattern = /^(3[01]|[12][0-9]|0?[1-9])(\/|-)(1[0-2]|0?[1-9])\2([0-9]{2})?[0-9]{2}$/;
        let userDate = new Date(event.target.value);
        let currentDate = new Date();

        let latestDate = new Date();
        latestDate.setFullYear(today.getFullYear() - 13);
        let latestYear = latestDate.getFullYear();
        let latestMonth = latestDate.getMonth().toString().padStart(2, '0');
        let latestDay = latestDate.getDate().toString().padStart(2, '0');

        let maxDate = new Date(latestYear, latestMonth, latestDay);

        let oldestDate = new Date();
        oldestDate.setFullYear(today.getFullYear() - 100);
        let oldYear = oldestDate.getFullYear();
        let oldMonth = oldestDate.getMonth().toString().padStart(2, '0');
        let oldDay = oldestDate.getDate().toString().padStart(2, '0');

        let minDate = new Date(oldYear, oldMonth, oldDay);

        let userFormatDate = userDate.getDate().toString() + '-' + userDate.getMonth().toString() + '-' + userDate.getFullYear().toString();

        let msg = document.getElementById('dobMsg');

        if (event.target.value != "") {
            isEmpty = false;
            if (userFormatDate.match(datePattern)) {

                console.log("min date " + minDate);
                console.log("user date" + userDate);

                if (userDate >= minDate && userDate <= currentDate) {
                    if ((currentDate.getFullYear() - userDate.getFullYear()) >= 13) {
                        isValidData = true;
                        msg.innerHTML = "";
                    }
                    else {
                        isValidData = false;
                        msg.innerHTML = "User Must be Older than 13 years";
                    }
                }
                else {
                    isValidData = false;
                    msg.innerHTML = `Valid Range : ${minDate.toLocaleDateString()} - ${maxDate.toLocaleDateString()} `;

                    // let options = { year: 'numeric', month: '2-digit', day: '2-digit' };
                    // let formattedDate = today.toLocaleDateString('en-US', options);
                }
            }
            else {
                isValidData = false;
                msg.innerHTML = "Invalid Date Pattern";
            }
        }
        else
            msg.innerHTML = "";
    }

    //check the validaton of the citizenship 
    const isValidCtzn = (event) => {
        let validCtzn = /^(\d{1,2}-){3}\d{5}$/;
        let msg = document.getElementById('ctznMsg');
        if (event.target.value != "") {
            isEmpty = false;
            if (event.target.value.match(validCtzn)) {
                isValidData = true;
                msg.innerHTML = "";
            }
            else {
                isValidData = false;
                msg.innerHTML = "The Citizenship Format is incorrect. Correct Format : 00-00-00-00000";
            }
        }
        else
            msg.innerHTML = "";
    }

    // check for the email validation
    const isValidEmail = (event) => {
        let validEmail = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
        let msg = document.getElementById('emailMsg');
        if (event.target.value != "") {
            isEmpty = false;
            if (!event.target.value.match(validEmail)) {
                msg.innerHTML = "Please Enter Valid Email !";
                isValidData = false;
            }
            else {
                msg.innerHTML = "";
                isValidData = true;
            }
        }
        else
            msg.innerHTML = "";
    }

    // validation for the province 
    const isValidProvince = (event) => {
        let msg = document.querySelector('.province');
        if (event.target.value == "")
            event.target.value = "Select Province";

        if (event.target.value !== "" && event.target.value !== "Select Province") {
            isValidData = true;
            isEmpty = false;
            msg.innerHTML = "";
        }
        else {
            isValidData = false;
            msg.innerHTML = "Please Select The Province";
        }
    }

    // validation for the district 
    const isValidDistrict = (event) => {
        let msg = document.querySelector('.district');

        if (event.target.value == "")
            event.target.value = "Select District";

        if (event.target.value !== "" && event.target.value !== "Select District") {
            isValidData = true;
            isEmpty = false;
            msg.innerHTML = "";
        }
        else {
            isValidData = false;
            msg.innerHTML = "Please Select The District";
        }
    }


    // function to show the  same address if the permanet and the temporary address are same 
    document.getElementById('isSameAddress').addEventListener('change', function () {
        let form = document.getElementById('user-registration');
        if (this.checked) {
            const Province = document.getElementById('t-province').value;
            const District = document.getElementById('t-district').value;

            form.elements['p-district'].value = District;
            form.elements['p-province'].value = Province;
        } else {
            form.elements['p-district'].value = "Select District";
            form.elements['p-province'].value = "Select Province";
        }
    });

    //get the email from the users when the user enters the input  and set it as the username 
    document.getElementById('userEmail').addEventListener('input', () => {
        const username = document.getElementById('userEmail').value;
        const usernamePrnt = document.getElementById('username');
        usernamePrnt.value = username;
    });

    // adding the event listeners to the input fields 
    nameField.forEach((fields) => {
        fields.addEventListener('input', (event) => { isValidName(event) });
    });

    dateField.addEventListener('input', (event) => { isValidDate(event) });       //check for the date fields

    ctznField.addEventListener('input', (event) => { isValidCtzn(event) });        //check for the citizenship validation

    numField.forEach((fields) => {
        fields.addEventListener('input', (event) => { isValidNumber(event) });
    });

    emailField.addEventListener('input', (event) => { isValidEmail(event) });

    provinceField.forEach((fields) => {
        fields.addEventListener('input', (event) => { isValidProvince(event) });
    });

    districtField.forEach((fields) => {
        fields.addEventListener('input', (event) => { isValidDistrict(event) });
    });

    // validating the final input 
    // Function to update submit button state based on validity of data and emptiness of fields
    const updateSubmitButtonState = () => {
        if (isValidData && !isEmpty) {
            submitBtn.disabled = false;
        } else {
            submitBtn.disabled = true;
        }
    };

    // Adding event listeners to all input fields to update submit button state
    document.querySelectorAll('input, select').forEach(field => {
        field.addEventListener('input', () => {
            updateSubmitButtonState();
        });
    });
});