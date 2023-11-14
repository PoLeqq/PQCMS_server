let showPass = document.getElementById("showPass");
let input = document.getElementById("password");
let show = false;

{
    showPass.addEventListener("click",()=>{
        show = !show;
        showPassword(show)
    });


}

function showPassword(val)
{
    if(val) {
        showPass.classList.remove("showPass");
        input.type = "text";
    } else {
        showPass.classList.add("showPass");
        input.type = "password";
    }

}