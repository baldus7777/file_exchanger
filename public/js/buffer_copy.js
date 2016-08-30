(function() {
  var link = document.getElementById('link');
  var btn = document.getElementById('buffer-btn');
  var range = document.createRange();

  window.getSelection().addRange(range);

  btn.onclick = function() {
    range.selectNode(link);
    window.getSelection().addRange(range);
    document.execComand('copy');
  };
}());
