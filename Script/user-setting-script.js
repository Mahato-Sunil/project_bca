document.addEventListener('DOMContentLoaded', () => {
    let user_bio = document.getElementById('bio');
    let msg = document.getElementById('bio_msg');
    // Set the maximum length of characters for the textarea
    user_bio.setAttribute('maxlength', '50');

    user_bio.addEventListener('input', (event) => {
        msg.style.color = 'green';
        let currentCount = event.target.value.length;
        msg.innerHTML = "Words: " + currentCount + "/50";

        // Alert the user when the maximum character count is reached
        if (currentCount === 50) {
            msg.innerHTML = "Limit Reached : 50/50";
            msg.style.color = 'red';
        }
    });

    // code for checking the valid password 
    const validPwd = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@.#$!%*?&^])[A-Za-z\d@.#$!%*?&]{8,25}$/;
    let pwd = document.getElementById('new_pwd');
    let confirm_pwd = document.getElementById('confirm_new_pwd');
    let btn = document.getElementById('pwd-change-btn');
    let pwdmsg = document.getElementById('pwdMsg');
    let isValid = false;
    let isEmpty = true;

    // check for input password 
    pwd.addEventListener('input', () => {
        if (pwd.value != "") {
            isEmpty = false;
            if (pwd.value.length < 8) {
                pwdmsg.innerHTML = "Password must be of (8-25) characters ";
                isValid = false;
            }
            else if (pwd.value.match(validPwd)) {
                pwdmsg.innerHTML = "";
                isValid = true;
            }
            else {
                pwdmsg.innerHTML = "Password should contain letters (upper & lowercase), numbers and symbols";
                isValid = false;
            }
        }
        else {
            pwdmsg.innerHTML = ""
            isValid = false;
            isEmpty = true;
        }
        //check for the final validation 
        updateButton();
    });

    // check for the confirmation message 
    confirm_pwd.addEventListener('input', () => {
        if (confirm_pwd.value != "") {
            isEmpty = false;
            if (pwd.value === confirm_pwd.value) {
                pwdmsg.innerHTML = "";
                isValid = true;
            }
            else {
                pwdmsg.innerHTML = "Password Not Matched";
                isValid = false;
            }
        } else {
            isValid = false;
            isEmpty = true;
            pwdmsg.innerHTML = "";
        }
        updateButton();
    });

    const updateButton = () => {
        if (isValid && !isEmpty) {
            btn.disabled = false;
            btn.style.background = "blue";
        } else {
            btn.disabled = true;
            btn.style.background = "gray";
        }
    }

    //overall check
    btn.addEventListener('click', () => {
        if ((pwd.value && confirm_pwd.value) != "") {
            if (pwd.value === confirm_pwd.value)
                pwdmsg.innerHTML = "";
            else
                pwdmsg.innerHTML = "Password Not Matched";
        }
        else
            alert("Fields can't be left Empty");
    });
});