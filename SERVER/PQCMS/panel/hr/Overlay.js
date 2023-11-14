export class Overlay 
{
    overlayDOM
    overlayIframe
    overlayPath
    hidden

    constructor() {
        this.overlayDOM = document.querySelector("#overlay")
        this.overlayIframe = this.overlayDOM.querySelector("iframe")
        this.overlayPath = null
        this.hidden = true

        this.overlayDOM.addEventListener("click", function(event) {
          if (event.target === this.overlayDOM)
          {
            this.hideOverlay()
          }
            // Kliknięto na overlay (nie na iframe)
          else
            console.log("iframe")
        }.bind(this));
    }

    /**
     * Zmienia ścieżkę w iframe w overlay'u (oraz pokazuje overlay, jeżeli był ukryty)
     * @param {string} path ścieżka 
     */
    showOverlay(path) {
        this.hidden = false
        this.overlayPath = path

        this.updateOverlayIframe()
    }
    
    /**
     * Chowa overlay oraz usuwa ścieżkę z iframe
     */
    hideOverlay() {
        this.hidden = true
        this.overlayPath = null

        this.updateOverlayIframe()
    }

    updateOverlayIframe() {
        if(this.overlayPath == "" || this.overlayPath == null)
        {
            this.overlayDOM.innerHTML = "";
            this.overlayDOM.style.animation = "hideOverlay .5s forwards";
        }
        else
        {
            this.overlayDOM.innerHTML = `<iframe src=\"${this.overlayPath}\"></iframe>`
            this.overlayDOM.style.animation = "showOverlay .5s forwards";
        }
    }

}