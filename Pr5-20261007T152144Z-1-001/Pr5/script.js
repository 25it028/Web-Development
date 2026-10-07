/* =========================================
   GET HTML ELEMENTS
   ========================================= */

const form =
    document.getElementById("registrationForm");

const passwordInput =
    document.getElementById("password");

const strengthBar =
    document.getElementById("strengthBar");

const strengthText =
    document.getElementById("strengthText");

const successMessage =
    document.getElementById("successMessage");


/* =========================================
   REGULAR EXPRESSIONS
   ========================================= */

// Name: letters and spaces, 3-50 characters
const namePattern =
    /^[A-Za-z ]{3,50}$/;


// Email
const emailPattern =
    /^[^\s@]+@[^\s@]+\.[^\s@]+$/;


// Indian mobile number
// Starts with 6-9 and contains 10 digits
const mobilePattern =
    /^[6-9]\d{9}$/;


// Password:
// Minimum 8 characters
// At least one lowercase
// At least one uppercase
// At least one number
// At least one special character
const passwordPattern =
    /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;


/* =========================================
   SHOW ERROR
   ========================================= */

function showError(id, message) {

    document.getElementById(id).textContent = message;

}


/* =========================================
   CLEAR ALL ERRORS
   ========================================= */

function clearErrors() {

    const errors =
        document.querySelectorAll(".error");

    errors.forEach(function(error) {

        error.textContent = "";

    });

}


/* =========================================
   PASSWORD STRENGTH
   ========================================= */

passwordInput.addEventListener(
    "input",
    function() {

        const password =
            passwordInput.value;

        let score = 0;


        // Minimum 8 characters
        if (password.length >= 8) {

            score++;

        }


        // Lowercase
        if (/[a-z]/.test(password)) {

            score++;

        }


        // Uppercase
        if (/[A-Z]/.test(password)) {

            score++;

        }


        // Number
        if (/\d/.test(password)) {

            score++;

        }


        // Special character
        if (/[@$!%*?&]/.test(password)) {

            score++;

        }


        // Empty password
        if (password.length === 0) {

            strengthBar.style.width = "0%";

            strengthText.textContent = "";

        }


        // Weak
        else if (score <= 2) {

            strengthBar.style.width = "33%";

            strengthText.textContent =
                "Password Strength: Weak";

        }


        // Medium
        else if (score <= 4) {

            strengthBar.style.width = "66%";

            strengthText.textContent =
                "Password Strength: Medium";

        }


        // Strong
        else {

            strengthBar.style.width = "100%";

            strengthText.textContent =
                "Password Strength: Strong";

        }

    }
);


/* =========================================
   FORM SUBMISSION
   ========================================= */

form.addEventListener(
    "submit",
    function(event) {

        // Stop default form submission
        event.preventDefault();


        // Clear previous errors
        clearErrors();


        // Hide previous success message
        successMessage.style.display = "none";

        successMessage.textContent = "";


        // Assume form is valid
        let isValid = true;


        /* =================================
           NAME VALIDATION
           ================================= */

        const name =
            document
                .getElementById("name")
                .value
                .trim();


        if (name === "") {

            showError(
                "nameError",
                "Full name is required."
            );

            isValid = false;

        }

        else if (!namePattern.test(name)) {

            showError(
                "nameError",
                "Enter a valid name using only letters and spaces."
            );

            isValid = false;

        }


        /* =================================
           EMAIL VALIDATION
           ================================= */

        const email =
            document
                .getElementById("email")
                .value
                .trim();


        if (email === "") {

            showError(
                "emailError",
                "Email address is required."
            );

            isValid = false;

        }

        else if (!emailPattern.test(email)) {

            showError(
                "emailError",
                "Enter a valid email address."
            );

            isValid = false;

        }


        /* =================================
           MOBILE VALIDATION
           ================================= */

        const mobile =
            document
                .getElementById("mobile")
                .value
                .trim();


        if (mobile === "") {

            showError(
                "mobileError",
                "Mobile number is required."
            );

            isValid = false;

        }

        else if (!mobilePattern.test(mobile)) {

            showError(
                "mobileError",
                "Enter a valid 10-digit mobile number."
            );

            isValid = false;

        }


        /* =================================
           PASSWORD VALIDATION
           ================================= */

        const password =
            document
                .getElementById("password")
                .value;


        if (password === "") {

            showError(
                "passwordError",
                "Password is required."
            );

            isValid = false;

        }

        else if (!passwordPattern.test(password)) {

            showError(
                "passwordError",
                "Password must contain 8+ characters, uppercase, lowercase, number and special character."
            );

            isValid = false;

        }


        /* =================================
           CONFIRM PASSWORD
           ================================= */

        const confirmPassword =
            document
                .getElementById("confirmPassword")
                .value;


        if (confirmPassword === "") {

            showError(
                "confirmPasswordError",
                "Please confirm your password."
            );

            isValid = false;

        }

        else if (password !== confirmPassword) {

            showError(
                "confirmPasswordError",
                "Passwords do not match."
            );

            isValid = false;

        }


        /* =================================
           COURSE VALIDATION
           ================================= */

        const course =
            document
                .getElementById("course")
                .value;


        if (course === "") {

            showError(
                "courseError",
                "Please select a course."
            );

            isValid = false;

        }


        /* =================================
           YEAR VALIDATION
           ================================= */

        const year =
            document
                .getElementById("year")
                .value;


        if (year === "") {

            showError(
                "yearError",
                "Please select your year."
            );

            isValid = false;

        }


        /* =================================
           GENDER VALIDATION
           ================================= */

        const gender =
            document.querySelector(
                'input[name="gender"]:checked'
            );


        if (!gender) {

            showError(
                "genderError",
                "Please select your gender."
            );

            isValid = false;

        }


        /* =================================
           TERMS VALIDATION
           ================================= */

        const terms =
            document
                .getElementById("terms")
                .checked;


        if (!terms) {

            showError(
                "termsError",
                "You must accept the Terms and Conditions."
            );

            isValid = false;

        }


        /* =================================
           FINAL RESULT
           ================================= */

        if (isValid) {

            successMessage.textContent =
                "✓ Registration successful!";

            successMessage.style.display = "block";


            // Reset form
            form.reset();


            // Reset password strength
            strengthBar.style.width = "0%";

            strengthText.textContent = "";

        }

    }
);