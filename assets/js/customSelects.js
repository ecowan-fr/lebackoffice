const customSelects = () => {
    let selects = document.querySelectorAll('div.select');
    selects.forEach(element => {
        let affichage = element.children[0];
        let input = element.children[1];
        let btn = element.children[2];
        let dataSelect = element.children[3];
        if (input.value !== '') {
            let liEncours = document.querySelectorAll('div.select li[value=' + input.value + ']')[0]
            if (liEncours) {
                affichage.innerHTML = liEncours.children[0].children[0].children[0].innerHTML
                liEncours.children[0].children[0].children[1].innerHTML = '<i class="fa-solid fa-check"></i>'
            }
        }
        element.addEventListener('click', event => {
            event.stopPropagation()
            dataSelect.classList.toggle('hidden')
        })
        let lis = dataSelect.children
        for (let index = 0; index < lis.length; index++) {
            const li = lis[index];
            li.addEventListener('click', event2 => {
                event2.stopPropagation()
                for (let index2 = 0; index2 < lis.length; index2++) {
                    const li2 = lis[index2];
                    li2.children[0].children[0].children[1].innerHTML = '';
                }
                li.children[0].children[0].children[1].innerHTML = '<i class="fa-solid fa-check"></i>'
                input.attributes.value.value = li.attributes.value.value
                affichage.innerHTML = li.children[0].children[0].children[0].innerHTML
                dataSelect.classList.add('hidden')
            })
        }
    });
}

export default customSelects