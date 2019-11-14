export function removeArrayItem (arr, forEach) {
  var i = arr.length
  while (i--) {
    if (forEach(arr[i])) {
      arr.splice(i, 1)
    }
  }
  return arr
}

/**
 * input:   2019-11-04 13:51:31
 * output:  04. nov. 2019, at 13:51
 */
export function formatDate (dateTime) {
  var dateTimeSplit = dateTime.split(' ')
  var date = dateTimeSplit[0].split('-')
  var time = dateTimeSplit[1].split(':')

  const months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sept', 'oct', 'nov', 'dec']

  return `${date[2]}. ${months[date[1] - 1]}. ${date[0]}, at ${time[0]}:${time[1]}`
}
