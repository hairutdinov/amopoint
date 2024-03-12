function toggleInputFields(selectedType) {
    let inputFields = document.querySelectorAll('input');
    inputFields.forEach(inputField => {
        if (inputField.name.includes(selectedType)) {
            inputField.style.display = "block";
        } else {
            inputField.style.display = "none";
        }
    });
}

document.addEventListener("DOMContentLoaded", () => {
    let selectType = document.querySelector('select[name="type_val"]');
    toggleInputFields(selectType.value);
    selectType.addEventListener("change", () => {
        toggleInputFields(selectType.value);
    });
});