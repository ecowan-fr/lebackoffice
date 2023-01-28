export default window.checkAllCheckboxOfTable = (event) => {
    event.path[4].children[1].querySelectorAll("input[type='checkbox']").forEach(checkbox => {
        checkbox.checked = event.path[0].checked
    })
}