window.onload = function()
{
    let input = document.querySelector("input");
    let preview = document.querySelector(".preview");

    input.style.opacity = 0;
    
    let fileTypes = ["application/octet-stream", "application/json", "application/xml", "text/plain"];

    function validFileType(file)
    {
        for (let i = 0; i < fileTypes.length; i++) {
            if (file.type === fileTypes[i]) {
                return true;
            }
        }
        return false;
    }

    function updateFileDisplay()
    {
        while (preview.firstChild) {
          preview.removeChild(preview.firstChild);
        }
      
        let curFiles = input.files;

        if (curFiles.length === 0) {
            let para = document.createElement("p");
            para.textContent = "Файл не выбран";
            preview.appendChild(para);
        } else {
            let list = document.createElement("ol");
            preview.appendChild(list);

            for (let i = 0; i < curFiles.length; i++) {
            let listItem = document.createElement("li");
            let para = document.createElement("p");
            if (validFileType(curFiles[i])) {
                para.textContent = curFiles[i].name;
                listItem.appendChild(para);
            } else {
                para.textContent = curFiles[i].name;
                listItem.appendChild(para);
            }

            list.appendChild(listItem);
            }
        }
    }

    input.addEventListener("change", updateFileDisplay);
}