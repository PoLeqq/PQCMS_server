import { Overlay } from './Overlay.js';

let overlay = new Overlay();

let expandManagers = document.querySelectorAll('.ulArrow')
let pluses = document.querySelectorAll('.overlayLink')

// Funkcjonalność strzałek - Pokazanie listy
expandManagers.forEach(e => {
    e.addEventListener('click',function() {
        rotateArrow(e);
    })
    rotateArrow(e);  
})

pluses.forEach(e => {
    e.addEventListener('click',function() {
        overlay.showOverlay(e.getAttribute("overlayPath"))
    // rotateArrow(e);  
    })
})

function rotateArrow(element) {
    const li = element.parentElement.parentElement.parentElement

    if(element.getAttribute("rotate") == "" || element.getAttribute("rotate") == "false")
    {
        element.setAttribute("rotate",true)
        element.style.transformOrigin = "center center"
        element.style.transform = "rotate(180deg)"
        // element.parentElement.parentElement.parentElement.style.height = "auto";
        li.classList.add('open')
        const contentHeight = li.scrollHeight
        li.style.maxHeight = `${contentHeight}px`
    }
    else
    {
        element.setAttribute("rotate",false)
        element.style.transformOrigin = "center center"
        element.style.transform = "rotate(0)"
        // element.parentElement.parentElement.parentElement.style.height = "60px";
        
        li.classList.remove('open')
        const scrollY = li.querySelector(".ulText").offsetHeight+20 // +20 wynika ze styli - padding.
        // W sumie można kiedyś zmienić na autom., byłoby to o wiele praktyczniejsze
        li.style.maxHeight = `${scrollY}px`
    }
}

