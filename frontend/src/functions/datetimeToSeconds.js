/**
 * Find the number of seconds between two JS DateTime objects.
 *
 * @return int
 */
export function datetimeToSeconds (startTime, endTime) {
  // subtract the current time with the start time (when images completed loading)
  let timeDiff = endTime - startTime // in ms
  // strip the ms and get seconds
  timeDiff /= 1000
  let seconds = Math.round(timeDiff)

  return seconds
}
