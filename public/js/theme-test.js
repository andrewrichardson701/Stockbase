const cssEditor = document.getElementById('css-editor');
const applyButton = document.getElementById('apply-button');
const style = document.getElementById('style');

document.addEventListener("load", (async () => {
    const text = await (await fetch(document.getElementById('theme-css').href)).text();
    cssEditor.innerHTML = text;
    })()
);

// Function to apply CSS
function applyCSS() {
    const style = document.getElementById('style');
    const cssText = cssEditor.value;
    style.innerHTML = cssText;
}

// Event listener for the "Apply CSS" button
applyButton.addEventListener('click', applyCSS);

function changeTheme() {
    var select = document.getElementById('theme-select');
    var value = select.value;
    var css = document.getElementById('theme-css');
    var profile_id = document.getElementById('profile-id').value;
    var theme = document.getElementById('theme-select-option-' + value).title;
    var theme_name = document.getElementById('theme-select-option-' + value).alt;
    // css.href = "./assets/css/theme-"+theme+".css";
    css.href = '/css/' + theme;

    const cssEditor = document.getElementById('css-editor');
    (async () => {
        const text = await (await fetch(css.href)).text();
        cssEditor.innerHTML = text;
    })();

    refreshCSS = () => {
        let links = document.getElementsByTagName('link');
        for (let i = 0; i < links.length; i++) {
            if (links[i].getAttribute('rel') == 'stylesheet') {
                if (links[i].id !== 'google-font') {
                    let href = links[i].getAttribute('href')
                        .split('?')[0];

                    let newHref = href + '?version='
                        + new Date().getMilliseconds();

                    links[i].setAttribute('href', newHref);
                }
            }
        }
    }

    refreshCSS();

}


// Function to download CSS content as a file
function downloadCSS() {
    const cssEditor = document.getElementById('css-editor');
    const cssText = cssEditor.value;
    const fileName = document.getElementById('download-theme-name').value !== '' ? document.getElementById('download-theme-name').value : 'new-theme';
    
    // Create a Blob containing the CSS content
    const blob = new Blob([cssText], { type: 'text/css' });
    
    // Create a temporary anchor element to trigger the download
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = fileName+'.css'; // Set the filename
    
    // Trigger the click event to initiate the download
    a.click();
    
    // Clean up
    URL.revokeObjectURL(a.href);
}

// color-picker box json
$("input.color").each(function() {
    var that = this;
    $(this).parent().prepend($("<i class='fa fa-paint-brush color-icon'></i>").click(function() {
        that.type = (that.type == "color") ? "text" : "color";
    }));
}).change(function() {
    $(this).attr("data-value", this.value);
    this.type = "text";
});