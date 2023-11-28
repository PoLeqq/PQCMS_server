let showPasswordElement = document.querySelector("#showPassword");
let input = document.querySelector("#password");
let show = false;

showPasswordElement.addEventListener("click",()=> {
    show = !show;
    showPassword(show);
});
showPasswordElement.addEventListener('keypress',function(event) {
    if(event.keyCode === 13 || event.key === "Enter")
    {
        show = !show;
        showPassword(show);
    }
})

function showPassword(val)
{
    if(val) {
        showPasswordElement.classList.remove("showPassword");
        showPasswordElement.src = "../images/hidePassword.svg";
        input.type = "text";
    } else {
        showPasswordElement.classList.add("showPassword");
        showPasswordElement.src = "../images/showPassword.svg";
        input.type = "password";
    }
}