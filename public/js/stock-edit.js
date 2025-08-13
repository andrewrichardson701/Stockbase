var selectBox = document.getElementById("tags-init");
var selectedBox = document.getElementById("tags");
var tagsSelected = document.getElementById("tags-selected");

selectBox.addEventListener("change", function() {
    var selectedOption = selectBox.options[selectBox.selectedIndex];
    selectedOption.innerHTML += " ✕";
    selectedOption.classList.add("clickable");
    if (selectedOption.value !== "") {
        selectedBox.add(selectedOption);
    }
});

selectedBox.addEventListener("change", function() {
    var removedOption = selectedBox.options[selectedBox.selectedIndex];
    var tempHTML = removedOption.innerHTML;
    var newHTML = tempHTML.replace(" ✕", "");
    removedOption.innerHTML = newHTML;
    removedOption.classList.remove("clickable");
    if (removedOption.value !== "") {
        selectBox.add(removedOption);
        selectedBox.remove(selectedBox.selectedIndex);
    }
});

selectedBox.addEventListener("change", function() {
    // Reset the selected values array
    selectedValues = [];

    // Iterate over the selected options and collect their values
    var selectedOptions = Array.from(selectedBox.options);
    selectedOptions.forEach(function(option) {
        selectedValues.push(option.value);
    });

    // Assign the selected values to the input field
    tagsSelected.value = selectedValues.join(", "); // Use desired separator if needed
});

selectBox.addEventListener("change", function() {
    // Reset the selected values array
    selectedValues = [];

    // Iterate over the selected options and collect their values
    var selectedOptions = Array.from(selectedBox.options);
    selectedOptions.forEach(function(option) {
        selectedValues.push(option.value);
    });

    // Assign the selected values to the input field
    tagsSelected.value = selectedValues.join(", "); // Use desired separator if needed
});

var loadImage = function(event) {
    var preview = document.getElementById('upload-img-pre');
    preview.src = URL.createObjectURL(event.target.files[0]);
    preview.onload = function() {
    URL.revokeObjectURL(preview.src) // free memory
    }
};

// MODAL SCRIPT bits
// Get the modal
function modalLoadSelection() {
    var modal = document.getElementById("modalDivSelection");
    // Get the image and insert it inside the modal - use its "alt" text as a caption
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal or if they click the image.
modalCloseSelection = function() { 
    var modal = document.getElementById("modalDivSelection");
    modal.style.display = "none";
}

function modalLoadCarousel() {
    var modal = document.getElementById("modalDivCarousel");
    // Get the image and insert it inside the modal - use its "alt" text as a caption
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal or if they click the image.
modalCloseCarousel = function() { 
    var modal = document.getElementById("modalDivCarousel");
    modal.style.display = "none";
}

// Populate the input box value
function modalImageInputFill(element) {
    var inputBoxVisible = document.getElementById('img-file-name-visible');
    var inputBox = document.getElementById('img-file-name');

    var imageThumb = document.getElementById('img-selected-thumb');

    inputBoxVisible.value = '/img/stock/'+element.alt;
    inputBox.value = element.alt;
    imageThumb.src = '/img/stock/'+element.alt;
    imageThumb.alt = element.alt;
}

function modalLoadUpload() {
    var modal = document.getElementById("modalDivUpload");
    // Get the image and insert it inside the modal - use its "alt" text as a caption
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal or if they click the image.
modalCloseUpload = function() { 
    var modal = document.getElementById("modalDivUpload");
    modal.style.display = "none";
}