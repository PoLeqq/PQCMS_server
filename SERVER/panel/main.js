window.addEventListener("load",() => {
    document.querySelector("#perm_license").checked = false;
});

document.querySelector("#perm_license").addEventListener("click", () => {
    let ed = document.querySelector("#expiry_date");

    if(ed.getAttribute("disabled") != null) ed.removeAttribute("disabled");
    else ed.setAttribute("disabled","");
});