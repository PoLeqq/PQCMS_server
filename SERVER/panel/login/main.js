let pass = document.querySelector("#password");

document.querySelector("#show_password").addEventListener("click", (event) => {
    if(event.target.checked) pass.type = "text";
    else pass.type = "password";
});