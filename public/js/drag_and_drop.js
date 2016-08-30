(function() {
  var dropzone = document.getElementById('dropzone');

  var upload = function(files) {
    var formData = new FormData(),
    xhr =new XMLHttpRequest();

    formData.append('file', files[0]);
    xhr.open('post', '/');
    xhr.send(formData);
}

  dropzone.ondrop = function(e) {
      e.preventDefault();
      document.getElementById('droptext').innerHTML = "Файл загружен!";
      upload(e.dataTransfer.files);   
  };

  dropzone.ondragover = function() 
  {
    this.className = 'dropzone dragover';
    document.getElementById('droptext').innerHTML = "Отпустите, чтобы загрузить файл";
    return false;
  }

   dropzone.ondragleave = function() {
    this.className = 'dropzone';
    document.getElementById('droptext').innerHTML = "Нажмите и выберите файл!";
    return false;
  }

  dropzone.onclick = function(e) {
      document.getElementById("file").click();
      return false;
  };

  dropzone.onmouseover = function() 
  {
    this.className = 'dropzone dragover';
    document.getElementById('droptext').innerHTML = "Нажмите и выберите файл!";
    return false;
  }

  dropzone.onmouseleave = function() {
    this.className = 'dropzone';
    document.getElementById('droptext').innerHTML = "Перетащите файл сюда";
    return false;
  }

}());
