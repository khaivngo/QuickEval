import axios from 'axios'

// const API_URL = process.env.API_URL || 'ajax/'

export default axios.create({
  // baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json'
    // 'Content-Type': 'application/x-www-form-urlencoded',
    // 'Accept': 'application/json',
    // 'Authorization': 'Bearer ' + localStorage.token
  }
})
