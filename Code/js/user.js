const logoutElements = document.querySelectorAll(".logout");
logoutElements.forEach(element => {
    element.addEventListener("click", async (e) => {
        e.preventDefault();
        
        const FD = new FormData();
        FD.append("logout", "1");

        console.log(element.href)

        const logoutFetch = await fetch(element.href.endsWith("/") ? element.href : element.href +"/", {
            method: "POST",
            body: FD
        });

        if(logoutFetch?.ok) {
            const responseData = await logoutFetch.text();
            console.log(responseData);
            window.location.href = element.href;
        }

        else {
            const responseData = await logoutFetch.json();
            console.error(responseData);
        }
    });
});

const forms = document.querySelectorAll("form");
forms.forEach(form => {
    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        if(typeof tinymce != "undefined") {
            tinymce.triggerSave();
        }

        else {
            console.log("Tinymce is not defined");
        }
        const FD = new FormData(form);
        
        const submitFetch = await fetch(form.action, {
            method: form.method,
            body: FD
        });

        const responseJson = await submitFetch.json();

        console.log(responseJson);

    });
});

const selectMultipleElements = document.querySelectorAll(".select-multiple");
selectMultipleElements.forEach(selectBox => {
    const triggerBox = selectBox.querySelector(".select-multiple-trigger");
    const optionsBox = selectBox.querySelector(".select-multiple-options");
    const options = optionsBox.querySelectorAll(".select-multiple-option");
    const inputsBox = selectBox.querySelector(".select-multiple-inputs");
    const selectedBox = selectBox.querySelector(".select-multiple-selected");

    function updateInputs() {
        while (inputsBox.firstChild) {
            inputsBox.removeChild(inputsBox.firstChild);
        }

        const selectedElements = selectBox.querySelectorAll(".selected-element");
        for (const selected of selectedElements) {
            const input = document.createElement("input");
            input.name = selectBox.dataset.name;
            input.type = "hidden";
            input.value = selected.dataset.value;
            inputsBox.appendChild(input);
        }
    }

    function hideOptions(e) {
        if(typeof e == "undefined") {
            optionsBox.classList.remove("active");
            return;
        }
        
        if(selectBox.contains(e.target) == false) {
            optionsBox.classList.remove("active");
            document.body.removeEventListener("click", hideOptions);
        }
    }

    triggerBox.addEventListener("click", (e) => {
        if(e.target == e.currentTarget) {
            if(optionsBox.classList.contains("active")) {
                optionsBox.classList.remove("active");
                document.body.removeEventListener("click", hideOptions);
            }

            else {
                optionsBox.classList.add("active");
                document.body.addEventListener("click", hideOptions);
            }
        }
    });

    for(const [index, optionElement] of options.entries()) {
        optionElement.addEventListener("click", () => {
            if(optionElement.classList.contains("selected-option")) {
                const selectedToRemove = selectedBox.querySelector(`[data-index="${index}"]`);
                selectedToRemove?.remove();
                optionElement.classList.remove("selected-option");
                updateInputs();
                return;
            }

            optionElement.classList.add("selected-option");

            const selectedElement = document.createElement("div");
            selectedElement.dataset.index = index;
            selectedElement.classList.add("selected-element");
            selectedElement.textContent = optionElement.textContent;
            selectedElement.dataset.value = optionElement.dataset.value;

            const removeButton = document.createElement("button");
            removeButton.classList.add("selected-element-remove");
            removeButton.type = "button";

            removeButton.addEventListener("mousedown", function(e) {
                hideOptions();
                e.preventDefault();
                selectedElement.remove();
                optionElement.classList.remove("selected-option");
                updateInputs();
            });

            selectedElement.appendChild(removeButton);
            selectedBox.appendChild(selectedElement);

            updateInputs();
        });
    }

});