document.addEventListener("DOMContentLoaded", function () {
    // About Section
    const aboutForm = document.querySelector("#userForm"); // Targeting the entire form since there's no specific section for "About" in your HTML
    aboutForm.addEventListener("submit", function (event) {
        event.preventDefault();

        // Get all form field values
        const firstName = document.getElementById("firstName").value;
        const lastName = document.getElementById("lastName").value;
        const address = document.getElementById("address").value;
        const city = document.getElementById("city").value;
        const state = document.getElementById("state").value;
        const zip = document.getElementById("zip").value;

        // For image file input
        const fileInput = document.getElementById("fileInput");
        const profileImageFile = fileInput.files[0];

        // Log About Section data (in this case, all form values)
        console.log("About Section:");
        console.log("First Name:", firstName);
        console.log("Last Name:", lastName);
        console.log("Address:", address);
        console.log("City:", city);
        console.log("State:", state);
        console.log("Zip:", zip);

        // Handle image file if provided
        if (profileImageFile) {
            console.log("Profile Image File Name:", profileImageFile.name);
        } else {
            console.log("No profile image uploaded");
        }

        // Log document files
        const resume = document.getElementById("resume").files[0];
        const btech = document.getElementById("btech").files[0];
        const ssc = document.getElementById("ssc").files[0];
        const hsc = document.getElementById("hsc").files[0];

        console.log("Documents:");
        if (resume) console.log("Resume:", resume.name);
        if (btech) console.log("B.Tech:", btech.name);
        if (ssc) console.log("SSC:", ssc.name);
        if (hsc) console.log("HSC:", hsc.name);

        // Log Social Media Links
        const linkedin = document.querySelector("#linkedin-label").value;
        const github = document.querySelector("#github-label").value;

        console.log("Links:");
        console.log("LinkedIn:", linkedin);
        console.log("GitHub:", github);
    });

    // Optionally add form validation check if needed
    const form = document.getElementById("userForm");
    form.addEventListener("submit", function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (form.checkValidity() === false) {
            form.classList.add("was-validated");
        }
    });
});
