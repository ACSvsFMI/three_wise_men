function getHTMLOfSelection () {
  var range;
  if (document.selection && document.selection.createRange) {
    range = document.selection.createRange();
    return $(range.htmlText).text();
  }
  else if (window.getSelection) {
    var selection = window.getSelection();
    if (selection.rangeCount > 0) {
      range = selection.getRangeAt(0);
      var clonedSelection = range.cloneContents();
      var div = document.createElement('div');
      div.appendChild(clonedSelection);
      return $(div).text();
    }
    else {
      return '';
    }
  }
  else {
    return '';
  }
}

chrome.extension.onMessage.addListener(function(message, extension, callback) {
	$.get("http://localhost/hackathon/index.php", {
		'insert': getHTMLOfSelection()
	});
	alert("Done!");
	return true;
});

console.log(111);