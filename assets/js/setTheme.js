import radioTesla from "./radioTesla";

export default window.setTheme = (theme = undefined) => {
    let themeSelected = 'light';
    let radioLight = document.querySelector('input#theme-light')
    let radioDark = document.querySelector('input#theme-dark')
    let circle = document.querySelector('#circle')
    let crescent = document.querySelector('#crescent')
    if (theme === 'dark' || theme == undefined && localStorage.theme == 'dark') {
        themeSelected = 'dark';
    }

    if (themeSelected === 'dark') {
        document.documentElement.classList.add('dark')
        if (radioDark) {
            radioDark.checked = true;
            radioTesla()
        }
        if (circle) {
            circle.style.background = "linear-gradient(40deg, #8983F7, #A3DAFB 70%)";
        }
        if (crescent) {
            crescent.style.transform = "scale(1)";
        }
    } else {
        document.documentElement.classList.remove('dark')
        if (radioLight) {
            radioLight.checked = true;
            radioTesla()
        }
        if (circle) {
            circle.style.background = "linear-gradient(40deg, #FF0080,#FF8C00 70%)";
        }
        if (crescent) {
            crescent.style.transform = "scale(0)";
        }
    }
    localStorage.setItem('theme', themeSelected)
}