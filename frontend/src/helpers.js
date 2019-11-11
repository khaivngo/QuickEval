export function removeArrayItem (arr, forEach) {
  var i = arr.length
  while (i--) {
    if (forEach(arr[i])) {
      arr.splice(i, 1)
    }
  }
  return arr
}
