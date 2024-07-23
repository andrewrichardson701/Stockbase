function toggleEditTag(id) {
    var row = document.getElementById('tag-'+id);
    var rowEdit = document.getElementById('tag-'+id+'-edit');
    if (rowEdit.hidden == true) {
        row.hidden=true;
        rowEdit.hidden=false;
    } else {
        row.hidden=false;
        rowEdit.hidden=true;
    }
}
function toggleHiddenTag(id) {
    var Row = document.getElementById('tag-'+id);
    var button = document.getElementById('tag-'+id+'-toggle');
    var buttonEdit = document.getElementById('tag-'+id+'-edit-toggle');
    var hiddenID = 'tag-'+id+'-stock';
    var hiddenRow = document.getElementById(hiddenID);
    if (hiddenRow.hidden == false) {
        hiddenRow.hidden=true;
        hiddenRow.classList.remove('theme-th-selected');
        Row.classList.remove('theme-th-selected');
        button.innerText='+';
        buttonEdit.innerText='+';
    } else {
        hiddenRow.hidden=false;
        hiddenRow.classList.add('theme-th-selected');
        Row.classList.add('theme-th-selected');
        button.innerText='-';
        buttonEdit.innerText='-';
    }
}