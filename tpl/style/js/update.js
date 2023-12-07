function clearTextarea() {
  document.getElementById("updateText").value = "";
}

function pasteTextarea() {
  document.getElementById("updateText").value = copyToClipboard();
}

function copyToClipboard() {
  const exampleValue = document.getElementById("example").value;
  const tempTextarea = document.createElement("textarea");
  tempTextarea.value = exampleValue;
  document.body.appendChild(tempTextarea);
  tempTextarea.select();
  document.execCommand("copy");
  const copiedValue = tempTextarea.value;
  document.body.removeChild(tempTextarea);
  return copiedValue;
}
